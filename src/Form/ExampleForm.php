<?php

namespace Drupal\example_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\example_module\Services\ExampleService;

/**
 * Provides a Example module form.
 */
class ExampleForm extends FormBase
{

  /**
   * Manages entity type plugin definitions.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  public $entityTypeManager;
  /**
   * Service Example.
   *
   * @var Drupal\example_module\Services\ExampleService;
   */
  public $exampleService;

  public function __construct(EntityTypeManager $entity_type_manager, ExampleService $example_service)
  {
    $this->entityTypeManager = $entity_type_manager;
    $this->exampleService = $example_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('example_module.service')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'example_module_example';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#maxlength' => 100,
      '#required' => TRUE,
      '#description' => t('Sólo caracteres alfanuméricos.'),

    ];

    $form['identification'] = [
      '#type' => 'number',
      '#attributes' => [
        ' type' => 'number',
      ],
      '#title' => t('Identificación'),
      '#description' => t('Cantidad de números: (min:6,max:12).'),
      '#min' => 0,
      '#maxlength' => 12,
      '#required' => TRUE
    ];

    $form['birthdate'] = [
      '#type' => 'date',
      '#title' => $this->t('Fecha de nacimiento'),
      '#description' => $this->t('Ingresa tu fecha de nacimiento.'),
      '#required' => TRUE,
      '#attributes' => [
        'max' => date('Y-m-d', strtotime('-18 years')),
        'class' => ['readonly-date-field']
      ],
    ];

    $form['position_id'] = [
      '#type' => 'select',
      '#options' => $this->getPositions(),
      '#title' => t('Cargo'),
      '#required' => TRUE,
      '#description' => $this->t('Elige el cargo.'),

    ];

    $form['output'] = [
      '#type' => 'markup',
      '#prefix' => '<div id="edit-output">',
      '#suffix' => '</div>',
    ];


    $form['submit_custom'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar'),
      '#ajax' => [
        'callback' => '::submitCustomForm',
        'wrapper' => 'edit-output',
        'progress' => [
          'type' => 'throbber',
        ]
      ]
    ];

    $form['#theme'] = 'example-add-form';
    $form['#attached']['library'][] = 'example_module/example_module';
    // Placeholder to put the result of Ajax call, setMessage().

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValues();
    /**
     * Se hace llamado a la función creada para validar el nombre
     */
    $this->validateName($values['name'], $form_state);
    /**
     * Se hace llamado a la función creada para validar la identificación
     */
    $this->validateIdentification($values['identification'], $form_state);
  }
  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * Función para ajax
   */
  public function submitCustomForm(array &$form, FormStateInterface $form_state)
  {
    /**
     * Se valida si la validación retorna errores si no se continua con el proceso.
     */
    if ($form_state->hasAnyErrors()) {
      $output = "<div id='edit-output'>No se registraron los datos.</div>";
    } else {
      /**
       * Se obtienen los datos del formulario y se guardan en variables.
       */
      $name = $form_state->getValue('name');
      $identification = $form_state->getValue('identification');
      $birthdate = $form_state->getValue('birthdate');
      $position_id = $form_state->getValue('position_id');
      /**
       * Se valida si ya existe un usuario con está identificación si no se guadar en la base de datos.
       */
      if (!$this->exampleService->getIdentification($identification)) {
        /**
         * Guarda los datos en la tabla example_users.
         */
        $markup = $this->exampleService->saveUser($name, $identification, $birthdate, $position_id);
        $output = "<div id='edit-output'>$markup</div>";
      } else {
        $output = "<div id='edit-output'>El usuario con identificación $identification ya se encuentra registrado.</div>";

      }
    }
    return ['#markup' => $output];
  }
  /**
   * Función para obtener los cargos.
   * */

  public function getPositions(): array
  {
    /**
     * Se muestran todos los cargos disponibles.
     * */
    $terms = $this->entityTypeManager->getStorage('taxonomy_term');
    $position = $terms->loadTree('positions');
    $arr = [];
    foreach ($position as $role) {
      $arr[$role->tid] = $role->name;
    }
    return $arr;
  }
  /**
   * Función para validar el nombre.
   */
  public function validateName($name, $form_state)
  {
    if (!preg_match('/^[a-zA-Z0-9\s]*$/', $name)) {
      return $form_state->setErrorByName('name', t('El campo nombre solo puede contener caracteres alfanuméricos y espacios.'));
    }
  }

  /**
   * Función para validar la identificación.
   */
  public function validateIdentification($identification, $form_state)
  {
    /**
     * Valida la cantidad de caracteres.
     */
    $length  = strlen(strval($identification));
    if ($length < 6 || $length > 12) {
      return $form_state->setErrorByName('identification', t('El campo identificación debe contener entre 6 y 12 caracteres.'));
    }
    /**
     * Valida que el campo sólo contenga caracteres numéricos.
     */
    $number = (int)$identification;
    if (!preg_match('/^[0-9]+$/', (int)$number)) {
      return $form_state->setErrorByName('identification', t('El campo identificación sólo debe contener caracteres numéricos.'));
    }
  }
}
