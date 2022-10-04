<?php
  add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'fontawesome-5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css',  false );
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Montserrat|Playfair+Display|Cinzel|Lora&display=swap',  false );
    wp_enqueue_style( 'aos', 'https://unpkg.com/aos@2.3.1/dist/aos.css',  false );
    wp_enqueue_style( 'animatecss', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css',  false );
    wp_enqueue_style( 'zl-theme', get_template_directory_uri() . '/assets/css/slave.css',  2 );
  });
  add_action( 'wp_enqueue_scripts', function () {
      wp_enqueue_script( 'simpleparalax', 'https://cdn.jsdelivr.net/npm/simple-parallax-js@5.2.0/dist/simpleParallax.min.js', false);
      wp_enqueue_script( 'aos', 'https://unpkg.com/aos@2.3.1/dist/aos.js', false);
      wp_enqueue_script( 'blazy', get_template_directory_uri() . '/assets/js/blazy.min.js', 5, true, true);
      wp_enqueue_script( 'zl-js', get_template_directory_uri() . '/assets/js/master.js', 5, true, true);
  });
