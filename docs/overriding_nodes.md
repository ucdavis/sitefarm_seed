# Overriding Node configuration

Often the default node configuration of Sitefarm features such as the Article
content type will need to be overridden to meet the needs of a subprofile. This
configuration can go into a custom module or the subprofile's own
`config/install` directory.

# Examples

## Adding a Reference field to Photo Galleries or Persons

*As a Developer, I want to add an entity reference field on the Article content
type to the Photo Gallery and Person content types so that I can offer blocks
rendering related content.*

This will require first to make dependencies on the `sitefarm_photo_gallery`
and `sitefarm_person` content types. This means requiring these modules before
the `sitefarm_article` module in the subprofile's `.info` file.

Second, add config files for field instances and node form display. 

**examples:** `modules/features/sitefarm_article/override_examples/`

![Screenshot of file structure to example config files](images/reference-config-examples.png)

Sitefarm Seed already provides base fields for Entity References to core content
types. This means that by reusing these existing fields all that is needed is
config for field instances.

![Screenshot of reusing entity reference dropdown](images/reuse-entity-reference-fields.png)
