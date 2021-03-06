<?php

namespace Drupal\feedbacksform\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Class DisplayTableController
 * @package Drupal\mymodule\Controller
 */
class DisplayTableController extends ControllerBase {

  /**
   * @return array
   * describes how the Database Data should be displayed, returns variable with array of arrays for TWIG
   */
  public function index() {

    $query = \Drupal::database()->select('feedbacks', 'm');
    $query->fields('m', ['id', 'first_name', 'email_address', 'phone_number', 'feedback', 'submit_date', 'fid_avatar_image', 'fid_feedback_image']);
    $result = $query->execute()->fetchAllAssoc('id');
    $rows = array();
    foreach ($result as $row => $content) {
      $url_delete = Url::fromRoute('feedbacksform.delete_form', ['id' => $content->id], []);
      $url_edit = Url::fromRoute('feedbacksform.edit_form', ['id' => $content->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);
      if ($content->fid_avatar_image) {
        $avatar_image = File::load($content->fid_avatar_image)->url();
      }
      else {
        $avatar_image = 'http://custom-form.localhost/sites/default/files/l60Hf.png';
      }
      if ($content->fid_feedback_image) {
        $feedback_image = File::load($content->fid_feedback_image)->url();
      }
      else {
        $feedback_image = null;
      }
      $rows[] = array('id' => $content->id, 'first_name' => $content->first_name, 'email_address' => $content->email_address, 'phone_number' => $content->phone_number,
        'feedback' => $content->feedback, 'submit_date' => $content->submit_date, 'avatar_image' => $avatar_image, 'feedback_image' => $feedback_image, 'delete' => $linkDelete,
        'edit' => $linkEdit);
    }
    // render table
    return [
      '#theme' => 'feedbacks_template',
      '#rows' => $rows,
    ];
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
