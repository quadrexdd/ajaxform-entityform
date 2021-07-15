<?php

namespace Drupal\feedbacks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides an 'EntityForm' block.
 *
 * @Block (
 *   id = "feedbacksform_entity_block",
 *   admin_label = @Translation ("Feedbacks entity Form Block"),
 *   category = @Translation ("Custom Block to display Entity Feedbacks form")
 * )
 */
class FeedbackEntityForm extends BlockBase {

  public function build() {
    $entity = \Drupal\feedbacks\Entity\Feedbacks::create();
    $form = \Drupal::service('entity.form_builder')->getForm($entity, 'add');
    return $form;
  }

}
