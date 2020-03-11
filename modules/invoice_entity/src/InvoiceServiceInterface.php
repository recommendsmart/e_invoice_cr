<?php

namespace Drupal\invoice_entity;

use Drupal\invoice_entity\Entity\InvoiceEntity;

/**
 * Interface InvoiceServiceInterface.
 */
interface InvoiceServiceInterface {

  /**
   * Set variable value.
   *
   * @param string $variable_name
   *   Variable machine name.
   * @param string $value
   *   New value for the variable.
   */
  public static function setInvoiceVariable($variable_name, $value);

  /**
   * Gets variables.
   *
   * @return string
   *   Get value of the requested variable.
   */
  public function getInvoiceVariable($variable_name);

}
