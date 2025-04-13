<?php

namespace App\Models;

class FakturaOnlineLines {
    private $data = [];

    function __construct(array $lines = [])
    {
        $this->data = $lines;
    }

    function add(FakturaOnlineLine $line) {
        $this->data[] = $line;

        return $this;
    }

    function get() {
        return $this->data;
    }

    function asArray() {
        return array_values(array_map(fn (FakturaOnlineLine $item) => $item->asArray(), $this->data));
    }
}
