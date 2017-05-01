(function ($) {
  'use strict';

  // Slideshow using Slick Slider
  Drupal.behaviors.slickSlideshow = {
    attach: function (context) {
      $('.slideshow').once().each(function (index) {
        var $this = $(this);
        var slideshowId = "slideshow-" + index;
        var sliderNav = $this.next('.slider-nav');
        var sliderNavId = "slider-nav-" + index;

        // Add a unique ID to the slideshow
        $this.attr('id', slideshowId);

        // Without thumbnail navigation
        if (sliderNav.length == 0) {
          $this.slick({
            slide: "#" + slideshowId + " .slideshow__item",
            lazyLoad: 'progressive',
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true
          });
        }
        // With Thumbnail navigation
        else {
          // Add a unique ID to the navigation
          sliderNav.attr('id', sliderNavId);

          $this.slick({
            slide: '#' + slideshowId + ' .slideshow__item',
            lazyLoad: 'progressive',
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            asNavFor: '#' + sliderNavId + '.slider-nav'
          });
          sliderNav.slick({
            slide: '#' + sliderNavId + ' .slideshow__item',
            lazyLoad: 'progressive',
            slidesToShow: 3,
            slidesToScroll: 1,
            asNavFor: '#' + slideshowId + '.slideshow',
            dots: false,
            centerMode: true,
            //centerPadding: '70px',
            focusOnSelect: true
          });
        }
      });

    }
  };

})(jQuery); // end jquery enclosure
