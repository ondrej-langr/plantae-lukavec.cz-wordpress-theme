<?php

namespace App\Hooks;

use App\FakturaOnlineClient;
use App\Hook;
use App\Models\FakturaOnline;
use WC_Order;
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

       $this->onAdminInit(function () {
           $this->onWooAddMetaBoxes(
               function () {
                   add_meta_box( 'woocommerce-fakturaonline-box', 'Tisk Faktury Uložené na fakturaonline.cz',  array( $this, 'renderBoxContent' ));
               }
           );

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
