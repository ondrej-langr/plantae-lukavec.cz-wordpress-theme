<?php

add_action('woocommerce_before_shop_loop_item', function () {
  echo '<div class="kytimg h-100 pos-rel"><div class="shadow">';
}, 10, 0);

add_action('woocommerce_before_shop_loop_item_title', function () {
  echo "</div></div><div header>";
}, 10, 0);

add_action('woocommerce_after_shop_loop_item', function () {
  echo "</div>";
}, 10, 0);

add_filter('woocommerce_show_page_title', '__return_null');

add_theme_support('wc-product-gallery-lightbox');
function remove_woo_elements()
{
  remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
}
add_action('wp_head', 'remove_woo_elements');



add_filter('woocommerce_available_payment_gateways', 'bbloomer_gateway_disable_shipping_326');
function bbloomer_gateway_disable_shipping_326($available_gateways)
{
  if (WC()->session === null) return $available_gateways;
  $chosen_methods = WC()->session->get('chosen_shipping_methods');
  $chosen_shipping = $chosen_methods[0];

  if (0 === strpos($chosen_shipping, 'local_pickup:2')) {
    foreach ($available_gateways as $methodId => $key) {
      if ($methodId == 'alg_custom_gateway_1') {
        continue;
      }
      unset($available_gateways[$methodId]);
    }
  } else {
    unset($available_gateways['alg_custom_gateway_1']);
  }

  return $available_gateways;
}


//cod == charge on delivery
add_filter('woocommerce_package_rates', 'tarkan_adjust_shipping_rate', 20, 2);
function tarkan_adjust_shipping_rate($rates)
{

  if (WC()->session->get('chosen_payment_method') == 'cod') {
    foreach ($rates as $rate_id => $rate) {
      $cost = $rate->cost;
      // Cost is now 140 and 'cod' (platba dobírkou) is 185 so we have to do 185 - 140
      $additionalCost = 185 - $cost;
      $rates[$rate_id]->cost = $cost + $additionalCost;
    }
  }
  return $rates;
}

add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache');
function clear_wc_shipping_rates_cache()
{
  $packages = WC()->cart->get_shipping_packages();

  foreach ($packages as $key => $value) {
    $shipping_session = "shipping_for_package_$key";

    unset(WC()->session->$shipping_session);
  }
}


function disable_shipping_calc_on_cart($show_shipping)
{
  if (is_cart()) {
    return false;
  }
  return $show_shipping;
}
add_filter('woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99);


add_action('woocommerce_cart_collaterals', function () {
  echo '<a href="' . get_permalink(woocommerce_get_page_id('shop')) . '" style="padding: 13px 13px;margin-top: 15px; font-size: 15px; font-weight: 600;" class="checkout-button button alt wc-forward">Pokračovat v nákupu</a>';
});

add_filter('woocommerce_bacs_account_fields', 'custom_bacs_account_field', 10, 2);
function custom_bacs_account_field($account_fields, $order_id)
{
  $account_fields['var_symbol'] = array(
    'label' => "Variabilní symbol",
    'value' => $order_id ? $order_id : 0
  );
  return $account_fields;
}


function filter_process_payment_order_status_callback($status, $order)
{
  return 'on-hold';
}

// add the filter
add_filter('woocommerce_cod_process_payment_order_status', 'filter_process_payment_order_status_callback', 10, 2);
add_filter('woocommerce_bacs_process_payment_order_status', 'filter_process_payment_order_status_callback', 10, 2);

add_filter('woocommerce_gateway_title', 'change_payment_gateway_title', 25, 2);

function change_payment_gateway_title($titleWithPrice)
{
  $splitted = explode("(", $titleWithPrice);

  $titleWithoutPrice = trim($splitted[0]);
  $price = trim(str_replace(")", "", $splitted[1]));

  return is_admin() ? "$titleWithoutPrice ($price)" : "<span class='gateway-title'>$titleWithoutPrice</span> <span class='gateway-price'>$price</span>";
}


add_filter( 'woocommerce_get_order_item_totals', 'removePaymentMethodPriceFromEmail', 10, 3 );
function removePaymentMethodPriceFromEmail( $total_rows, $order, $tax_display ){
    // On Email notifications only
    if ( ! is_wc_endpoint_url() ) {
      [$title] = explode(" <span class='gateway-price'", $order->get_payment_method_title());

       $total_rows['payment_method']['value'] = $title;
    }
    return $total_rows;
}
