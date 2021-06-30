<?php

namespace Drupal\feedbacksform\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AjaxFormSubmit' block.
 *
 * @Block (
 *   id = "feedbacksform_block",
 *   admin_label = @Translation ("Feedbacks Form Block"),
 *   category = @Translation ("Custom Block to display Feedbacks form")
 * )
 */
class AjaxFormBlock extends BlockBase {
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\feedbacksform\Form\AjaxFormSubmit');
    return $form;
  }
}
