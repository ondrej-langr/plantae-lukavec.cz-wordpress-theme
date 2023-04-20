<?php
  add_action( 'after_setup_theme', function () {
    add_theme_support( 'woocommerce' );
  });
  register_nav_menus(array(
    'primary'=>'Primární menu - Header',
    'secondary' => 'Sekundarní menu - Footer'
  ));
