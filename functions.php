<?php
  //Connect every part of this file on init
  $dir = get_template_directory() . "/" . "parts" . "/" . "functions";
  foreach (scandir($dir) as $filename) {
    $path = $dir . '/' . $filename;
    if (is_file($path)) {
      require_once $path;
    }
  }
