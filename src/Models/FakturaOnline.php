<?php

namespace App\Models;

use DateInterval;
use DateTime;
use WC_Order;
use WC_Order_Refund;

class FakturaOnline {
    function __construct(
        readonly \DateTime $issued_on,
        readonly FakturaOnlineLines $lines_attributes,
        readonly FakturaOnlineBuyer $buyer_attributes,
        readonly \DateTime $due_on,
        readonly string $number,
        readonly FakturaOnlineMeansOfPayment $means_of_payment,
        readonly FakturaOnlineSeller $seller_attributes = new FakturaOnlineSeller(),
        readonly string $kind = 'invoice',
        readonly array $advance_deduction_amount = [],
        readonly \DateTime|null $tax_point_on = null,
        readonly string|null $already_paid_note = null,
        readonly string $constant_symbol = '',
        public string|null $registration_number = null,
        readonly string $payment_symbol = '',
        readonly string $issued_by = 'Michal Jon',
        readonly string $vat_calculation = "vat_inclusive",
        readonly string $currency = 'CZK',
        readonly string|null $vat_totals_currency = '',
        readonly bool $vat_totals_currency_conversion = false,
        readonly bool $paid = false,
        readonly \DateTime|null $paid_at = null,
        readonly string $rounding_type = 'none',
        readonly string $language = 'cs',
        readonly int|string $design = '5',
        readonly bool $show_qr_code = true,
        readonly string $note = '',
        readonly string $foot_note = '',
        readonly int $logo_id = 120250,
    )
    {
        if (!$this->registration_number) {
            $this->registration_number = $this->number;
        }
    }

    function asArray(): array {
        $items = get_object_vars($this);

        $items['due_on'] = $this->due_on->format('d. m. Y');
        $items['lines_attributes'] = $this->lines_attributes->asArray();
        $items['seller_attributes'] = $this->seller_attributes->asArray();
        $items['buyer_attributes'] = $this->buyer_attributes->asArray();
        $items['issued_on'] = $this->issued_on->format('d. m. Y');
        $items['issued_on_localized'] = $this->issued_on->format('d. m. Y');
        $items['means_of_payment'] = $this->means_of_payment->value;

        if ($items['tax_point_on']) {
            $items['tax_point_on_localized'] = $this->tax_point_on->format('d. m. Y');
            $items['tax_point_on'] = $this->tax_point_on->format('d. m. Y');
        }

        if ($items['paid_at']) {
            $items['paid_at'] = $this->paid_at->format('d. m. Y');
        } else {
            unset($items['paid_at']);
        }

        return $items;
    }


    static function nonempty(string|null $input): string|null {
            return !$input ? null : $input;
        }

    static function fromWpOrder(WC_Order|WC_Order_Refund $order) {
        $items = $order->get_items();

        $lines = new FakturaOnlineLines(
            array_map(
                fn($item) => new FakturaOnlineLine(
                    description: $item->get_name(),
                    price: $item->get_product()->get_price(),
                    quantity: $item->get_quantity(),
                ), $items
            )
        );

        if ($order->get_shipping_total() > 0) {
            $methodTitle = $order->get_shipping_method();

            if ($order->get_payment_method() === 'cod') {
                $methodTitle .= " (vč. dobírky)";
            }

            $lines->add(new FakturaOnlineLine(
                description: $methodTitle,
                price: $order->get_shipping_total(),
                quantity: 1,
            ));
        }

        // There would normally be cash payment but we need to create qrcode even for inperson money exchange
        $meansOfPayment = FakturaOnlineMeansOfPayment::BANK_TRANSFER;

        if ($order->get_payment_method() === 'bacs') {
            // $meansOfPayment = FakturaOnlineMeansOfPayment::BANK_TRANSFER;
        } else if ($order->get_payment_method() === 'cod') {
            $meansOfPayment = FakturaOnlineMeansOfPayment::ON_DELIVERY;
        }

        $buyerName = static::nonempty($order->get_billing_company())
            ?? static::nonempty($order->get_formatted_billing_full_name())
            ?? $order->get_formatted_shipping_address();

        $issuedOn = new \DateTime();
        $paidAt = null;

        if ($order->get_date_paid() && ($order->get_status() === 'completed' || $order->get_status() === 'processing')) {
            $issuedOn = $order->get_date_paid();
            $paidAt = $order->get_date_paid();
        }

        return new FakturaOnline(
            number: strval(date('y')) . strval($order->get_id()),
            due_on: (clone $issuedOn)->add(DateInterval::createFromDateString("7 days")),
            paid: !!$paidAt,
            lines_attributes: $lines,
            issued_on: $issuedOn,
            means_of_payment: $meansOfPayment,
            paid_at: $paidAt,
            // TODO: add IČO/Dič
            buyer_attributes: new FakturaOnlineBuyer(
                name: $buyerName,
                email: $order->get_billing_email(),
                address_attributes: new FakturaOnlineAddress(
                    street: static::nonempty($order->get_billing_address_1()) ?? $order->get_shipping_address_1(),
                    city: static::nonempty($order->get_billing_city())  ?? $order->get_shipping_city(),
                    postcode: static::nonempty($order->get_billing_postcode()) ?? $order->get_shipping_postcode(),
                    country_code: static::nonempty($order->get_billing_country()) ?? $order->get_shipping_country()
                )
            )
        );
    }
}
