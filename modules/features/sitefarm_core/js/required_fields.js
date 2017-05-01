(function ($) {
  'use strict';

  // Prevent required field error notices to not flow under the admin toolbar
  Drupal.behaviors.sitefarmCoreRequiredFields = {
    attach: function () {
      var $inputs = $('input, select');

      for (var i = $inputs.length; i--;) {
        $inputs[i].addEventListener('invalid', function () {
          this.scrollIntoView(false);
        });
      }
    }
  };

})(jQuery); // end jquery enclosure
