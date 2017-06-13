<?php

namespace Drupal\Tests\sitefarm_simple_configuration\Unit\Form;

use Drupal\sitefarm_simple_configuration\Form\CacheClearForm;

/**
 * Mock class for testing
 */
class MockCacheClearForm extends CacheClearForm {

  /**
   * Clear all Drupal caches and set a message
   */
  protected function clearCaches() {
    return 'Caches cleared.';
  }

}
