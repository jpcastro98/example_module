<?php

namespace Drupal\example_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\example_module\Services\ExampleService;

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

  public function __construct(ExampleService $exampleService) {
    $this->exampleService = $exampleService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('example_module.service')
    );
  }

  public function build() {

    $data = $this->exampleService->getAllUsers();
    $theme = [
      '#theme' => 'example_users_theme',
      '#data' => $data,
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
