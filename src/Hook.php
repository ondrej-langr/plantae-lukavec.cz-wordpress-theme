<?php

namespace App;

abstract class Hook {
    function __construct()
    {

    }

    function onWooEmailBeforeOrderTable(callable $callback, int|null $priority = null) {
         add_action( 'woocommerce_email_before_order_table', $callback, $priority, 4);
    }

    function onAdminInit(callable $callback, int|null $priority = null) {
         add_action( 'admin_init', $callback, $priority);
    }

    function onAdminHead(callable $callback, int|null $priority = null) {
         add_action( 'admin_head', $callback, $priority);
    }

    function onWPHead(callable $callback, int|null $priority = null) {
         add_action( 'wp_head', $callback, $priority);
    }

    function onWidgetsInit(callable $callback, int|null $priority = null) {
        add_action( 'widgets_init', $callback, $priority);
    }

    function onCustomizerPreviewInit(callable $callback, int|null $priority = null) {
         add_action( 'customize_preview_init',  $callback, $priority);
    }

    function onCustomizerRegister(callable $callback, int|null $priority = null) {
         add_action( 'customize_register',  $callback, $priority);
    }

    function onEnqueueScripts(callable $callback, int|null $priority = null) {
         add_action( 'wp_enqueue_scripts',  $callback, $priority);
    }

    function onEnqueueAdminScripts(callable $callback, int|null $priority = null) {
         add_action( 'admin_enqueue_scripts',  $callback, $priority);
    }

     function onWooAdminOrderActionsEnd(callable $callback, int|null $priority = 10) {
         add_action( 'woocommerce_admin_order_actions_end',  $callback, $priority);
     }

     function onWooOrderStatusChanged(callable $callback, int|null $priority = 10) {
           add_action( 'woocommerce_order_status_changed',  $callback, $priority, 4);
     }

     function onWooNewOrder(callable $callback, int|null $priority = 10) {
           add_action( 'woocommerce_new_order', $callback, $priority, 2);
     }

     function onWooAdminOrderActions(callable $callback, int|null $priority = 10) {
              add_action( 'woocommerce_admin_order_actions',  $callback, $priority);
          }
    function onWooAddMetaBoxes(callable $callback, int|null $priority = 10) {
        add_action( 'add_meta_boxes',$callback, $priority);
    }

    function registerAuthenticatedAjaxHandler(string $name, callable $callback, int|null $priority = 10) {
        add_action ("wp_ajax_$name",  $callback, $priority) ;
    }

    function registerUnauthenticatedAjaxHandler(string $name, callable $callback, int|null $priority = 10) {
        add_action ("wp_ajax_nopriv_$name",  $callback, $priority) ;
    }
}
