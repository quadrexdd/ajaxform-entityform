<?php
/**
 * @file
 * Contains \Drupal\feedbacksform\Form
 */

namespace Drupal\feedbacksform\Form;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides the form for editing feedbacks.
 */
class EditForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public $id;

  public function getFormId() {
    return 'feedback_edit_form';
  }
  /**
   * {@inheritdoc}
   * build edit-form with DefaultValues by ID condition
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $this->id = $id;
    $conn = Database::getConnection();
    $data = array();
    if (isset($id)) {
      $query = $conn->select('feedbacks', 'm')
        ->condition('id', $id)
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
      '#value' => $this->t('Save Feedback'),
    ];
    return $form;
  }
  /**
   * {@inheritdoc}
   * describes how edited data should be updated in DB, added redirect by route after deletion
   */
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
      // update data in database
      \Drupal::database()->update('feedbacks')->fields($data)->condition('id', $this->id)->execute();
      $url = new Url('feedbacksform.ajax_form_submit');
      $response = new RedirectResponse($url->toString());
      $response->send();
      \Drupal::messenger()->addMessage('Successfully edited!');
  }
}
