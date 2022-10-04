( function( $ ) {
  // live prev of blogname
  wp.customize( 'blogname', function( value ) {
    value.bind( function( newval ) {
      $( 'header[scope="site"] .branding a, main[scope="front-page"] .preview h1 > div' ).text( newval );
    } );
  } );
  wp.customize.section( 'zl_settings_product', function( section ) {
    section.expanded.bind( function( isExpanded ) {
      if ( isExpanded ) {
        wp.customize.previewer.previewUrl.set( '/zahradnictvilukavec/product/muchovnik-cusickeho-amelanchier-alnifolia-cusickii/' );
      }
    } );
  } );
} )( jQuery );
