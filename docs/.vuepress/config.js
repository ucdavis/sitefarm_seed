module.exports = {
  title: 'SiteFarm Seed',
  description: 'SiteFarm Seed is a base profile for Drupal 8 meant for extending by a custom built sub-profile.\n',
  themeConfig: {
    lastUpdated: 'Last Updated',
    repo: 'ucdavis/sitefarm_seed',
    docsDir: 'docs',
    docsBranch: '8.x-1.x',
    editLinks: true,
    nav: [
      { text: 'Get Started', link: '/configuring_localhost' },
      { text: 'Sub-profile Template', link: 'https://github.com/ucdavis/sitefarm-distro-template' },
    ],
    sidebar: [
      ['/', 'Home'],
      ['configuring_localhost', 'Local Development Setup'],
      ['creating_subprofile', 'Create a Sub-Profile'],
      ['testing', 'Tests'],
      'best_practices',
      'sitefarm_custom_modules',
      {
        title: 'Feature Modules',
        children: [
          ['sitefarm_features/sitefarm_core', 'Core'],
          ['sitefarm_features/sitefarm_page_feature', 'Basic Page'],
          ['sitefarm_features/sitefarm_article_feature', 'Articles'],
          ['sitefarm_features/sitefarm_person_feature', 'Person'],
          ['sitefarm_features/sitefarm_event_feature', 'Event'],
          ['sitefarm_features/sitefarm_photo_gallery_feature', 'Photo Gallery'],
          ['sitefarm_features/sitefarm_basic_block_feature', 'Basic Block'],
          ['sitefarm_features/sitefarm_focal_link_feature', 'Focal Link'],
          ['sitefarm_features/sitefarm_focus_box_feature', 'Focus Box'],
          ['sitefarm_features/sitefarm_hero_banner_feature', 'Hero Banner'],
          ['sitefarm_features/sitefarm_marketing_highlight_feature', 'Marketing Highlight'],
          ['sitefarm_features/sitefarm_image_styles_feature', 'Image Styles'],
          ['sitefarm_features/sitefarm_wysiwyg_feature', 'WYSIWYG'],
        ]
      },
      {
        title: 'New Config',
        children: [
          'adding_new_config',
          'altering_config',
          'adding-core-reference-field',
          'overriding_image_styles',
        ]
      },
      {
        title: 'Libraries',
        children: [
          'libraries',
          'adding_libraries',
        ]
      },

    ]
  }
};
