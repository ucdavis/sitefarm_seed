// Dialog window for the Feature Block

CKEDITOR.dialog.add('feature_block', function (editor) {
  'use strict';

  return {
    title: 'Feature Block Alignment',
    minWidth: 200,
    minHeight: 100,
    contents: [
      {
        id: 'info',
        elements: [
          {
            id: 'align',
            type: 'radio',
            label: 'Align',
            items: [
              [editor.lang.common.alignNone, 'none'],
              [editor.lang.common.alignLeft, 'left'],
              [editor.lang.common.alignRight, 'right']
            ],

            // When setting up this field, set its value to the "align" value from widget data.
            // Note: Align values used in the widget need to be the same as those defined in the "items" array above.
            setup: function (widget) {
              this.setValue(widget.data.align);
            },

            // When committing (saving) this field, set its value to the widget data.
            commit: function (widget) {
              widget.setData('align', this.getValue());
            }
          }
        ]
      }
    ]
  };
});
