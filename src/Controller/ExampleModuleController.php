<?php

namespace Drupal\example_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\example_module\Services\ExampleService;
use Drupal\Core\Entity\EntityTypeManager;


/**
 * Returns responses for Example Module routes.
 */
class ExampleModuleController extends ControllerBase {

   /**
   * Service Example.
   *
   * @var Drupal\example_module\Services\ExampleService;
   */
  public $exampleService;
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(ExampleService $exampleService,EntityTypeManager $entityTypeManager) {
    $this->exampleService = $exampleService;
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('example_module.service'),
      $container->get('entity_type.manager')
    );
  }

  public function build() {

    /**
     * Se cargan todos los terminos de positions
     */
    $terms = $this->entityTypeManager->getStorage('taxonomy_term');
    $position = $terms->loadTree('positions');
    $positions = [];
    foreach ($position as $role) {
      $positions[$role->tid] = $role->name;
    }

    /**
     * Se cargan todos los usuarios
     */
    $data = $this->exampleService->getAllUsers();
    /**
     * Se devuelve un template con todos los usuarios mostrados en una tabla.
     */
    $theme = [
      '#theme' => 'example_users_theme',
      '#data' => $data,
      '#positions' => $positions,
      '#cache' => [
          'max-age' => 0
      ],
      '#attached' => [
        'library' =>
        'example_module/example_module']
    ];

    return  $theme;

  }

}
