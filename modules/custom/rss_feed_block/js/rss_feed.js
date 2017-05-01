(function ($) {

  // register
  Vue.component('rss-feed', {
    template: '#rss-feed-template',

    props: {
      blockId: {
        type: String,
        required: true
      },
      count: {
        type: Number,
        default: 0
      },
      text: {
        type: String,
        required: false
      },
      cutoff: {
        type: Number,
        default: 250,
        required: false
      },
      more: {
        type: Number,
        default: 0
      }
    },

    data: function () {
      return {
        data: '',
        items: [],
        totalItems: 0,
        countShowing: 0
      }
    },

    created: function () {
      var self = this;

      this.countShowing = this.count;

      $.post('/rss-feed-proxy', {
        id: this.blockId
      }, function (data) {
        self.data = data;
        self.parseData();
      });
    },

    methods: {
      parseData: function () {
        var self = this;

        var $item = $(this.data).find('item');

        this.items = [];
        this.totalItems = $item.length;

        $item.each(function (index) {
          if (self.countShowing <= index) {
            return false;
          }

          var $this = $(this);
          var item = {
            'title': $this.find('title').text(),
            'link': $this.find('link').text(),
            'description': self.truncateText($this.find('description').text())
          };
          self.items.push(item);
        });
      },

      truncateText: function (text) {
        var textLength = text.length;

        // Return nothing if a text property type is not passed in or there is
        // no text passed into the function
        if (!this.text || textLength < 1) {
          return false;
        }

        // Strip HTML
        text = this.stripHtml(text);

        if (this.text == 'snippet' && textLength > 160) {
          text = text.substring(0, 160) + "...";
        }

        if (this.text == 'paragraph') {
          var cutoff = this.cutoff || 500;

          if (textLength > cutoff) {
            while (typeof text[cutoff] != 'undefined' && text[cutoff] !== '.') {
              cutoff++;
            }
            text = text.slice(0, cutoff) + '...';
          }
        }

        if (this.text == 'full') {
          text = this.nl2br(text);
        }

        return text;
      },

      stripHtml: function (html) {
        var tmp = document.createElement('DIV');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
      },

      nl2br: function (text) {
        return text.replace(/(?:\r\n|\r|\n)/g, '<br />');
      },

      showMore: function () {
        var remaining = this.totalItems - this.countShowing;
        if (remaining < 4) {
          this.countShowing = this.totalItems;
        }
        else {
          this.countShowing = this.countShowing + 4;
        }

        this.parseData();
      }
    }
  });

  // create a root instance for each block
  $('.block-rss-feed-block').each(function () {
    var selector = '#' + $(this).attr('id') + ' .rss-feed__wrapper';

    new Vue({
      el: selector
    });
  });

})(jQuery);
