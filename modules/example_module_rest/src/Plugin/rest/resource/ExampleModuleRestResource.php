<?php

namespace Drupal\example_module_rest\Plugin\rest\resource;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\example_module\Services\ExampleService;

/**
 * Represents ExampleRest records as resources.
 *
 * @RestResource (
 *   id = "example_module_rest",
 *   label = @Translation("ExampleRest"),
 *   uri_paths = {
 *     "canonical" = "/api/example-module-rest/{id}",
 *     "create" = "/api/example-module-rest"
 *   }
 * )
 *
 * @DCG
 * The plugin exposes key-value records as REST resources. In order to enable it
 * import the resource configuration into active configuration storage. An
 * example of such configuration can be located in the following file:
 * core/modules/rest/config/optional/rest.resource.entity.node.yml.
 * Alternatively you can enable it through admin interface provider by REST UI
 * module.
 * @see https://www.drupal.org/project/restui
 *
 * @DCG
 * Notice that this plugin does not provide any validation for the data.
 * Consider creating custom normalizer to validate and normalize the incoming
 * data. It can be enabled in the plugin definition as follows.
 * @code
 *   serialization_class = "Drupal\foo\MyDataStructure",
 * @endcode
 *
 * @DCG
 * For entities, it is recommended to use REST resource plugin provided by
 * Drupal core.
 * @see \Drupal\rest\Plugin\rest\resource\EntityResource
 */
class ExampleModuleRestResource extends ResourceBase
{

  /**
   * The key-value storage.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   */
  protected $storage;

  /**
   * The service ExampleService.
   *
   * @var Drupal\example_module\Services\ExampleService;
   */
  protected $exampleService;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    KeyValueFactoryInterface $keyValueFactory,
    ExampleService $exampleService
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger, $keyValueFactory);
    $this->storage = $keyValueFactory->get('example_module_rest');
    $this->exampleService = $exampleService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('keyvalue'),
      $container->get('example_module.service')
    );
  }

  /**
   * Responds to POST requests and saves the new record.
   *
   * @param array $data
   *   Data to write into the database.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   */
  public function post(array $data)
  {

    $validateData = $this->validateData($data);

    if (!(is_array($validateData) && in_array(false,$validateData))) {
      try {
        $response = $this->exampleService->saveUser($data['name'], $data['identification'], $data['birthdate'], $data['position_id']);
        $status = 201;
      } catch (\Exception $e) {
        $response = 'La solicitud fallo';
        $status = 400;
      }
    }else{
      $response = $validateData[0];
      $status = 400;
    }

    return new ResourceResponse($response, $status, ['Content-Type' => 'application/json']);
  }

  /**
   * Responds to GET requests.
   *
   * @param int $id
   *   The ID of the record.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get($id)
  {
    $response = $this->exampleService->getUsers($id);
    $status = 200;
    if (!$response) {
      $response = ['message' => 'Usuario no encontrado.'];
      $status = 404;
    }
    return new ResourceResponse($response, $status, ['Content-Type' => 'application/json']);
  }

  /**
   * Responds to PATCH requests.
   *
   * @param int $id
   *   The ID of the record.
   * @param array $data
   *   Data to write into the storage.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   */
  public function patch($id, array $data)
  {
    $validateData = $this->validateData($data);

    if (!(is_array($validateData) && in_array(false,$validateData))) {
      try {
        $response = $this->exampleService->updateUser($id,$data['name'], $data['identification'], $data['birthdate'], $data['position_id']);
        $status = 201;
      } catch (\Exception $e) {
        $response = 'La solicitud fallo';
        $status = 400;
      }
    }else{
      $response = $validateData[0];
      $status = 400;
    }
    return new ModifiedResourceResponse($response, $status);
  }

  /**
   * Responds to DELETE requests.
   *
   * @param int $id
   *   The ID of the record.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   */
  public function delete($id)
  {

    $response = $this->exampleService->deleteUser($id);
    if (!$response) {
      $response = ['message' => 'Usuario no encontrado.'];
      $status = 404;
    }
    return new ResourceResponse($response, $status, ['Content-Type' => 'application/json']);
  }

  /**
   * {@inheritdoc}
   */
  protected function getBaseRoute($canonical_path, $method)
  {
    $route = parent::getBaseRoute($canonical_path, $method);
    // Set ID validation pattern.
    if ($method != 'POST') {
      $route->setRequirement('id', '\d+');
    }
    return $route;
  }

  public function validateData($data)
  {

    $fields = ['name', 'identification', 'birthdate', 'position_id'];
    $countData = count($data);
    $countFields = count($fields);
    $arr = [];
    /**
     * Se valida la cantidad de campos enviados en la solicitud
     */
    if ($countData > $countFields || $countData < $countFields) {
      $arr = ['Los datos no cumplen con la cantidad de campos.', false];
      return $arr;
    }
    /**
     * Se valida si los campos enviados en el body están disponibles en la tabla.
     */
    foreach ($data as $key => $value) {
      if (!in_array($key, $fields)) {
        $arr = [$key . ' no es un campo disponible para la solicitud.', false];
        return $arr;
      }
    }
    return true;
  }
}
