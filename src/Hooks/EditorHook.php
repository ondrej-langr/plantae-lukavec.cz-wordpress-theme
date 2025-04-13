<?php

namespace App\Hooks;

use App\Hook;

class EditorHook extends Hook {
    function __construct()
    {
        parent::__construct();

        add_theme_support('editor-styles');

        $this->onAdminInit(function () {
             add_editor_style('assets/css/editor-style.css');
        });
    }

}
