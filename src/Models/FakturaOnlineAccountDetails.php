<?php

namespace App\Models;

class FakturaOnlineAccountDetails {
    function __construct(
        readonly string $number,
        readonly bool $show_iban = false,
        readonly string|null $iban = null,
        readonly string|null $swift = null,
    )
    {}

    function asArray() {
        $items = get_object_vars($this);

        return $items;
    }
}
