## Desplegar el proyecto

Para desplegar el proyecto, puedes utilizar cualquier herramienta o servidor en el que puedas instalar los siguientes requisitos:

- PHP: [Instrucciones de instalación](https://www.php.net/manual/en/install.php)
- Composer: [Instrucciones de instalación](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)

En este caso, utilizare lando para desplegar el proyecto:

### Lando

Lando es una herramienta que nos permite montar entornos de desarrollo local y facilita la configuración y gestión de contenedores Docker, lo que nos permite montar nuestro proyecto en cuestión de minutos.

Para instalar Lando, puedes seguir las indicaciones de la documentación oficial: [Instalación de Lando](https://docs.lando.dev/getting-started/installation.html)

Pasos para desplegar el proyecto:

1. Clonar el proyecto desde el repositorio.
`git clone https://github.com/jpcastro98/example_module.git`
3. Validar la configuración en el archivo `.lando.yml` y asegurarse de que sea correcta. En caso de ser necesario, crear el archivo con la configuración adecuada. Por ejemplo:
```
name: drupal9app
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
6. Ejecutar `lando drusn en example_module` y `lando drush en example_module_rest` para instalar los modulos, tener en cuenta las dependecias.
8. En la configuración del modulo restui "admin/config/services/rest" habilitar el api ExampleRest. 

Estos pasos te permitirán desplegar el proyecto utilizando Lando. 


### Form: 
/example-module/form
Se creo el formulario para registrar usuarios, el formulario funciona mediante el envió de ajax, también se creo un template twig custom para mostrar el formulario y se le aplicaron estilos.

### Data 
/example-module/data
Mediante esta ruta vamos a poder ver los datos registrados en el formulario visitado anteriormente.###Service:
El ExampleService, es el servicio que nos permite consultar y almacenar los datos en la base de datos. 
### Cargos
 Para los cargos al instalar el modulo mediante el hook_install se crea una taxonomía en la cual tamb#ién se crean 3 terminos por defecto que son los cargos relacionados en la prueba, 
 así mismo se crea la tabla base 'example_users' donde se van a almacenar los datos registrados en el formulario construido.

### example_module_rest:
  Se creo un modulo REST, que tiene como dependencias los siguientes modulos:
    -example_module
    -rest
    -restui
    -user
la ruta para consultar la api es '/api/example-module-rest/{id}'



