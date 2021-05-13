<?php
/**
 * @file
 * Contains \Drupal\feedbacksform\Form
 */

namespace Drupal\feedbacksform\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Form with modal window.
 */
class AjaxFormSubmit extends FormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'ajax_form_submit';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['system_messages'] = [
      '#markup' => '<div id="form-system-messages"></div>',
      '#weight' => -100,
    ];
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#desctiption' => $this->t('Enter your First name. Note that the First name must be at least 3 characters in length'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::validateFirstNameAjax',
        'event' => 'change',
      ],
      '#suffix' => '<div class="first-name-validation-message"></div>',
    ];
    $form['email_address'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#description' => $this->t('Please enter your e-mail address'),
      '#required' => TRUE,
    ];
    $form['phone_number'] = [
      '#type' => 'number',
      '#title' => $this->t('Phone number'),
      '#description' => $this->t('Please, enter your phone number'),
      '#required' => TRUE,
//      '#attributes' => array(
//        'type' => 'number',
//      ),
    ];
    $form['feedback'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feedback'),
      '#description' => $this->t('Please, enter your feedback'),
      '#required' => TRUE,
    ];
    $form['profile_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Avatar picture (maximum upload size 2MB)'),
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(2000000),
      ),
      '#upload_location' => 'public://avatar-pictures'
    ];
    $form['feedback_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Feedback picture (maximum upload size 5MB)'),
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(5000000),
      ),
      '#upload_location' => 'public://feedback-pictures'
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => 'Submit this form',
    ];
    $form['#cache']['max-age'] = 0;
    return $form;
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Thank you for feedback!');
  }
  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $message = [
      '#theme' => 'status_messages',
      '#message_list' => \Drupal::messenger()->addMessage(''),
      '#status_headings' => [
        'status' => t('Status message'),
        'error' => t('Error message'),
        'warning' => t('Warning message'),
      ],
    ];
    $messages = \Drupal::service('renderer')->render($message);
    $ajax_response->addCommand(new HtmlCommand('#form-system-messages', $messages));
    return $ajax_response;
  }
  public function validateFirstNameAjax (array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (strlen($form_state->getValue('first_name')) <= '2' || (strlen($form_state->getValue('first_name')) >='100')) {
      $response->addCommand(new HtmlCommand('.first-name-validation-message', 'The first name should contain minimum 2 characters and not greater then 100'));
    }
    else {
      $response->addCommand(new HtmlCommand('.first-name-validation-message', ''));
    }
    return $response;
  }
}


