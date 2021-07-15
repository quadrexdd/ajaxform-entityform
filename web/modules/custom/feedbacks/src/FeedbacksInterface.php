<?php

namespace Drupal\feedbacks;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Interface FeedbacksInterface
 *
 * @package Drupal\feedbacks
 * @ingroup feedbacks
 */
interface FeedbacksInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
