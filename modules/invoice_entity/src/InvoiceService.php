<?php

namespace Drupal\invoice_entity;

use Drupal\invoice_entity\Entity\InvoiceEntity;
use Drupal\invoice_entity\Entity\InvoiceEntityInterface;

/**
 * Class InvoiceService.
 */
class InvoiceService implements InvoiceServiceInterface {

  protected static $invoiceNumber;
  protected static $consecutiveName;

  /**
   * Update the configuration values.
   */
  public function updateValues() {
    $this->setInvoiceVariable(self::$consecutiveName, self::$invoiceNumber);
  }
 
  /**
   * {@inheritdoc}
   */
  public static function setInvoiceVariable($variable_name, $value) {
    $config = \Drupal::service('config.factory')->getEditable('invoice_entity.settings');
    $config->set($variable_name, $value)->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getInvoiceVariable($variable_name) {
    $config = \Drupal::config('invoice_entity.settings');
    $value = $config->get($variable_name);
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function setConsecutiveNumber($documentType) {

    switch ($documentType) {
      case 'FE':
        self::$consecutiveName = 'electronic_bill_consecutive';
        break;

      case 'ND':
        self::$consecutiveName = 'debit_note_consecutive';
        break;

      case 'NC':
        self::$consecutiveName = 'credit_note_consecutive';
        break;

      case 'TE':
        self::$consecutiveName = 'electronic_ticket_consecutive';
        break;

      default:
        self::$consecutiveName = 'electronic_bill_consecutive';
        break;
    }

    self::$invoiceNumber = $this->getInvoiceVariable(self::$consecutiveName);
    if (is_null(self::$invoiceNumber)) {
      self::$invoiceNumber = '0000000001';
      $this->updateValues();
    }
  }

}
