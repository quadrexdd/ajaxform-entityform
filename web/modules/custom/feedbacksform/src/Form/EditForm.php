<?php

namespace Drupal\feedbacksform\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class FeedbackEditForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'feedback_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$data = NULL) {
    $conn = Database::getConnection();

    if(isset($data['id'])){
      $form['id'] = [
        '#type' => 'hidden',
        '#attributes' => array(
          'class' => ['txt-class'],
        ),
        '#default_value' => (isset($data['id'])) ? $data['id'] : '',
      ];
    }
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#required' => TRUE,
      '#attributes' => array(
        'class' => ['txt-class'],
      ),
      '#default_value' => (isset($data['first_name'])) ? $data['first_name'] : '',
    ];
    $form['email_address'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#required' => TRUE,
      '#attributes' => array(
        'class' => ['txt-class'],
      ),
      '#default_value' => (isset($data['email_address'])) ? $data['email_address'] : '',

    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
      '#required' => TRUE,
      '#attributes' => array(
        'class' => ['txt-class'],
      ),
      '#default_value' => (isset($data['phone_number'])) ? $data['phone_number'] : '',
    ];
    $form['feedback'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feedback'),
      '#required' => TRUE,
      '#attributes' => array(
        'class' => ['txt-class'],
      ),
      '#default_value' => (isset($data['feedback'])) ? $data['feedback'] : '',
    ];
    $form['avatar_image'] = [
      '#type' => 'managed_file',
      '#default_value' => (isset($data['fid_avatar_image'])) ? $data['fid_avatar_image'] : '',
      '#title' => $this->t('Avatar image'),
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
      '#upload_location' => 'public://',
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(5242880),
      ),
    ];



    $form['actions']['#type'] = 'actions';
    $form['actions']['Save'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => ['callback' => '::updateFeedbackData'] ,
      '#value' => (isset($data['first_name'])) ? $this->t('Update') : $this->t('Save') ,
    ];



    $form['#prefix'] = '<div class="form-div-edit" id="form-div-edit">';
    $form['#suffix'] = '</div>';

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {

  }

  public function updateFeedbackData(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#form-div-edit', $form));
    }
    else {
      $conn = Database::getConnection();
      $field = $form_state->getValues();
      $re_url = Url::fromRoute('feedbacksform.ajax_form_submit');

      $fields["first_name"] = $field['first_name'];
      $fields["email_address"] = $field['email_address'];
      $fields["phone_number"] = $field['phone_number'];
      $fields["feedback"] = $field['feedback'];
      $fields["avatar_image"] = $field['avatar_image'];
      $fields["feedback_image"] = $field['feedback_image'];

      $conn->update('feedbacks')
        ->fields($fields)->condition('id', $field['id'])->execute();
      $response->addCommand(new OpenModalDialogCommand("Success!!!", 'The table has been submitted.', ['width' => 800]));
      $render_array = \Drupal::formBuilder()->getForm('Drupal\feedbacksform\Form\AjaxFormSubmit','All');


      $response->addCommand(new HtmlCommand('.result_message','' ));
      $response->addCommand(new AppendCommand('.result_message', $render_array));
      $response->addCommand(new InvokeCommand('.pagination-link', 'removeClass', array('active')));
      $response->addCommand(new InvokeCommand('.pagination-link:first', 'addClass', array('active')));

    }

    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {

  }

}
