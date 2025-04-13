<?php

namespace App\Models;

class FakturaOnlineAddress {
    function __construct(
        readonly string $street,
        readonly string $city,
        readonly string $postcode,
        readonly string $country_code
    )
    {
    }

    function asArray() {
        return get_object_vars($this);
    }
}
