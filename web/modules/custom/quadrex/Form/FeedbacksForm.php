<?php
/**
 * @file
 * Contains \Drupal\quadrex\Form\FeedbacksForm.
 */

namespace Drupal\quadrex\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class FeedbacksForm extends FormBase {
  /**
   * {@inheritdoc }
   */
  public function getFormId() {
    return 'feedbacks_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['first_name'] = [
      '#type' => 'textfield',
      '#first_name' => $this->t('First name'),
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
      '#ajax' => [
        '#callback' => '::validateEmailAjax',
        '#event' => 'change',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('Verifying email...')
        ),
      ],
      '#suffix' => '<div class="email-validation-message"></div>'
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
      '#description' => $this->t('Please, enter your phone number'),
      '#required' => TRUE,
    ];
    $form['feedback'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feedback'),
      '#description' => $this->t('Please, enter your feedback'),
      '#required' => TRUE,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    $form['profile_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Avatar picture'),
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(2000000),
      ),
      '#upload_location' => 'public://avatar-pictures'
    ];
    $form['feedback_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Feedback picture'),
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(5000000),
      ),
      '#upload_location' => 'public://feedback-pictures'
    ];
    $form['system_messages'] = [
      '#markup' => '<div id="form-system-messages"></div>',
      '#weight' => -100,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => 'Submit this form',
      '#ajax' => [
        'callback' => '::ajaxSubmitCallback',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];
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

  public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (substr($form_state->getValue('email_address'), -11) == 'example.com' && $form_state->getValue('email_address') !=='@') {
      $response->addCommand(new HtmlCommand('.email-validation-message', 'This e-mail is invalid'));
    }
    else {
      $response->addCommand(new HtmlCommand('.email-validation-message', ''));
    }
    return $response;
  }
  public function validateFirstNameAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    switch ($form_state ->getValue('first_name')) {
      case strlen($form_state->getValue('first_name') <=2 ||'first_name' >=100) :
        $style = ['border' => '2px solid red'];
        break;
      case strlen($form_state->getValue('first_name') >2 || 'first_name' <100) :
        $style = [];
        break;
    }
  }
}


