<?php

namespace Drupal\e_invoice_cr;

/**
 * Implements the XML signature process.
 */
class Signature implements SignatureInterface {

  /**
   * {@inheritdoc}
   */
  public function signDocument() {
    // Locate where is the jar.
    $res = chdir("modules/custom/e_envoice_cr/jar");
    if ($res) {
      $base_path = DRUPAL_ROOT . '/sites/default/files/';
      // Check the directory.
      $signed_path = $base_path . 'xml_signed/';
      if (file_prepare_directory($signed_path, FILE_CREATE_DIRECTORY)) {
        // Define the paths.
        $cert_path = $base_path . "certs/";
        $settings = \Drupal::config('e_invoice_cr.settings');
        $pass = $settings->get('cert_password');
        $doc_path = $base_path . "xml/";
        $signed_path = $base_path . "xml_signed/";
        $doc_name = "document";
        // Build the java command.
        $command = 'java -jar java-xades4j-signer.jar ' . $cert_path . ' "' . $pass . '" ' . $doc_path . ' ' . $signed_path . ' ' . $doc_name . ' 2>&1';
        // Execute the command.
        exec($command, $response);
        // Send the response.
        return $response;
      }
      else {
        $message = t('Error. The xml_signed directory could not be created.');
        drupal_set_message($message, 'error');
        return FALSE;
      }
    }
    else {
      $message = t('Error. There were problems running the chdir command.');
      drupal_set_message($message, 'error');
      return FALSE;
    }
  }

}
