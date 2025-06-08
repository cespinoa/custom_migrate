<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Database\Database;

/**
 * Source plugin for remote media images.
 *
 * @MigrateSource(
 *   id = "remote_media_image"
 * )
 */
class RemoteMediaImage extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    /** @var \Drupal\Core\Database\Connection $database */
    $database = \Drupal\Core\Database\Database::getConnection('default', 'remote');

    $query = $database->select('media_field_data', 'mfd');
    $query->fields('mfd', ['mid', 'bundle', 'name', 'langcode', 'uid']);
    $query->innerJoin('media__field_media_image', 'fmd_doc', 'fmd_doc.entity_id = mfd.mid');
    $query->innerJoin('file_managed', 'fmd', 'fmd.fid = fmd_doc.field_media_image_target_id');
    $query->addField('fmd', 'uri', 'file_uri');
    $query->condition('mfd.bundle', 'image');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'mid' => $this->t('Media ID'),
      'bundle' => $this->t('Bundle'),
      'name' => $this->t('Name'),
      'langcode' => $this->t('Language code'),
      'file_uri' => $this->t('File URI'),
      'uid'      => $this->t('Owner user ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'mid' => [
        'type' => 'integer',
      ],
    ];
  }

  public function initializeIterator() {
    $result = $this->query()->execute();
    $rows = [];
    foreach ($result as $row) {
      $rows[] = (array) $row;
    }

    \Drupal::logger('custom_migrate')->notice('initializeIterator encontró @count filas.', ['@count' => count($rows)]);

    return new \ArrayIterator($rows);
  }

  public function prepareRow(Row $row) {
    \Drupal::logger('custom_migrate')->notice('Procesando MID: @mid, URI: @uri', [
      '@mid' => $row->getSourceProperty('mid'),
      '@uri' => $row->getSourceProperty('file_uri'),
    ]);
    $uri = $row->getSourceProperty('file_uri');
    $local_destination = dirname($uri);
    $local_destination = str_replace('public://', '', $local_destination);

    // Asume que "public://" apunta a sites/default/files/
    $relative_path = str_replace('public://', '', $uri);
    $relative_path = implode('/', array_map('rawurlencode', explode('/', $relative_path)));


    $remote_url_base = 'http://carlosespino.local/sites/default/files/';

    $remote_url = $remote_url_base . $relative_path;
    $row->setSourceProperty('remote_file_url', $remote_url);
    $row->setSourceProperty('local_destination', $local_destination);
    return parent::prepareRow($row);
  }

}
