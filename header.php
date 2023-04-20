<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php wp_title(); ?></title>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
    <?php wp_head(); ?>
  </head>
  <body class="<?php
    if (is_front_page()) {
      echo "front-page";
    } else {
      echo get_post_type();
      if (is_product()) {
        echo " product-page";
      }
    }
  ?> <?php
    echo (current_user_can( 'administrator' ) ? "admin" : "normal_user");
  ?>">
    <header scope="site">
      <div class="branding">
        <a href="/"><?php bloginfo('name'); ?></a>
      </div>
      <nav id="main-nav">
          <?php
          $cart = '<li class="special"><a href="' .
          wc_get_cart_url() .
          '" title="Váš košík obsahuje ' .
          sprintf (_n( '%d', '%d', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ) .
          ' položek">Košík <i class="fas fa-shopping-bag" aria-hidden item-count-sb="' .
          sprintf (_n( '%d', '%d', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ) .
          '"></i></a></li>';
          wp_nav_menu(array(
            'theme_location'=>'primary',
            'container' => false,
            'menu_class' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s' . $cart . '<button class="mobileMenuButtonClose"><i class="fas fa-times" aria-hidden></i></button></ul>'
          ));

          ?>
          <button class="mobileMenuButton">
            <i class="fas fa-bars" aria-hidden></i>
          </button>
      </nav>
    </header>
    <?php $ele_type = "section"; ?>
    <?php if (get_post_type() == "post"): ?>
        <article class="page_article">
        <?php $ele_type = "header" ?>
        <<?php echo $ele_type; ?> id="hinfo" class="pos-rel post-header">
        <div class="parallaxed pos-abs w-100">
          <div class="pos-abs w-100 h-100 clearset"></div>
          <img src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw== data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'large'); ?>" data-src-small="<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium_large'); ?>" class="b-lazy w-100 parallaxed_img">
        </div>
        <div class="pos-rel" style="z-index: 1;">
          <?php the_title('<h1>', '</h1>'); ?>
        </div>
        </<?php echo $ele_type; ?>>
    <?php elseif (is_front_page()): ?>
      <main scope="front-page">
        <section class="preview pos-rel">
          <img src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw== data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(), array(1921,1080)); ?>" class="b-lazy h-100 parallaxed_img">
          <div class="pos-abs w-100 h-100 cleareset"></div>
         <!--  <div class='icon-scroll mouse centerX'></div> -->
          <i class="fas fa-angle-down animated fadeInDown infinite mouse centerX pos-abs" aria-hidden></i>
          <h1 class="centerXY pos-abs" ><div data-aos="fade-up" data-aos-offset="100" data-aos-delay="200" data-aos-duration="1000"><?php bloginfo('name'); ?></div></h1>
        </section>
    <?php elseif (is_shop() or is_product_category()): ?>
      <<?php echo $ele_type; ?> id="hinfo" class="pos-rel">
        <div class="pos-abs w-100 h-100 background">
          <?php
            $src="";
            if (is_product_category()){
  	          $src = get_current_term_image_uri();
            } elseif (is_shop()){
              $src = rand_term_image();
            }
          ?>
          <img src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw== data-src="<?php echo $src; ?>" alt="" class="b-lazy w-100 parallaxed_img">
        </div>
        <div class="pos-abs w-100 h-100 clearset">

        </div>
        <div class="pos-rel" >
          <h1>Produkty</h1>
          <?php if (is_product_category()): ?>
            <p>Kategorie: <?php echo single_cat_title( '', false ); ?></p>
          <?php endif; ?>
        </div>
      </<?php echo $ele_type; ?>>
    <?php else: ?>
      <<?php echo $ele_type; ?> id="hinfo" class="pos-rel">
        <div class="pos-abs w-100 h-100 background">
          <img src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw== data-src="<?php echo get_theme_mod('header_image') ?>" alt="" class="b-lazy w-100 parallaxed_img">
        </div>
        <div class="pos-abs w-100 h-100 clearset">

        </div>
        <div class="pos-rel">
          <?php if (is_product()): ?>
              <h1>Produkt</h1>
          <?php else: ?>
            <?php the_title('<h1>', '</h1>'); ?>
          <?php endif; ?>
        </div>
      </<?php echo $ele_type; ?>>
    <?php endif; ?>
