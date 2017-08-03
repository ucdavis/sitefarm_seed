# Javascript Libraries

Several javascript libraries are included which provide enhancements to [CKEditor](http://ckeditor.com/)
and other UI elements. Below is a list of those libraries with information on
what purpose they serve within SiteFarm.

Each of the following libraries is located in SiteFarm Seed profile's `/libraries`.
Ultimately these will be cloned by Composer into a project's root `/libraries` on
install.

See [Adding Libraries](adding_libraries.md) for more information on adding in
new libraries into a sub-profile

## CKEditor Plugins

### Autosave
Autosave is an official CKEditor plugin [http://ckeditor.com/addon/autosave](http://ckeditor.com/addon/autosave).

It allows content in text areas such as the Body field to be saved automatically
to a browser's localStorage temporarily. If a page crashes or the user accidentally
navigates away they will be offered to reload data when returning to the page.

The custom module `ck_autosave` implements this plugin.

### Feature Block

The Feature Block (labeled "Feature Box" in the UI) is a custom CKEditor plugin
implemented by the `ck_feature_block` module. The output is basically a [title
with a content body](http://ucd-one-patternlab.s3-website-us-west-1.amazonaws.com/?p=molecules-feature-block).
This makes it useful for highlighting some content in the main body of text.

It also provides an option for alignment within the page.

Markup output:
```
<aside class="wysiwyg-feature-block u-align--right u-width--half">
  <h3 class="wysiwyg-feature-block__title">Title</h3>
  <div class="wysiwyg-feature-block__body">
    <p>Content</p>
  </div>
</aside>

```

### Media Link
The Media Link (labeled "Teaser Link Box" in the UI) is another custom CKEditor
plugin implemented by the custom `ck_media_link` module. The output is a standard
[media object](https://css-tricks.com/media-object-bunch-ways/) that shows an
[image to the left of a title and body](http://ucd-one-patternlab.s3-website-us-west-1.amazonaws.com/?p=molecules-media-link)

The markup within CKEditor is slightly differen than the final output
```html
<div class="media-link__wrapper" data-url="#">
  <div class="media-link__figure">
    <img src="http://placehold.it/135x135" alt="Thumbnail" data-entity-type="file" />
  </div>
  <div class="media-link__body">
    <h3 class="media-link__title">Title</h3>
    <div class="media-link__content"><p>Content</p></div>
  </div>
</div>
```
A Text filter then transforms this markup into the following
```html
<a href="#" class="media-link ">
  <div class="media-link__wrapper">
    <div class="media-link__figure">
      <img src="http://placehold.it/135x135" alt="Thumbnail" data-entity-type="file" />
    </div>
    <div class="media-link__body">
      <h3 class="media-link__title">Title</h3>
      <p class="media-link__content">Content</p>
    </div>
  </div>
</a>
```
The reason for needing this transformation is due to CKEditor not recognizing
block elements like a `div` inside of an `a` tag.

### Notification
Notification is an official CKEditor plugin [http://ckeditor.com/addon/notification](http://ckeditor.com/addon/notification)

This is simply a requirement of the Autosave plugin for showing messages.

### Wordcount
Wordcount is an official CKEditor plugin [http://ckeditor.com/addon/wordcount](http://ckeditor.com/addon/wordcount)
and is implemented by the Drupal contrib module [CKEditor Wordcount](https://www.drupal.org/project/ckwordcount)

This plugin shows the current word and character count in the editor's footer.

## Other libraries

### PhotoSwipe
[PhotoSwipe](http://photoswipe.com/) is one of the best responsive and mobile-friendly
image gallery lightbox solutions currently available. This library is implemented by the
Drupal contrib module [PhotoSwipe](https://www.drupal.org/project/photoswipe)

The [Photo Gallery](http://ucd-one-patternlab.s3-website-us-west-1.amazonaws.com/?p=templates-photo-gallery)
content type uses this to display images.

### Slick Slider
[Slick Slider](http://kenwheeler.github.io/slick/) is used for image [carousels
and slideshows](http://ucd-one-patternlab.s3-website-us-west-1.amazonaws.com/?p=molecules-slideshow-thumbnails).
Although there is a Drupal module available, due to it's size and
numerous dependencies we opted not to use it. Since all that was needed was a
simple block to show photo galleries in we created our own implementation in the
`sitefarm_photo_gallery` feature module.

A sub-profile is welcome to add in the entire [Slick Carosel](https://www.drupal.org/project/slick)
Drupal module for more functionality if preferred.
