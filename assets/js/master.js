;(function($){
  var $header;
  $body = jQuery("body");
  // inits start
  var image = document.getElementsByClassName('parallaxed_img');
  new simpleParallax(image, {
  	delay: .9,
  	transition: 'cubic-bezier(0,0,0,2)',
    scale: 1.12,
    maxTransition: 70

  });
  AOS.init();
  var bLazy = new Blazy({});

  // inits start
  $header_menu = jQuery('header[scope="site"]');
  if ($body.hasClass("front-page")) {
    $header = jQuery(".front-page .preview");
  } else if ($body.hasClass("post")) {
    $header = jQuery(".post-header .parallaxed");
  } else {
    $header = jQuery("#hinfo");
  }

  jQuery(window).scroll(function (event) {
    var scroll = jQuery(window).scrollTop();
      if (scroll > ($header.offset().top + $header.outerHeight(true) - 150)) {
        if ( ! $body.hasClass("header-on")) {
          $body.addClass("header-on")
          $header_menu.removeClass("fadeOut").addClass("animated").addClass("fadeIn");
        }
      } else {
        if ($body.hasClass("header-on")) {
          $header_menu.stop().removeClass("fadeIn").addClass("fadeOut");
          setTimeout(function () {
            $body.removeClass("header-on");
            $header_menu.removeClass("fadeOut");
            $header_menu.addClass("fadeIn");
          }, 200);
        }
      }

  });
  function trans_helper ($element, callback, speed) {
    $element.addClass("begin-trans");
    wait(speed);
  }
  const toggleMenu = (e) => {
    const body = document.querySelector("body");
    console.log(body);
    if (body.classList.contains("menu-open")) {
      body.classList.remove("menu-open")
    } else {
      body.classList.add("menu-open")
    }
  }

  document.querySelector(".mobileMenuButton").addEventListener("click", toggleMenu);
  document.querySelector(".mobileMenuButtonClose").addEventListener("click", toggleMenu);

  $( document.body ).on( 'added_to_cart', function(e){
    console.log(e);
    console.log('EVENT: added_to_cart');
  });

})(jQuery);
