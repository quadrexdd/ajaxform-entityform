<?php

namespace Drupal\feedbacks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FeedbacksSettingsForm.
 *
 * @package Drupal\feedbacks\Form
 * @ingroup feedbacks
 */
class FeedbacksSettingsForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'feedbacks_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }


  /**
   * Define the form used for Feedbacks settings.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   Form definition array.
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['feedbacks_settings']['#markup'] = 'Settings form for Feedbacks. Manage field settings here.';
    return $form;
  }

}
