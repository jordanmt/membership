<?php

namespace Drupal\membership_term\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\scheduled_message\Plugin\ScheduledMessageInterface;
use Drupal\scheduled_message\ScheduledMessagePluginCollection;

/**
 * Defines the Membership term type entity.
 *
 * @ConfigEntityType(
 *   id = "membership_term_type",
 *   label = @Translation("Membership term type"),
 *   handlers = {
 *     "list_builder" = "Drupal\membership_term\MembershipTermTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\membership_term\Form\MembershipTermTypeForm",
 *       "edit" = "Drupal\membership_term\Form\MembershipTermTypeForm",
 *       "delete" = "Drupal\membership_term\Form\MembershipTermTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\membership_term\MembershipTermTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "membership_term_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "membership_term",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "membership_type",
 *     "term_length",
 *     "grace_period",
 *     "workflow",
 *     "term_length",
 *     "messages",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/membership_term_type/{membership_term_type}",
 *     "add-form" = "/admin/structure/membership_term_type/add",
 *     "edit-form" = "/admin/structure/membership_term_type/{membership_term_type}/edit",
 *     "delete-form" = "/admin/structure/membership_term_type/{membership_term_type}/delete",
 *     "collection" = "/admin/structure/membership_term_type"
 *   }
 * )
 */
class MembershipTermType extends ConfigEntityBundleBase implements MembershipTermTypeInterface, EntityWithPluginCollectionInterface {

  /**
   * The Membership term type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Membership term type label.
   *
   * @var string
   */
  protected $label;

  /**
   * Term length for this membership term type.
   *
   * @var string
   */
  protected $term_length;

  /**
   * Grace period after term expires, before membership is revoked.
   *
   * @var string
   */
  protected $grace_period;

  /**
   * The membership term workflow ID.
   *
   * @var string
   */
  protected $workflow;

  /**
   * The membership type for this term type.
   *
   * @var string
   */
  protected $membership_type;

  /**
   * List of scheduled messages for this type.
   *
   * @var array
   */
  protected $messages = [];

  /**
   * @var ScheduledMessagePluginCollection
   */
  protected $messagesCollection;
  /**
   * @inheritdoc
   */
  public function getMembershipType() {
    return $this->membership_type;
  }

  /**
   * @inheritdoc
   */
  public function setMembershipType($membership_type) {
    $this->membership_type = $membership_type;
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getTermLength() {
    return $this->term_length;
  }

  /**
   * @inheritdoc
   */
  public function getGracePeriod() {
    return $this->grace_period;
  }

  /**
   * @inheritdoc
   */
  public function getWorkflowId() {
    return $this->workflow;
  }

  /**
   * @inheritdoc
   */
  public function setWorkflowId($workflow) {
    $this->workflow = $workflow;
    return $this;
  }

  public function getMessage($message) {
    return $this->getMessages()->get($message);
  }

  public function getMessages() {
    if (!$this->messagesCollection) {
      $this->messagesCollection = new ScheduledMessagePluginCollection(\Drupal::service('plugin.manager.scheduled_message'), $this->messages);

    }
    return $this->messagesCollection;
  }

  public function addMessage(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getMessages()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  public function deleteMessage(ScheduledMessageInterface $message) {
    $this->getMessages()->removeInstanceId($message->getUuid());
    $this->save();
  }
  /**
   * @inheritdoc
   */
  public function getPluginCollections() {
    return [ 'messages' => $this->getMessages() ];
  }
}
