<?php

namespace App;

use App\Hooks\AdminHook;
use App\Hooks\CustomizerHook;
use App\Hooks\EditorHook;
use App\Hooks\FrontAssetsHook;
use App\Hooks\WidgetsHook;
use App\Hooks\WoocommerceInvoices;
use Dotenv\Dotenv;
use Exception;

class Engine {
    static function run() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();

        if (!$_ENV['FAKTURA_ONLINE_PASSWORD'] || !$_ENV['FAKTURA_ONLINE_EMAIL']) {
            throw new Exception('Faktura online environment variables are not set');
        }

        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        add_action( 'after_setup_theme', fn() => add_theme_support( 'woocommerce' ));
        static::registerMenus();
        static::includeDeprecated();

        new AdminHook();
        new CustomizerHook();
        new EditorHook();
        new FrontAssetsHook();
        new WidgetsHook();
        new WoocommerceInvoices();
    }

    private static function registerMenus() {
        register_nav_menus(array(
            'primary'=>'Primární menu - Header',
            'secondary' => 'Sekundarní menu - Footer'
        ));
    }

    private static function includeDeprecated() {
        $dir = get_template_directory() . "/src/functions";

        foreach (scandir($dir) as $filename) {
          $path = $dir . '/' . $filename;
          if (is_file($path)) {
            require_once $path;
          }
        }
    }
}
