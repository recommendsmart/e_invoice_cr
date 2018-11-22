<?php

namespace Drupal\invoice_received_entity\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Invoice received entity revision.
 *
 * @ingroup invoice_received_entity
 */
class InvoiceReceivedEntityRevisionDeleteForm extends ConfirmFormBase {


  /**
   * The Invoice received entity revision.
   *
   * @var \Drupal\invoice_received_entity\Entity\InvoiceReceivedEntityInterface
   */
  protected $revision;

  /**
   * The Invoice received entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $InvoiceReceivedEntityStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new InvoiceReceivedEntityRevisionDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $entity_storage, Connection $connection) {
    $this->InvoiceReceivedEntityStorage = $entity_storage;
    $this->connection = $connection;
  }

  /**
   * Instantiates a new instance of this class.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container this instance should use.
   *
   * @return \Drupal\Core\Form\ConfirmFormBase
   *   A new instance of this class.
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('invoice_received_entity'),
      $container->get('database')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'invoice_received_entity_revision_delete_confirm';
  }

  /**
   * Returns the question to ask the user.
   *
   * @return string
   *   The form question. The page title will be set to this value.
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the revision from %revision-date?', ['%revision-date' => format_date($this->revision->getRevisionCreationTime())]);
  }

  /**
   * Returns the route to go to if the user cancels the action.
   *
   * @return \Drupal\Core\Url
   *   A URL object.
   */
  public function getCancelUrl() {
    return new Url('entity.invoice_received_entity.version_history', ['invoice_received_entity' => $this->revision->id()]);
  }

  /**
   * Returns a caption for the button that confirms the action.
   *
   * @return string
   *   The form confirmation text.
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * Returns a caption for the button that confirms the action.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $customer_entity_revision
   *   All previous revisions of the customer entity.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $invoice_received_entity_revision = NULL) {
    $this->revision = $this->InvoiceReceivedEntityStorage->loadRevision($invoice_received_entity_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->InvoiceReceivedEntityStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Invoice received entity: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    drupal_set_message(t('Revision from %revision-date of Invoice received entity %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.invoice_received_entity.canonical',
       ['invoice_received_entity' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {invoice_received_entity_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.invoice_received_entity.version_history',
         ['invoice_received_entity' => $this->revision->id()]
      );
    }
  }

}
