<?php

namespace Drupal\feedbacksform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class FeedbacksController extends ControllerBase {
  public function index() {
    $header_feedbacks_titles = array(
      'first_name' => t('First Name'),
      'email_address' => t('Email'),
      'phone_number' => t('Phone'),
      'feedback' => t('Feedback'),
    );
    $query = \Drupal::database()->select('feedbacks', 'm');
    $query->fields('m', ['id', 'first_name', 'email_address', 'phone_number', 'feedback']);
    $results = $query->execute() ->fetchAll();
    $feedbacks = array();
    foreach ($feedbacks as $data) {
      $feedbacks[] = array(
        'first_name' => $data->first_name,
        'email_address' => $data->email_address,
        'phone_number' => $data->phone_number,
        'feedback' => $data->feedback,
      );
    }
    $form['feedbacks'] = [
      '#type' => 'table',
      '#header' => $header_feedbacks_titles,
      '#rows' => $feedbacks,
      '#empty' => t('No feedbacks found'),
    ];
    return $form;
  }
}
