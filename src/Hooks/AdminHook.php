<?php

namespace App\Hooks;

use App\Hook;

class AdminHook extends Hook {

    function __construct()
    {
        $this->onAdminHead(function () {
            echo '<style>.notice.wcs-nux__notice {display:none !important;}</style>';
        }, 99999);
    }
}
