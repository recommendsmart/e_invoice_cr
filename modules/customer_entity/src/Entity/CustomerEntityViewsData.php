<?php

namespace Drupal\customer_entity\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Customer entities.
 */
class CustomerEntityViewsData extends EntityViewsData {

  /**
   * Returns views data for the entity type.
   *
   * @return array
   *   Views data in the format of hook_views_data().
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
