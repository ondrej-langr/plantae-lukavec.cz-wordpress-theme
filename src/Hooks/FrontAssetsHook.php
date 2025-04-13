<?php

namespace App\Hooks;

use App\Hook;

class FrontAssetsHook extends Hook {
    function __construct()
    {
        $themeRootUri = get_template_directory_uri();
        $themeRoot = get_template_directory();

        $assetManifestFile = json_decode(file_get_contents("$themeRoot/assets/build/.vite/manifest.json"));
        $assetBuiltTime = file_get_contents("$themeRoot/assets/build/.vite/build-time");

        $this->onEnqueueAdminScripts(
            function () use ($themeRootUri, $assetManifestFile, $assetBuiltTime ) {
                $jsFilename = $assetManifestFile->{'admin/admin.ts'}->file;


                wp_enqueue_script(
                    $jsFilename,
                    "$themeRootUri/assets/build/$jsFilename",
                    ['jquery'],
                    $assetBuiltTime
                );

            }
        );

        $this->onEnqueueScripts(function () use ($themeRootUri, $assetManifestFile, $assetBuiltTime ) {
            $jsFilename = $assetManifestFile->{'global/global.ts'}->file;
            $cssFilename = $assetManifestFile->{'style.css'}->file;

            wp_enqueue_script(
                $jsFilename,
                "$themeRootUri/assets/build/$jsFilename",
                ['jquery'],
                $assetBuiltTime
            );

            wp_enqueue_style(
                $cssFilename,
                "$themeRootUri/assets/build/$cssFilename",
                [],
                $assetBuiltTime
            );

            // Styles
            wp_enqueue_style('fontawesome-5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css',  false);
            wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Montserrat|Playfair+Display|Cinzel|Lora&display=swap',  false);
        });
    }
}
