<?php

namespace App\Models;

class FakturaOnlineLine {
    function __construct(
        readonly string $description,
        readonly int $price,
        readonly int $quantity,
        readonly string $unit_type = 'ks',
        readonly int $vat_rate = 0
    )
    {}

    function asArray() {
        return get_object_vars($this);
    }
}
