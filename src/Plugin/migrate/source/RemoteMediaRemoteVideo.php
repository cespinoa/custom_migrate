<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Database\Database;
use Drupal\migrate\Annotation\MigrateSource;


/**
 * Source plugin for remote media remote videos.
 *
 * @MigrateSource(
 *   id = "remote_media_rv"
 * )
 */
class RemoteMediaRemoteVideo extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $database = \Drupal\Core\Database\Database::getConnection('default', 'remote');

    $query = $database->select('media_field_data', 'mfd');
    // Campos base
    $query->fields('mfd', ['mid', 'bundle', 'name', 'langcode', 'uid']);
    // Unimos solo la tabla de oEmbed (sin file_managed)
    $query->innerJoin(
      'media__field_media_oembed_video',
      'fmd_remote_video',
      'fmd_remote_video.entity_id = mfd.mid'
    );
    // Tomamos la URL directamente del valor del campo
    $query->addField('fmd_remote_video', 'field_media_oembed_video_value', 'video_url');
    // Filtramos solo el bundle remote_video
    $query->condition('mfd.bundle', 'remote_video');

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

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Solo para debug, puedes quitarlo una vez funcione.
    \Drupal::logger('custom_migrate')->notice('Procesando MID: @mid, video_url: @url', [
      '@mid' => $row->getSourceProperty('mid'),
      '@url' => $row->getSourceProperty('video_url'),
    ]);

    // El campo video_url ya viene de query() desde media__field_media_oembed_video.
    // Si quisieras normalizar o validar la URL, podrías hacerlo aquí:
    $video_url = trim($row->getSourceProperty('video_url'));
    $row->setSourceProperty('video_url', $video_url);

    return parent::prepareRow($row);
  }


}
