<?php
//Connect every part of this file on init
$dir = get_template_directory() . "/" . "parts" . "/" . "functions";
foreach (scandir($dir) as $filename) {
  $path = $dir . '/' . $filename;
  if (is_file($path)) {
    require_once $path;
  }
}
function example_serif_font_and_large_address()
{
?>
  <style>
    .order-colophon .colophon-imprint {
      font-size: 1.65em
    }

    .order-notes {
      margin: 0 !important;
    }
  </style>
<?php
}
add_action('wcdn_head', 'example_serif_font_and_large_address', 20);

function hide_jetpack_banner()
{
  echo '<style>.notice.wcs-nux__notice {display:none !important;}</style>';
}
add_action('admin_head', 'hide_jetpack_banner', 99999);
