<?php

namespace Drupal\feedbacks\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for content_entity_feedbacks entity.
 *
 * @ingroup feedbacks
 */
class FeedbacksListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('Content Entity implements a feedback model. These feedbacks are fieldable entities. You can manage the fields on the <a href="@adminlink">Feedbacks admin page</a>.', [
        '@adminlink' => \Drupal::urlGenerator()
          ->generateFromRoute('feedbacks.feedbacks_settings'),
      ]),
    ];
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the feedbacks list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('FeedbackID');
    $header['first_name'] = $this->t('First name');
    $header['email_address'] = $this->t('Email');
    $header['phone_number'] = $this->t('Phone number');
    $header['feedback_text'] = $this->t('Feedback text');
    $header['avatar_image'] = $this->t('Avatar image');
    $header['feedback_image'] = $this->t('Feedback image');
    $header['created'] = $this->t('Created date');
    $header['changed'] = $this->t('Changed date');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\feedbacks\Entity\Feedbacks */
    $row['id'] = $entity->id();
    $row['first_name'] = $entity->first_name->value;
    $row['email_address'] = $entity->email_address->value;
    $row['phone_number'] = $entity->phone_number->value;
    $row['feedback_text'] = $entity->feedback_text->value;
    $row['avatar_image'] = $entity->avatar_image->value;
    $row['feedback_image'] = $entity->feedback_image->value;
    $row['created'] = $entity->created->value;
    $row['changed'] = $entity->changed->value;
    return $row + parent::buildRow($entity);
  }

}
