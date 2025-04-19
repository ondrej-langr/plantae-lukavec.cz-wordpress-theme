<?php

namespace App\Hooks;

use App\FakturaOnlineClient;
use App\Hook;
use App\Models\FakturaOnline;
use WC_Email;
use WC_Order;
use WP;
use WP_Post;

class WoocommerceInvoices extends Hook {
    private FakturaOnlineClient $invoiceApiClient;

    function getPrintLink(string $orderId) {
        return home_url(add_query_arg(['action' => 'request_print_invoice', 'orderId' => $orderId], '/wp-admin/admin-ajax.php'));
    }

    function renderBoxContent($post) {
       	global $post_id;

		$orderId = ( $post instanceof WP_Post ) ? $post->ID : $post->get_id();
		$order    = wc_get_order( $orderId );
		$metaKey = 'fakturaonline_invoice_id';

		if ( ! $order ) {
			return;
		}

		$linkedInvoiceId = get_post_meta($orderId, $metaKey, true);
		$linkedInvoice = $linkedInvoiceId ? $this->invoiceApiClient->getInvoiceUnmapped($linkedInvoiceId) : null;

		// Make sure the meta is purged
		if ($linkedInvoiceId && !$linkedInvoice) {
		    $order->delete_meta_data($metaKey);
			$order->save_meta_data();
		}

		$sections = [];

        if ($linkedInvoice) {
            $sections[] = "
            <a
                href=\""  . esc_url( $this->getPrintLink($orderId) ) . "\"
                class=\"button button-primary button-add-site-icon button-hero\"
                data-invoice-id=\"$linkedInvoiceId\"
                target=\"_blank\"
            >
                Vytisknout fakturu
			</a>";

			$sections[] = "<hr />";

			$sections[] = "<div style=\"display:flex;justify-content:space-between;align-items:center;\">
    		     <a
                        href=\"https://www.fakturaonline.cz/vystavene-faktury/$linkedInvoiceId/edit\"
                        class=\"button button-small\"
                        target=\"__blank\"
                        data-invoice-id=\"$linkedInvoiceId\"
                        target=\"_blank\"
                    >
                    Upravit fakturu
                </a>
                <div>
    				<a href=\"#\" id=\"request_delete_invoice\" class=\"button-link-delete button-small\" data-order-id=\"$orderId\">Vymazat</a>
    			</div>
            </div>";
        } else {
            $form = "<p>Zatím žádná faktura pro tuto objednávku. <br/>Vytvořte ji přepnutím obejdnávky do stavu \"Zpracovává se\" a nebo kliknutím na tlačítko:</p>";
            $form .= "<div>";

            $form .= '<button id="request_new_invoice" type="button" class="button button-primary" data-order-id="'.$orderId.'">Vytvořit</button>';

            $form .= "</div>";

            $sections[] = $form;
        }


        echo "<ul class='submitbox' style='margin-bottom:-5px;'>".
            implode('', array_map(fn ($content) => "<li class='wide'>$content</li>", $sections))
        . "</ul>";
    }

    function __construct()
    {
        $this->invoiceApiClient = new FakturaOnlineClient();

        // Update invoice when order reaches processing status. At this status owner of eshop decided that everything is paid and prepared
        $this->onWooOrderStatusChanged(function (int $order_id,string  $old_status, string $new_status, WC_Order $order) {
            if ($new_status === 'processing') {
                 $invoiceId = get_post_meta($order->get_id(), 'fakturaonline_invoice_id', true);

                 if (!$invoiceId) {
                     $invoice = $this->invoiceApiClient->createInvoice(FakturaOnline::fromWpOrder($order));
                     $invoiceId = strval($invoice->invoice_id);

                     add_post_meta($order->get_id(), 'fakturaonline_invoice_id', $invoice->invoice_id, true);
                 }

                 $this->invoiceApiClient->updateInvoice($invoiceId, FakturaOnline::fromWpOrder($order));
            }

            $order->save_meta_data();
        });

        // Add attachment invoice to order for either payment on delivery or payment before delivery
        add_filter( 'woocommerce_email_attachments', function ( $array, $id, $object, $that ){
            if (isset($id) && $id === 'customer_processing_order' && $object instanceof WC_Order) {
                $isLocalPayment = $object->get_payment_method() === 'alg_custom_gateway_1';
                $order = $object;

                if (!$isLocalPayment) {
                    $invoicesTempDirectory = sys_get_temp_dir() . '/invoices';

                    // make sure that temp directory is working
                    if (!file_exists($invoicesTempDirectory)) {
                        mkdir($invoicesTempDirectory, 0777, true);
                    }

                    // Ensure the invoice is in the other system and attached to order
                    $invoiceId = get_post_meta($order->get_id(), 'fakturaonline_invoice_id', true);

                    if (!$invoiceId) {
                        $invoice = $this->invoiceApiClient->createInvoice(FakturaOnline::fromWpOrder($order));
                        $invoiceId = strval($invoice->invoice_id);

                        add_post_meta($order->get_id(), 'fakturaonline_invoice_id', $invoice->invoice_id, true);
                    } else {
                        $this->invoiceApiClient->updateInvoice($invoiceId, FakturaOnline::fromWpOrder($order));
                    }

                    $filename = "faktura-$invoiceId.pdf";
                    $filepath = "$invoicesTempDirectory/$filename";
                    file_put_contents($filepath, $this->invoiceApiClient->getPdfForInvoice($invoiceId));

                    $array[] = $filepath;
                }
            }

           	return $array;
        }, 10, 4 );

        // Delete payment notes on specific types of order states
        $this->onWooEmailBeforeOrderTable(function (WC_Order $order, bool $sent_to_admin, bool $plain_text, WC_Email $email ) {
            $payment_method = $order->get_payment_method();

            $isBankPayment = $payment_method === 'bacs';
            $ingorePaymentInstructionsForEmails = ['customer_completed_order', 'customer_refunded_order', 'customer_processing_order'];

            if ( $isBankPayment && in_array($email->id, $ingorePaymentInstructionsForEmails)) {
                $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

                remove_action( 'woocommerce_email_before_order_table', [ $available_gateways[$payment_method], 'email_instructions' ], 10 );
            }
        });

       $this->onAdminInit(function () {
           $this->onWooAddMetaBoxes(
               function () {
                   add_meta_box( 'woocommerce-fakturaonline-box', 'Tisk Faktury Uložené na fakturaonline.cz',  array( $this, 'renderBoxContent' ));
               }
           );

            // Returns invoice for printing
            $this->registerAuthenticatedAjaxHandler(
                'request_print_invoice',
                function () {
                    $orderId = $_GET['orderId'];

                    if (!is_numeric($orderId)) {
                        wp_die(
                            args: [
                                'response' => 400
                            ]
                        );
                    }

                    $order = wc_get_order( $orderId );

                    if (!$order) {
                        wp_die(
                            args: [
                                'response' => 404
                            ]
                        );
                    }

                    $invoiceId = get_post_meta($order->get_id(), 'fakturaonline_invoice_id', true);

                    if (!$invoiceId) {
                        throw new \Exception('Faktura zatím není vytvořena');
                    }

                    $filename = "faktura-$invoiceId.pdf";
                    header("Content-Type: application/pdf; name=\"$filename\"");
                    header("Content-Disposition: inline; filename=\"$filename\"");

                    $this->invoiceApiClient->updateInvoice($invoiceId, FakturaOnline::fromWpOrder($order));

                    echo $this->invoiceApiClient->getPdfForInvoice($invoiceId);

                    wp_die();
                }
            );

            // Delete invoice from store
            $this->registerAuthenticatedAjaxHandler(
                 'request_delete_invoice',
                 function () {
                     $orderId = $_POST['orderId'];

                     if (!is_numeric($orderId)) {
                         wp_die(
                             args: [
                                 'response' => 400
                             ]
                         );
                     }

                     $order = wc_get_order( $orderId );

                     if (!$order) {
                         wp_die(
                             args: [
                                 'response' => 404
                             ]
                         );
                     }

                     $invoiceId = get_post_meta($order->get_id(), 'fakturaonline_invoice_id', true);
                     $this->invoiceApiClient->deleteInvoice($invoiceId);

                     $order->delete_meta_data('fakturaonline_invoice_id');
                     $order->save_meta_data();

                     wp_die();
                 }
            );

            // Create new invoice from order
           $this->registerAuthenticatedAjaxHandler(
                'request_new_invoice',
                function () {
                    $orderId = $_POST['orderId'];

                    if (!is_numeric($orderId)) {
                        wp_die(
                            args: [
                                'response' => 400
                            ]
                        );
                    }

                    $order = wc_get_order( $orderId );

                    if (!$order) {
                        wp_die(
                            args: [
                                'response' => 404
                            ]
                        );
                    }

                    $faktura = FakturaOnline::fromWpOrder($order);
                    $invoice = $this->invoiceApiClient->createInvoice($faktura);

                    add_post_meta($order->get_id(), 'fakturaonline_invoice_id', $invoice->invoice_id, true);

                    wp_die();
                }
           );
       });
    }
}
