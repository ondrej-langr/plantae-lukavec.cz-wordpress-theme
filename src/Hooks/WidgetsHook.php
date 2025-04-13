<?php

namespace App\Hooks;

use App\Hook;

class WidgetsHook extends Hook {
    function __construct()
    {
        $this->onWidgetsInit(function () {
            register_sidebar(
              array(
                'id'            => 'shop-sidebar',
                'name'          => __( 'Sidebar obchodu' ),
                'description'   => __( 'Kategorie či nedávně hledané položky.' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h6 class="widget-title">',
                'after_title'   => '</h6>',
              )
            );
        });
    }
}
