<?php
/**
 * @file
 * Contains \Drupal\feedbacksform\Form
 */

namespace Drupal\feedbacksform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;


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
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#desctiption' => $this->t('Enter your First name.'),
      '#required' => TRUE,
      '#attributes' => [
        'name' => 'firstname',
      ],
    ];
    $form['email_address'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#description' => $this->t('Please enter your e-mail address'),
      '#required' => TRUE,
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
      '#description' => $this->t('Please, enter your phone number'),
      '#required' => TRUE,
      '#maxlength' => '10',
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
      '#value' => $this->t('Submit Feedback'),
      '#ajax' => [
        'callback' => '::AjaxValidateForm',
        'event' => 'change',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('Verifying...'),
          ),
      ],
      '#suffix' => '<div class="ajax-validate-form-error"></div>',
    ];
    $form['#cache']['max-age'] = 0;
    return $form;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $name_value = $form_state->getValue('first_name');
    if (strlen($name_value) <= 2) {
      $form_state->setErrorByName('first_name', 'Why are you gay?');
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = array(
      'first_name' => $form_state->getValue('first_name'),
      'email_address' => $form_state->getValue('email_address'),
      'phone_number' => $form_state->getValue('phone_number'),
      'feedback' => $form_state->getValue('feedback'),
    );
    \Drupal::database()->insert('feedbacks')->fields($data)->execute();
    \Drupal::messenger()->addMessage('Thank you for feedback!');
  }
}
