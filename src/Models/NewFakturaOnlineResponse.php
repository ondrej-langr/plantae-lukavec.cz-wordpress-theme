<?php

namespace App\Models;

class NewFakturaOnlineResponse {
    function __construct(
        readonly string $public_url,
        readonly int $invoice_id,
    ) {}

    static function fromResponse(array $response) {
        $publicUrl = $response['public_url'] ?? '';
        $invoiceId = $response['invoice_id'] ?? '';

        if (!$publicUrl || !$invoiceId) {
            throw new \Exception('Invalid response from fakturaonline.cz');
        }

        return new self(
            public_url: $publicUrl,
            invoice_id: $invoiceId
        );
    }
}
