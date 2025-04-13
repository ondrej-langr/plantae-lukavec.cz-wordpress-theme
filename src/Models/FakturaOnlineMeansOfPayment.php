<?php

namespace App\Models;

enum FakturaOnlineMeansOfPayment : string {
    case CASH = 'cash';
    case ON_DELIVERY = 'cash_on_delivery';
    case BANK_TRANSFER = 'bank_transfer';
}
