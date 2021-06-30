<?php
/**
 * @file
 * Contains \Drupal\feedbacksform\Form
 */

namespace Drupal\feedbacksform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;


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
    $form['#prefix'] = '<div id="feedbacks-form-inner">';
    $form['#suffix'] = '</div>';
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#desctiption' => $this->t('Enter your First name.'),
      '#required' => TRUE,
      '#allowed_tags' => Xss::getHtmlTagList(),
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
      '#allowed_tags' => Xss::getHtmlTagList(),
    ];
    $get_current_time=\Drupal::time()->getCurrentTime();
    date_default_timezone_set('Europe/Kiev');
    $current_time = date('m/d/Y H:i:s', $get_current_time);
    $form['submit_date'] = [
      '#type' => 'hidden',
      '#value' => $current_time,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Feedback'),
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $first_name=$form_state->getValue('first_name');
    if(strlen($first_name) <=2 && strlen($first_name)>= 100) {
      $form_state->setErrorByName('first_name', 'Enter correct first name');
    }
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = array(
      'first_name' => $form_state->getValue('first_name'),
      'email_address' => $form_state->getValue('email_address'),
      'phone_number' => $form_state->getValue('phone_number'),
      'feedback' => $form_state->getValue('feedback'),
      'submit_date' => $form_state->getValue('submit_date'),
    );
    \Drupal::database()->insert('feedbacks')->fields($data)->execute();
    \Drupal::messenger()->addMessage('Thank you for feedback!');
  }
}
