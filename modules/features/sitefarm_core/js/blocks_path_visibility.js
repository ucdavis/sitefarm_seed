(function ($, window) {
  'use strict';

  // Set a default path visibility when using the Place Block module on a page
  Drupal.behaviors.sitefarmCoreBlockPathVisibility = {
    attach: function () {
      var $path_box = $('textarea[data-drupal-selector="edit-visibility-request-path-pages"]');
      var paths = $path_box.text();
      var current_path = getParameterByName('destination');
      var placement = getParameterByName('block-place');

      // If this is a block placement, no paths set, and a page destination is set
      if (placement && paths.length == 0 && current_path) {
        // First check if this is the frontpage and set the <front> path
        if ($('body').hasClass('path-frontpage')) {
          $path_box.text('<front>');
        } else {
          // Add the current path to the path visibility
          $path_box.text(current_path);
        }
      }

    }
  };

  function getParameterByName(name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
    var results = regex.exec(url);
    if (!results) {
      return null;
    }
    if (!results[2]) {
      return '';
    }
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

})(jQuery, window); // end jquery enclosure
