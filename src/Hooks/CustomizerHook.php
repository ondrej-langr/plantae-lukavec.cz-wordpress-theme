<?php

namespace App\Hooks;

use App\CustomizerFieldNames;
use App\Cutomizer\WP_Customize_Range;
use App\Hook;
use WP_Customize_Image_Control;
use WP_Customize_Manager;

class CustomizerHook extends Hook {
    function __construct()
    {
        $this->onWPHead(function () {

        $imagesRenderSide = get_theme_mod(CustomizerFieldNames::IMAGE_GALLERY_PLACEMENT->value, 'left');
        $isLeft = $imagesRenderSide != 'right';

        $cssVariables = [
            ['--product-image-margin-side-left', $isLeft ? '133px' : '0px'],
            ['--product-image-margin-side-right', $isLeft ? '0px' : '133px'],
            ['--product-gallery-side-leftt', $isLeft ? '0px' : 'unset'],
            ['--product-gallery-side-right', $isLeft ? 'unset' : '0px'],
        ];

           echo "
            <style type=\"text/css\">
                :root {
                    " . implode("\n", array_map(function ($item) {
                        [$key, $value] = $item;

                        return "$key: $value;";
                    }, $cssVariables)) . "
                }
            </style>
           ";
        });

        $this->onCustomizerPreviewInit(
            function () {
                  // live prev enabling
               	wp_enqueue_script(
               		  'cd_customizer',
               		  get_template_directory_uri() . '/assets/js/customizer.js',
               		  array( 'jquery','customize-preview' ),
               		  '',
               		  true
               	);
            }
        );

        $this->onCustomizerRegister(function (WP_Customize_Manager $wp_customize) {
          $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
          $wp_customize->add_control( new WP_Customize_Range( $wp_customize, 'translate_y_percentage', array(
          	'label'	=>  'Pozice Y na obrázcích v hlavičkách',
              'min' => 0,
              'max' => 100,
              'step' => 1,
          	'section' => 'title_tagline',
          ) ) );

          $wp_customize->add_setting( 'translate_y_percentage' , array(
            'default'     => 0,
            'transport'   => 'postMessage',
          ) );

          // Header IMage
          $wp_customize->add_setting(CustomizerFieldNames::HEADER_IMAGE->value);
          $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, CustomizerFieldNames::HEADER_IMAGE->value,
            array(
            'label' => 'Nahrát obrázek do hlavičky',
            'section' => 'title_tagline',
            'settings' => CustomizerFieldNames::HEADER_IMAGE->value,
          ) ) );

           $wp_customize->add_panel( 'zl_settings', array(
              'title' => __( 'Nastavení zl' ),
              'description' => "nastavení", // Include html tags such as <p>.
              'priority' => 160, // Mixed with top-level-section hierarchy.
            ) );

            $wp_customize->add_section( 'zl_settings_product' , array(
              'title'    => __( 'Nastavení v produktu' ),
              'capability' => 'edit_theme_options',
              'panel' => "zl_settings",
              'priority' => 160
            ) );

           $wp_customize->add_control( CustomizerFieldNames::IMAGE_GALLERY_PLACEMENT->value, array(
              'label' => 'Nastavení zobrazení náhledu galerie',
              'section' => 'zl_settings_product',
              'settings' => CustomizerFieldNames::IMAGE_GALLERY_PLACEMENT->value,
              'type' => 'radio',
              'choices' => array(
                'left' => 'Levá strana',
                'right' => 'Pravá strana',
              ),
            ) );
        });
    }


}
