<?php

namespace Drupal\example_module\Services;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Service description.
 */
class ExampleService
{

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an ExampleServices object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   */
  public function __construct(Connection $connection, EntityTypeManagerInterface $entity_type_manager)
  {
    $this->connection = $connection;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Función para obtener los usuarios
   */

  public function getUsers($id)
  {

    $query = $this->connection->select('example_users', 'u')
      ->fields('u', ['id', 'name', 'identification', 'birthdate', 'position_id', 'status']);
    $query->condition('id', $id, '=');
    $result = $query->execute();
    if ($result) {
      $data = $result->fetchAssoc();
      return $data;
    }
  }

  /**
   * Funcion para obtener todos los usuarios.
   */
  public function getAllUsers()
  {

    $query = $this->connection->select('example_users', 'u')
      ->fields('u', ['id', 'name', 'identification', 'birthdate', 'position_id', 'status']);
    $result = $query->execute();
    if ($result) {
      $data = $result->fetchAll();
      return $data;
    }
  }

  /**
   * Función para guardar los usuarios ingresados en el form.
   */
  public function saveUser($name, $identification, $birthdate, $position)
  {
    /**
     * Se valida el cargo para guardar el estado
     */
    $status = ($position == 1) ? 1 : 0;

    $data = [
      'name' => $name,
      'identification' => $identification,
      'birthdate' => $birthdate,
      'position_id' => $position,
      'status' => $status
    ];
    /**
     * Se ejecuta el query de insert para la base de datos
     */
    $query = $this->connection->insert('example_users')->fields($data)->execute();
    if ($query) {
      return 'Los datos se registraron correctamente.';
    }
  }

  /**
   * Funcion de actualizar el estado
   */

  public function updateUser($id, $name, $identification, $birthdate, $position)
  {
    /**
     * Se valida el cargo para guardar el estado
     */
    $status = ($position == 1) ? 1 : 0;
    $data = [
      'name' => $name,
      'identification' => $identification,
      'birthdate' => $birthdate,
      'position_id' => $position,
      'status' => $status
    ];
    /**
     * Se ejecuta el query de update para la base de datos
     *
     */
    $query = $this->connection->update('example_users')->fields($data)->condition('id', $id)->execute();
    if ($query) {
      return 'Los datos se actualizaron correctamente.';
    }
  }
  /**
   * Función para eliminar un usuario
   *
   */
  public function deleteUser($id)
  {

    $delete = $this->connection->delete('example_users')->condition('id', $id)->execute();
    if ($delete) {
      return 'Usuario eliminado.';
    }
  }
}
