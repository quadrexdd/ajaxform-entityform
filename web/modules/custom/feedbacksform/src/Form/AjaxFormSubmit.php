<?php
/**
 * @file
 * Contains \Drupal\feedbacksform\Form
 */

namespace Drupal\feedbacksform\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

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
      '#suffix' => '<div class="firstname-validation-message"></div>'
    ];
    $form['email_address'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#description' => $this->t('Please enter your e-mail address'),
      '#required' => TRUE,
      '#suffix' => '<div class="email-validation-message"></div>'
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
      '#description' => $this->t('Please, enter your phone number'),
      '#required' => TRUE,
      '#maxlength' => '10',
      '#suffix' => '<div class="phone-validation-message"></div>'
    ];
    $form['feedback'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feedback'),
      '#description' => $this->t('Please, enter your feedback'),
      '#required' => TRUE,
      '#allowed_tags' => Xss::getHtmlTagList(),
    ];
    $form['avatar_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Avatar image'),
      '#description' => $this->t('You may upload your avatar image'),
      '#upload_location' => 'public://',
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(2097152),
      ),
    ];
    $form['feedback_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Feedback image'),
      '#description' => $this->t('You may upload your feedback image'),
      '#upload_location' => 'public://',
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(5242880),
      ),
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Feedback'),
      '#ajax' => [
        'callback' => '::submitCallback',
      ],
    ];
    return $form;
  }

  public function submitCallback (array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $phone_number = $form_state->getValue('phone_number');
    $pattern_phone = preg_match("/^[0-9]{10}$/", $phone_number);
    if (!$pattern_phone) {
      $response->addCommand(new HtmlCommand('.phone-validation-message', 'Your phone is not valid!'));
    }
    else {
      $response->addCommand(new HtmlCommand('.phone-validation-message', ''));
    }
    $email_address = $form_state->getValue('email_address');
    $pattern_mail = preg_match("/[a-zA-Z0-9 +-]+@[a-zA-Z0-9 +-]+\.[a-zA-Z0-9]+/", $email_address);
    if (!$pattern_mail) {
      $response->addCommand(new HtmlCommand('.email-validation-message', 'Your email is not valid!'));
    }
    else {
      $response->addCommand(new HtmlCommand('.email-validation-message', ''));
    }
    $first_name=$form_state->getValue('first_name');
    if (strlen($first_name)<=2 || strlen($first_name) >=100) {
      $response->addCommand(new HtmlCommand('.firstname-validation-message', 'First name should contain >2 and <100 characters'));
    }
    else {
      $response->addCommand(new HtmlCommand('.firstname-validation-message', ''));
    }
    switch ($form_state->hasAnyErrors()) {
      case true:
        break;
      case false:
        $url = Url::fromRoute('feedbacksform.ajax_form_submit');
        $command = new RedirectCommand($url->toString());
        $response->addCommand($command);
    }
    return $response;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $phone_number = $form_state->getValue('phone_number');
    $email_address = $form_state->getValue('email_address');
    $first_name=$form_state->getValue('first_name');
    $pattern_phone = preg_match("/^[0-9]{10}$/", $phone_number);
    $pattern_mail = preg_match("/[a-zA-Z0-9 +-]+@[a-zA-Z0-9 +-]+\.[a-zA-Z0-9]+/", $email_address);
    if (!$pattern_phone) {
      $form_state->setErrorByName('phone_number', 'Please, enter correct phone');
    } else if(!$pattern_mail) {
      $form_state->setErrorByName('email_address', 'Please, enter correct email');
    } else if (strlen($first_name)<=2 || strlen($first_name) >=100) {
      $form_state->setErrorByName('first_name', 'Please, enter correct first name');
    }
    else {
      $form_state->clearErrors();
    }
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $get_current_time=\Drupal::time()->getCurrentTime();
    date_default_timezone_set('Europe/Kiev');
    $current_time = date('m/d/Y H:i:s', $get_current_time);
    $avatar_image = $form_state->getValue('avatar_image');
    $feedback_image = $form_state->getValue('feedback_image');
    $data = array(
      'first_name' => $form_state->getValue('first_name'),
      'email_address' => $form_state->getValue('email_address'),
      'phone_number' => $form_state->getValue('phone_number'),
      'feedback' => $form_state->getValue('feedback'),
      'fid_avatar_image' => $avatar_image[0],
      'fid_feedback_image' => $feedback_image[0],
      'submit_date' => $current_time,
    );
    $avatar_image_file = File::load($avatar_image[0]);
    if ($avatar_image_file) {
      $avatar_image_file->setPermanent();
      $avatar_image_file->save();
    }
    $feedback_image_file = File::load($feedback_image[0]);
    if ($feedback_image_file) {
      $feedback_image_file->setPermanent();
      $feedback_image_file->save();
    }
      \Drupal::database()->insert('feedbacks')->fields($data)->execute();
      \Drupal::messenger()->addMessage('Thank you for feedback!');
  }
}
