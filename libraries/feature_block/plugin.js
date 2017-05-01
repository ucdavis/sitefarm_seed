(function ($) {
  'use strict';

  CKEDITOR.plugins.add('feature_block', {
    requires: 'widget',

    // Register the icon used for the toolbar button. It must be the same
    // as the name of the widget.
    icons: 'feature_block',
    hidpi: true,

    init: function (editor) {
      // Register the editing dialog.
      CKEDITOR.dialog.add('feature_block', this.path + 'dialogs/feature_block.js');

      // Add toolbar button for this plugin.
      editor.ui.addButton('feature_block', {
        label: 'Feature Box',
        command: 'feature_block',
        toolbar: 'insert,10',
        icon: this.path + 'icons/' + (CKEDITOR.env.hidpi ? 'hidpi/' : '') + 'feature_block.png'
      });

      // Register the widget.
      editor.widgets.add('feature_block', {
        // Create the HTML template
        template:
          '<aside class="wysiwyg-feature-block u-align--right u-width--half">' +
            '<h3 class="wysiwyg-feature-block__title">Title</h3>' +
            '<div class="wysiwyg-feature-block__body"><p>Content</p></div>' +
          '</aside>',

        editables: {
          title: {
            selector: '.wysiwyg-feature-block__title',
            allowedContent: 'span'
          },
          content: {
            selector: '.wysiwyg-feature-block__body'
          }
        },

        // Prevent the editor from removing these elements
        allowedContent: 'aside(!wysiwyg-feature-block, u-align--right, u-align--left, u-width--half); h3(!wysiwyg-feature-block__title); div(!wysiwyg-feature-block__body)',

        // The minimum required for this to work
        requiredContent: 'aside(wysiwyg-feature-block)',

        // Convert any div with the .wysiwyg-feature-block into this widget
        upcast: function (element) {
          return element.name === 'aside' && element.hasClass('wysiwyg-feature-block');
        },

        // Set the widget dialog window name. This enables the automatic widget-dialog binding.
        // This dialog window will be opened when creating a new widget or editing an existing one.
        dialog: 'feature_block',

        // When a widget is being initialized, we need to read the data ("align")
        // from DOM and set it by using the widget.setData() method.
        // More code which needs to be executed when DOM is available may go here.
        init: function () {
          if (this.element.hasClass('u-align--left')) {
            this.setData('align', 'left');
          }
          else if (this.element.hasClass('u-align--right')) {
            this.setData('align', 'right');
          }
          else {
            this.setData('align', 'none');
          }
        },

        // Listen on the widget#data event which is fired every time the widget data changes
        // and updates the widget's view.
        // Data may be changed by using the widget.setData() method
        data: function () {
          // Remove all align classes and set a new one if "align" widget data is set.
          this.element.removeClass('u-width--half');
          this.element.removeClass('u-align--left');
          this.element.removeClass('u-align--right');
          this.element.removeClass('u-align--center');
          if (this.data.align && this.data.align !== 'none') {
            this.element.addClass('u-width--half');
            this.element.addClass('u-align--' + this.data.align);
          }
        }

      });

    }

  });


})(jQuery);
