

CRM callcenter
============================

Aplicación para el manejo de los clientes de un callcenter de encuestas masivas


ESTRUCTURA DE DIRECTORIOS
-------------------

      config / contiene configuraciones de aplicaciones
      controllers / contiene clases de controlador web
      mail / contiene archivos de vista para correos electrónicos
      models / contiene modelos de las clases
      vendor / contiene paquetes dependientes de terceros 
      views / contiene archivos de vista para la aplicación web
      web / contiene el script de entrada y recursos web


REQUISITOS
------------

El requisito mínimo de esta plantilla de proyecto es que su servidor web sea compatible con PHP 5.4.0.




INSTALACIÓN
------------

### Instalar desde un archivo comprimido

Extraiga el archivo comprimido descargado de [yiiframework.com](http://www.yiiframework.com/download/) para un directorio llamado `[carpeta de instalación]` que está directamente debajo de la raíz Web.

Extraiga el archivo comprimido crm_callcenter en el directorio llamado `[carpeta de instalación]`.

Establezca la clave de validación de cookies en el archivo `config/web.php` en alguna cadena secreta aleatoria:

`` `php
'request' => [
    // !!! inserte una clave secreta en la siguiente (si está vacía) - esto es requerido por la validación de cookies
   'cookieValidationKey' => '<la cadena aleatoria secreta va aquí>',
]
`` `

A continuación, puede acceder a la aplicación a través de la siguiente URL:

~~~
http://localhost/[carpeta de instalación]
~~~



CONFIGURACIÓN
-------------

### Base de datos

Edite el archivo `config/db.php` con datos reales, por ejemplo:

`` `php
regreso [
    'class' => 'yii \ db \ Connection',
    'dsn' => 'mysql: host = localhost; dbname = crm_yyymmdd',
    'username' => 'root',
    'contraseña' => '1234',
    'charset' => 'utf8',
];
`` `

** NOTAS: **
- No se creara la base de datos automaticamente, esto debe hacerse manualmente antes de que se pueda acceder a ella.
- Verifique y edite los otros archivos en el directorio `config /` para personalizar su aplicación según sea necesario.
