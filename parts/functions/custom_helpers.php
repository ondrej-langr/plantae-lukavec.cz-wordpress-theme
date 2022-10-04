<?php
  function get_term_image($id) {
    $thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );
    $image = wp_get_attachment_url( $thumbnail_id );
    if ( $image ) {
      return $image;
    } else {
      return rand_term_image();
    }
  }
  function get_current_term_image_uri() {
    global $wp_query;
    $src = "";
    $cat = $wp_query->get_queried_object();
    $term_id = $cat->term_id;
    $ancestor = get_ancestors($cat->term_id, 'product_cat');
    if (count($ancestor) > 0) {
      $term_id = $ancestor[0];
    }
    $src = get_term_image($term_id);
    return $src;
  }
  function get_random_term_ids() {
    $categories = get_categories( array(
      'orderby' => 'name',
      'taxonomy' => 'product_cat',
      'parent'  => 0
    ) );
    shuffle ($categories);
    return $categories;
  }
  function rand_term_image() {
    $counter = 0;
    $dec = true;
    $rndms = get_random_term_ids();
    foreach ($rndms as $t) {
      $thumbnail_id = get_term_meta( $t->term_id, 'thumbnail_id', true );
      $image = wp_get_attachment_url( $thumbnail_id );
      if ($image) {
        return $image;
      }
    }

  }
  function get_current_term_desc() {
    global $wp_query;
    $cat = $wp_query->get_queried_object()->$term_id;
    $ancestor = get_ancestors($cat, 'product_cat');
    if (count($ancestor) > 0) {
      $cat = $ancestor[0];
    }
    if (term_description($cat)) {
      // code...
      $ret = '<div class="pinfo_bottom"><div class="term-description">';
      $ret .= term_description($cat);
      $ret .= '</div></div>';
      return $ret;
    } else {
      return "";
    }
  }
