<?php

namespace Drupal\feedbacksform\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Link;
use Drupal\Core\Url;

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
      'delete' => t('Delete'),
      'edit' => t('Edit'),
    );
    $query = \Drupal::database()->select('feedbacks', 'm');
    $query->fields('m', ['id', 'first_name', 'email_address', 'phone_number', 'feedback', 'submit_date', 'fid_avatar_image', 'fid_feedback_image']);
    $results = $query->execute()->fetchAll();
    $rows = array();
    foreach ($results as $data) {
      $url_delete = Url::fromRoute('feedbacksform.delete_form', ['id' => $data->id], []);
      $url_edit = Url::fromRoute('feedbacksform.ajax_form_submit', ['id' => $data->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);

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
        'delete' => $linkDelete,
        'edit' =>  $linkEdit,
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
  public function editFeedbackAjax($id) {

    $conn = Database::getConnection();
    $query = $conn->select('feedbacks', 'm');
    $query->condition('id', $id)->fields('m');
    $record = $query->execute()->fetchAssoc();

    $render_array = \Drupal::formBuilder()->getForm('Drupal\feedbacksform\Form\AjaxFormSubmit',$record);
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand('Edit Form', $render_array, ['width' => '800']));

    return $response;
  }
}
