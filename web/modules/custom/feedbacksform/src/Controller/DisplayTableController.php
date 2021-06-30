<?php

namespace Drupal\feedbacksform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\feedbacksform\Form\AjaxFormSubmit;

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
      'first_name' => t('first name'),
      'email' => t('Email'),
      'phone' => t('phone'),
    );
    $query = \Drupal::database()->select('feedbacks', 'm');
    $query->fields('m', ['id', 'first_name', 'email_address', 'phone_number']);
    $results = $query->execute()->fetchAll();
    $rows = array();
    foreach ($results as $data) {

      //get data
      $rows[] = array(
        'id' => $data->id,
        'first_name' => $data->first_name,
        'email' => $data->email_address,
        'phone_number' => $data->phone_number,
      );

    }
    // render table
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No data found, sorry nigga!'),
    ];
    return $form;
  }
}
