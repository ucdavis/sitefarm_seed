(function ($) {
  'use strict';

  CKEDITOR.plugins.add('media_link', {
    requires: 'widget',

    // Register the icon used for the toolbar button. It must be the same
    // as the name of the widget.
    icons: 'media_link',
    hidpi: true,

    init: function (editor) {
      // Register the editing dialog.
      CKEDITOR.dialog.add('media_link', this.path + 'dialogs/media_link.js');

      // Add our plugin-specific CSS to style the widget within CKEditor.
      //editor.addContentsCss(this.path + 'media-link.css');

      // Add toolbar button for this plugin.
      editor.ui.addButton('media_link', {
        label: 'Teaser Link Box',
        command: 'media_link',
        toolbar: 'insert,10',
        icon: this.path + 'icons/' + (CKEDITOR.env.hidpi ? 'hidpi/' : '') + 'media_link.png'
      });

      // Register the widget.
      editor.widgets.add('media_link', {
        // Create the HTML template
        template:
        '<div class="media-link__wrapper" data-url="#">' +
          '<div class="media-link__figure">' +
            '<img src="http://placehold.it/135x135" data-entity-type="file" data-entity-uuid="placeholder" />' +
          '</div>' +
          '<div class="media-link__body">' +
            '<h3 class="media-link__title">Title</h3>' +
            '<div class="media-link__content"><p>Content</p></div>' +
          '</div>' +
        '</div>',

        editables: {
          image: {
            selector: '.media-link__figure',
            allowedContent: 'img[!src, alt, height, width, data-entity-type, data-entity-uuid]'
          },
          title: {
            selector: '.media-link__title',
            allowedContent: 'span'
          },
          content: {
            selector: '.media-link__content',
            allowedContent: 'p br strong em'
          }
        },

        // Prevent the editor from removing these elements
        allowedContent: 'div(!media-link__wrapper)[!data-url]; div(!media-link__figure); div(!media-link__body); h3(!media-link__title); div(!media-link__content)',

        // The minimum required for this to work
        requiredContent: 'div(media-link__wrapper)',

        // Convert any 'a' tag with the .media-link into this widget
        upcast: function (element) {
          return element.name === 'div' && element.hasClass('media-link__wrapper');
        },

        // Set the widget dialog window name. This enables the automatic widget-dialog binding.
        // This dialog window will be opened when creating a new widget or editing an existing one.
        dialog: 'media_link',

        // When a widget is being initialized, we need to read the data ("align")
        // from DOM and set it by using the widget.setData() method.
        // More code which needs to be executed when DOM is available may go here.
        init: function () {
          // Get the URL from the HTML5 data attribute
          var link = this.element.data('url');
          if (link && link !== '#') {
            this.setData('linkURL', link);
          }
        },

        // Listen on the widget#data event which is fired every time the widget data changes
        // and updates the widget's view.
        // Data may be changed by using the widget.setData() method
        data: function () {
          this.element.data('url', this.data.linkURL);
        }

      });

    }

  });


})(jQuery);
