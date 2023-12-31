
# example_module
Creación de modulo para prueba técnica.

## Desplegar el proyecto

Para desplegar el proyecto, puedes utilizar cualquier herramienta o servidor en el que puedas instalar los siguientes requisitos:

- PHP: [Instrucciones de instalación](https://www.php.net/manual/en/install.php)
- Composer: [Instrucciones de instalación](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) para la instalación de drupal y la admnistración de sus dependencias

En este caso, utilizare lando para desplegar el proyecto:

### Lando
/** Para instalaciones desde cero de drupal con lando validar la documentación*/

Lando es una herramienta que nos permite montar entornos de desarrollo local y facilita la configuración y gestión de contenedores Docker, lo que nos permite montar nuestro proyecto en cuestión de minutos.

Para instalar Lando, puedes seguir las indicaciones de la documentación oficial: [Instalación de Lando](https://docs.lando.dev/getting-started/installation.html)

Pasos para desplegar el proyecto:

1. Clonar el proyecto desde el repositorio.
`git clone https://github.com/jpcastro98/example_module.git`
2. Validar la configuración en el archivo `.lando.yml` y asegurarse de que sea correcta. En caso de ser necesario, crear el archivo con la configuración adecuada. Por ejemplo:
```
name: exampemodule
recipe: drupal9
config:
  webroot: web
  xdebug: true
  config:
    php: .vscode/php.ini

```

3. Ejecutar el comando `lando start` para iniciar el contenedor.
4. Ejecutar `lando composer install` para instalar las dependencias del proyecto.
5. Ejecutar el comando `lando info` para obtener las credenciales de la base de datos. Crear y configurar el archivo `$settings.local.php` para entornos locales basado en el `$settings.php` con las credenciales correspondientes.
6. Ejecutar `lando drush en example_module` y `lando drush en example_module_rest` para instalar los modulos, tener en cuenta las dependecias y si es necesario instalarlas antes de instalar el modulo.


Estos pasos te permitirán desplegar el proyecto utilizando Lando.


### Form:
/example-module/form
Mediante el APIForm  de drupal Se creo el formulario para registrar usuarios, el formulario funciona mediante el envió de ajax, también se creo un template twig custom para mostrar el formulario y se le aplicaron estilos.

Los datos registrados en este formulario se guardan en la tabla example_users que se crea cuando se instala el modulo.

### Data:
/example-module/data
Mediante un controlador vamos a mostrar en esta ruta los datos registrados en el formulario visitado anteriormente.

### Service:
El ExampleService, es el servicio que nos permite consultar y almacenar los datos en la base de datos.
### Cargos:
 Para los cargos al instalar el modulo mediante el hook_install se crea una taxonomía (Positions) en la cual también se crean 3 terminos por defecto que son los cargos relacionados en la prueba, esto con el fin de que sead administrables y se puedan agregar más cargos en un futuro.

### example_module_rest:
  Se creo un modulo REST, que tiene como dependencias los siguientes modulos que son necesarios instalar:
  - drupal/restui: [drupal/restui](https://www.drupal.org/project/restui)

  ```
    -example_module
    -rest
    -restui
    -user
  ```
si se cuenta con el modulo restui activar el api  ExampleRest en su configuración.
```
admin/config/services/rest/resource/example_module_rest/edit
 ```
Y activar sus permisos en :
 ```
admin/people/permissions#module-rest
 ```
la rutas para consultar la api es
 ```
 - GET,PATCH,DELETE
'/api/example-module-rest/{id}'.
 - POST
'/api/example-module-rest/{id}'.
 ```

Para hacer peticiones POST o PATCH sólo se aceptan los campos propuestos en la prueba ejemplo:

```
{
    'name':'Prueba',
    'identification':123456789,
    'birthdate': 1998-07-06',
    'position_id':'1'
}
  ```






