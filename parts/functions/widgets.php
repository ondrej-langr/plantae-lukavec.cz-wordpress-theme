<?php
  function reg_sidebars() {
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
  }
  add_action( 'widgets_init', 'reg_sidebars' );
