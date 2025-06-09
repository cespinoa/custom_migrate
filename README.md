# Custom Migrate

Módulo personalizado para migraciones en Drupal 11, que incluye la importación de usuarios, medios, taxonomías y nodos.

---

## Descripción

Este módulo facilita la migración de datos desde una base de datos remota, incluyendo:

- Usuarios
- Media (Imagen, Documento, Video Remoto)
- Taxonomías (ej. tipo_de_articulo)
- Nodos (artículos con campos personalizados)

---

## Instalación

1. Coloca el módulo en `web/modules/custom/custom_migrate`.
2. Activa el módulo mediante la interfaz de administración o con Drush:

    ```bash
    drush en custom_migrate -y
    ```

3. Configura las migraciones si es necesario.

---

## Uso

Para ejecutar las migraciones completas:


drush migrate:import --all

O migrar por separado:

    ```bash
    drush migrate:import user
    drush migrate:import media_image
    drush migrate:import media_document
    drush migrate:import media_remote_video
    drush migrate:import taxonomy_tipo_de_articulo
    drush migrate:import article
    ```
    

## Dependencias

- Drupal 11

- Base de datos remota configurada en settings.php como remote

- Módulo Migrate y Migrate Plus activados

## Configuración de la base de datos
En web/sites/default/settings.php se debe incluir la configuración de la base de datos remota:

```bash
$databases['remote']['default'] = [
  'database' => 'database_name',
  'username' => 'database_user',
  'password' => 'user_password',
  'prefix' => '',
  'host' => '127.0.0.1',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];
```
El nombre 'remote' debe coincidir con el utilizado en los ficheros yaml situados en config/install dentro del módulo, bajo las claves source: y key:

```bash
id: taxonomy_tipo_de_articulo
label: 'Taxonomía - Tipo de artículo'
migration_group: custom_migrate

source:
  plugin: remote_taxonomy_term__tipo_de_articulo
  key: remote
];
```
## Arquitectura
Para cada elemento a importar (user, article, media_image...) disponemos de dos ficheros.
El fichero yaml que configura la importación siempre es requrido, ya que es el utilizado por el sistema de migración de Drupal para controlar cómo se realiza.
En este caso, como la importación se realiza contra una base de datos de Drupal 11 y los diferentes items importados tienen campos personalizados, en el fichero yaml se define el plugin a utilizar:
```bash
source:
  plugin: remote_taxonomy_term__tipo_de_articulo
  ```

El plugin personalizado se coloca dentro del módulo en la ruta src/Plugin/migrate/source/ y en él se recuperan los datos que serán transferidos al sistema de migración de Drupal.
