<?php

$themeRoot = get_template_directory_uri();

$assetManifestFile = json_decode(file_get_contents("$themeRoot/assets/build/.vite/manifest.json"));
$assetBuiltTime = file_get_contents("$themeRoot/assets/build/.vite/build-time");

add_action('wp_enqueue_scripts', function () use ($themeRoot, $assetManifestFile, $assetBuiltTime ) {
    $jsFilename = $assetManifestFile->{'index.ts'}->file;
    $cssFilename = $assetManifestFile->{'style.css'}->file;

    wp_enqueue_script(
        $jsFilename,
        "$themeRoot/assets/build/$jsFilename",
        [],
        $assetBuiltTime
    );

    wp_enqueue_style(
        $cssFilename,
        "$themeRoot/assets/build/$cssFilename",
        [],
        $assetBuiltTime
    );

    // Styles
    wp_enqueue_style('fontawesome-5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css',  false);
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Montserrat|Playfair+Display|Cinzel|Lora&display=swap',  false);
});
