##ABOUT

Block Style Plugins is an API module that allows a module or theme to add style
options to to block configuration by creating a custom plugin. 

##DEPENDENCIES

- block
- block_content

##USAGE

### Setting up an image style in a module

A sample Block Style plugin can be found in 
`block_style_plugins/src/Plugin/BlockStyle/SampleBlockStyle.php`

Create a new plugin class extending the BlockStyleBase class.

Override the `BlockStyleBase::formAlter` method to extend the `$form` array with
your own custom style options using the 
[Form API](https://api.drupal.org/api/drupal/elements).

A `styles` fieldset is automatically provided that can be used to do some
automatic loading of values as classes onto the block attributes.

```
$form['styles']['sample_class'] = array(
  '#type' => 'textfield',
  '#title' => $this->t('Add a custom class to this block'),
  '#description' => $this->t('Do not add the "period" to the start of the class'),
  '#default_value' => $this->configuration['styles']['sample_class'],
);
```

### Adding to a theme

Standard class Annotations are not discoverable in a theme. Thus the need to use
a `themename.blockstyle.yml` file

```
sample_block_style:
  id: 'sample_block_style'
  label: 'Sample Block Style'
  class: '\Drupal\themename\Plugin\BlockStyle\SampleBlockStyle'
  exclude:
    - 'block_plugin_id'
  include:
    - 'block_content_type'
```

Then add a BlockStyle plugin class into 
`themename\Plugin\BlockStyle\SampleBlockStyle.php` which will extend the 
BlockStyleBase class

### Visibility rules for showing or style options per block

"include" and "exclude" attributes are available to only include certain blocks 
or to exclude certain blocks from accessing your custom block styles. 

Pass in a "block plugin id" or a custom block content "block content type"
bundle name into the "include" or "exclude" attributes.



