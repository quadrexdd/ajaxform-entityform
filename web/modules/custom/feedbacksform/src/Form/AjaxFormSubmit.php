<?php
/**
 * @file
 * Contains \Drupal\feedbacksform\Form
 */

namespace Drupal\feedbacksform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;
use Drupal\file\Entity\File;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;


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
    $conn = Database::getConnection();
    $data = array();
    if(isset($_GET['id'])) {
      $query = $conn->select('feedbacks', 'm')
        ->condition('id', $_GET['id'])
        ->fields('m');
      $data = $query->execute()->fetchAssoc();
    }
    $form['#prefix'] = '<div id="feedbacks-form-inner">';
    $form['#suffix'] = '</div>';
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#desctiption' => $this->t('Enter your First name.'),
      '#required' => TRUE,
      '#default_value' => (isset($data['first_name'])) ? $data['first_name'] : '',
      '#allowed_tags' => Xss::getHtmlTagList(),
    ];
    $form['email_address'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#description' => $this->t('Please enter your e-mail address'),
      '#required' => TRUE,
      '#default_value' => (isset($data['email_address'])) ? $data['email_address'] : '',
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
      '#description' => $this->t('Please, enter your phone number'),
      '#required' => TRUE,
      '#default_value' => (isset($data['phone_number'])) ? $data['phone_number'] : '',
      '#maxlength' => '10',
    ];
    $form['feedback'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feedback'),
      '#description' => $this->t('Please, enter your feedback'),
      '#required' => TRUE,
      '#default_value' => (isset($data['feedback'])) ? $data['feedback'] : '',
      '#allowed_tags' => Xss::getHtmlTagList(),
    ];
    $form['avatar_image'] = [
      '#type' => 'managed_file',
      '#default_value' => (isset($data['fid_avatar_image'])) ? $data['fid_avatar_image'] : '',
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
      '#default_value' => (isset($data['fid_feedback_image'])) ? $data['fid_feedback_image'] : '',
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
    if (isset($_GET['id'])) {
      // update data in database
      \Drupal::database()->update('feedbacks')->fields($data)->condition('id', $_GET['id'])->execute();
      $url = new Url('feedbacksform.ajax_form_submit');
      $response = new RedirectResponse($url->toString());
      $response->send();
      \Drupal::messenger()->addMessage('Successfully edited!');
    } else {
      // insert data to database
      \Drupal::database()->insert('feedbacks')->fields($data)->execute();
      \Drupal::messenger()->addMessage('Thank you for feedback!');
    }
  }
}
