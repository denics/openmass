<?php

namespace Drupal\workbench_moderation\Plugin\Menu;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\workbench_moderation\ModerationInformation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class for making the edit tab use 'Edit draft' or 'New draft'
 */
class EditTab extends LocalTaskDefault implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The moderation information service.
   *
   * @var \Drupal\workbench_moderation\ModerationInformation
   */
  protected $moderationInfo;

  /**
   * The entity.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  protected $entity;

  /**
   * The entity storage, so that the entity can be loaded.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * Constructs a new EditTab object.
   *
   * @param array $configuration
   *   Plugin configuration.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin definition.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The translation service.
   * @param \Drupal\workbench_moderation\ModerationInformation $moderation_information
   *   The moderation information.
   * @param \Drupal\Core\Entity\Query\QueryInterface $entity_query
   *   The entity query service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TranslationInterface $string_translation, ModerationInformation $moderation_information, EntityStorageInterface $entity_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->stringTranslation = $string_translation;
    $this->moderationInfo = $moderation_information;
    $this->entityStorage = $entity_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('string_translation'),
      $container->get('workbench_moderation.moderation_information'),
      $container->get('entity_type.manager')->getStorage($plugin_definition['entity_type_id'])
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    // Override the node here with the latest revision.
    $this->entity = $route_match->getParameter($this->pluginDefinition['entity_type_id']);

    // In some cases, like when Views is used to create a sub-path with a local
    // task tab, the entity is not loaded by the routing system.
    if ($this->entity && !($this->entity instanceof EntityInterface)) {
      $this->entity = $this->entityStorage->load($this->entity);
    }

    return parent::getRouteParameters($route_match);
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    if (!$this->moderationInfo->isModeratableEntity($this->entity)) {
      // Moderation isn't enabled.
      return parent::getTitle();
    }

    // @todo write a test for this.
    return $this->moderationInfo->isLiveRevision($this->entity)
      ? $this->t('New draft')
      : $this->t('Edit draft');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // @todo write a test for this.
    $tags = parent::getCacheTags();
    // Tab changes if node or node-type is modified.
    $tags = array_merge($tags, $this->entity->getCacheTags());
    $tags[] = $this->entity->getEntityType()->getBundleEntityType() . ':' . $this->entity->bundle();
    return $tags;
  }

}
