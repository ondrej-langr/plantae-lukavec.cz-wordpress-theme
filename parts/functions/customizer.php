<?php
  add_action( 'customize_register', 'cd_customizer_settings' );
  function cd_customizer_settings( $wp_customize ) {
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
    $wp_customize->add_setting('header_image');
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'header_image',
      array(
      'label' => 'Nahrát obrázek do hlavičky',
      'section' => 'title_tagline',
      'settings' => 'header_image',
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
     $wp_customize->add_control( 'zl_product_images_side', array(
        'label' => 'Nastavení zobrazení náhledu galerie',
        'section' => 'zl_settings_product',
        'settings' => 'zl_product_images_side',
        'type' => 'radio',
        'choices' => array(
          'left' => 'Levá strana',
          'right' => 'Pravá strana',
        ),
      ) );
  }
  // add to customization
  add_action( 'wp_head', 'cd_customizer_css');
  function cd_customizer_css()
  {
      ?>
           <style type="text/css">
               :root {
                 <?php if ((get_theme_mod('zl_product_images_side') == "left") or get_theme_mod('zl_product_images_side') != "right"): ?>
                    --product-image-margin-side-left: 133px;
                    --product-image-margin-side-right: 0px;
                    --product-gallery-side-left: 0px;
                    --product-gallery-side-right: unset;
                 <?php elseif (get_theme_mod('zl_product_images_side') == "right"): ?>
                     --product-image-margin-side-left: 0px;
                     --product-image-margin-side-right: 133px;
                     --product-gallery-side-left: unset;
                     --product-gallery-side-right: 0px;
                 <?php endif; ?>;
               }
           </style>
      <?php
  }
  // live prev enabling
  add_action( 'customize_preview_init', 'cd_customizer' );
  function cd_customizer() {
  	wp_enqueue_script(
  		  'cd_customizer',
  		  get_template_directory_uri() . '/assets/js/customizer.js',
  		  array( 'jquery','customize-preview' ),
  		  '',
  		  true
  	);
  }
  if( class_exists( 'WP_Customize_Control' ) ) {
  	class WP_Customize_Range extends WP_Customize_Control {
  		public $type = 'range';

          public function __construct( $manager, $id, $args = array() ) {
              parent::__construct( $manager, $id, $args );
              $defaults = array(
                  'min' => 0,
                  'max' => 100,
                  'step' => 1
              );
              $args = wp_parse_args( $args, $defaults );

              $this->min = $args['min'];
              $this->max = $args['max'];
              $this->step = $args['step'];
          }

  		public function render_content() {
  		?>
  		<label>
  			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
  			<input class='range-slider' min="<?php echo $this->min ?>" max="<?php echo $this->max ?>" step="<?php echo $this->step ?>" type='range' <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" oninput="jQuery(this).next('input').val( jQuery(this).val() )">
              <input onKeyUp="jQuery(this).prev('input').val( jQuery(this).val() )" type='text' value='<?php echo esc_attr( $this->value() ); ?>'>

  		</label>
  		<?php
  		}
  	}
  }
