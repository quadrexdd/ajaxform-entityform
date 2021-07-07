<?php

namespace Drupal\feedbacksform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Class DisplayTableController
 * @package Drupal\mymodule\Controller
 */
class DisplayTableController extends ControllerBase
{

  public function index()
  {

    $query = \Drupal::database()->select('feedbacks', 'm');
    $query->fields('m', ['id', 'first_name', 'email_address', 'phone_number', 'feedback', 'submit_date', 'fid_avatar_image', 'fid_feedback_image']);
    $result = $query->execute()->fetchAllAssoc('id');
    $rows = array();
    foreach ($result as $row => $content) {
      $rows[] = array('id' => $content->id, 'first_name' => $content->first_name, 'email_address' => $content->email_address, 'phone_number' => $content->phone_number,
        'feedback' => $content->feedback, 'submit_date' => $content->submit_date, 'fid_avatar_image' => $content->fid_avatar_image,
        'fid_feedbackimage' => $content->fid_feedbackimage);
    }
    // render table
    return [
      '#theme' => 'feedbacks_template',
      '#rows' => $rows,
    ];
  }
}
