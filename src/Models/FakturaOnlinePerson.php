<?php

namespace App\Models;

class FakturaOnlinePerson {
    function __construct(
        readonly string $name,
        readonly FakturaOnlineAddress $address_attributes,
        readonly string|null $company_number = null,
        readonly string|null $tax_number = null,
        readonly string|null $phone = null,
        readonly string|null $email = null,
        readonly FakturaOnlineAccountDetails|null $bank_account_attributes = null
    )
    {}

    function asArray() {
        $items = get_object_vars($this);

        $items['address_attributes'] = $this->address_attributes->asArray();
        $items['bank_account_attributes'] = $this->bank_account_attributes ? $this->bank_account_attributes->asArray() : null;

        if ($items['bank_account_attributes'] === null) {
            unset($items['bank_account_attributes']);
        }

        if ($items['tax_number'] === null) {
            unset($items['tax_number']);
        }

        if ($items['phone'] === null) {
            unset($items['phone']);
        }

        if ($items['email'] === null) {
            unset($items['email']);
        }

        return $items;
    }
}
