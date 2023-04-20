<?php

function getProductCategories($parentId = 0)
{
  $orderby = 'menu_order';
  $hide_empty = false;

  return get_terms('product_cat', array(
    'orderby'    => $orderby,
    'hide_empty' => $hide_empty,
    'exclude' => [15],
    'parent' => $parentId
  ));
}

function renderProductListItem($category)
{
  $result = '';
  $children = getProductCategories($category->term_id);
  $hasChildren = count($children) > 0;
  $activeTermId = get_queried_object()->term_id;
  $isActive = is_product_category($category->slug);
  $hasActiveChildren = false;
  $rootClassList = [
    'cat-item',
    "cat-item-$category->term_id"
  ];

  if ($hasChildren) {
    $rootClassList[] = 'cat-parent';
  }

  if ($isActive) {
    $rootClassList[] = 'current-cat';
  }

  $result .= '<li class="' . implode(" ", $rootClassList) . '">';
  $result .= '<a href="' . get_term_link($category) . '">';
  $result .= $category->name;
  $result .= '</a>';


  if ($hasChildren) {
    // Get all children of current category
    $categoryChildren = get_term_children($category->term_id, 'product_cat');

    // Check if activeTermId that user is on is in current step category id
    if (in_array($activeTermId, $categoryChildren)) {
      $hasActiveChildren = true;
    }

    if (($isActive || $hasActiveChildren)) {

      $result .= '<ul class="children">';

      foreach ($children as $categoryChildren) {
        $result .= renderProductListItem($categoryChildren);
      }

      $result .= '</ul>';
    }
  }

  $result .= '</li>';

  return $result;
}

function getProductCategoriesShortCode($atts)
{
  $a = shortcode_atts(array(
    'titulek' => 'Kategorie',
  ), $atts);

  $title = $a['titulek'];
  $product_categories = getProductCategories();

  $content  = '<div id="woocommerce_product_categories-2" class="widget woocommerce widget_product_categories">';
  $content .= '<h6 class="widget-title">' . $title . '</h6>';
  $content .= '<ul class="product-categories">';

  if (!empty($product_categories)) {
    foreach ($product_categories as $category) {
      $content .= renderProductListItem($category);
    }
  }

  $content .= "</ul>";
  $content .= "</div>";

  return $content;
}

add_shortcode('productCategoriesShortCode', 'getProductCategoriesShortCode');
