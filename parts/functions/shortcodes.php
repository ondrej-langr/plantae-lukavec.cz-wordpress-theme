<?php
//newest posts shortcode
function newestpostzl()
{
  $recent_posts = wp_get_recent_posts(array(
    'numberposts' => 1,
    'post_status' => 'publish'
  ));
  $post = $recent_posts[0];
  $message = '<article class="special newestpost">';
  $message .= "<header>";
  $message .= '<img src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw== data-src="' . get_the_post_thumbnail_url($post['ID'], 'large') . '" class="b-lazy w-100 centerXY pos-abs">';
  $message .= '<div class="cleareset w-100 h-100" >';
  $message .= "<div data-aos='fade-up' data-aos-duration='1000'>";
  $message .= '<p class="inf">nejnovejší příspěvek</p>';
  $message .= "<h1 style='padding-top: 0px'><a href='" . get_permalink($post['ID']) . "'>" . $post['post_title'] . "</a></h1>";
  $message .= "<p class='excerpt'>";
  $message .= get_the_excerpt($post['ID']);
  $message .= "</p>";
  $message .= '</div>';
  $message .= "</div>";
  $message .= "</header>";
  $message .= "";
  $message .= "</article>";
  wp_reset_query();
  return $message;
}
//newest posts shortcode
add_shortcode('newestpost', 'newestpostzl');
//newest items (woocommerce)
function newestitemszl($params)
{
  $message = "";
  if (is_array($params)) {
    if (array_key_exists('quantity', $params)) {
      $message .= $params['quantity'];
    }
  }
  $args = array(
    'post_type'      => 'product',
    'posts_per_page' => 3,
  );

  $loop = new WP_Query($args);
  wp_reset_query();
  return $message;
}
//newest items (woocommerce)
add_shortcode('newestitems', 'newestitemszl');
