<?php

namespace Drupal\feedbacksform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
/**
 * Class DisplayTableController
 * @package Drupal\mymodule\Controller
 */
class DisplayTableController extends ControllerBase
{

  public function index()
  {
    //create table header
    $header_table = array(
      'id' => t('ID'),
      'first_name' => t('First name'),
      'email' => t('Email'),
      'phone' => t('Phone'),
      'feedback' => t('Feedback text'),
      'date' => t('Submit date'),
      'avatar' => t('Avatar image'),
      'feedback_image' => t ('Feedback image'),
    );
    $query = \Drupal::database()->select('feedbacks', 'm');
    $query->fields('m', ['id', 'first_name', 'email_address', 'phone_number', 'feedback', 'submit_date', 'fid_avatar_image', 'fid_feedback_image']);
    $results = $query->execute()->fetchAll();
    $rows = array();
    foreach ($results as $data) {

      //get data
      $rows[] = array(
        'id' => $data->id,
        'first_name' => $data->first_name,
        'email' => $data->email_address,
        'phone_number' => $data->phone_number,
        'feedback' => $data->feedback,
        'submit_date' => $data->submit_date,
        'avatar_image'=> $data->fid_avatar_image,
        'feedback_image'=>$data->fid_feedback_image,
      );

    }
    // render table
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No data found, sorry bro!'),
    ];
    return $form;
  }
}
