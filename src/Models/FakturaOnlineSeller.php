<?php

namespace App\Models;

class FakturaOnlineSeller extends FakturaOnlinePerson {
    function __construct()
    {
        parent::__construct(
            company_number: '69148007',
            name: 'Michal Jon',
            email: 'jon.m@seznam.cz',
            phone: '+420606651356',
            address_attributes: new FakturaOnlineAddress(
                city: 'Hořice v Podkrkonoší',
                street: 'Lukavec U Hořic',
                postcode: '508 01',
                country_code: 'CZ'
            ),
            bank_account_attributes: new FakturaOnlineAccountDetails(
                number: '8825027001/5500',
                show_iban: true,
                iban: 'CZ56 5500 0000 0088 2502 7001'
            )
        );
    }
}
