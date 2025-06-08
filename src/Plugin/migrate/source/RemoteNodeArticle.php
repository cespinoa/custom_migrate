<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * Source plugin for remote article nodes.
 *
 * @MigrateSource(
 *   id = "remote_node_article"
 * )
 */
class RemoteNodeArticle extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $database = Database::getConnection('default', 'remote');

    $query = $database->select('node_field_data', 'nfd');
    $query->fields('nfd', ['nid', 'vid', 'type', 'langcode', 'status', 'uid', 'title', 'created', 'changed']);

    $query->leftJoin('node__body', 'b', 'b.entity_id = nfd.nid');
    $query->leftJoin('node__field_display_edge_to_edge', 'dee', 'dee.entity_id = nfd.nid');
    $query->leftJoin('node__field_hide_breadcrumb', 'hb', 'hb.entity_id = nfd.nid');
    $query->leftJoin('node__field_hide_sidebars', 'hs', 'hs.entity_id = nfd.nid');
    $query->leftJoin('node__field_hide_title', 'ht', 'ht.entity_id = nfd.nid');
    $query->leftJoin('node__field_image', 'fi', 'fi.entity_id = nfd.nid');
    $query->leftJoin('node__field_custom_library', 'cl', 'cl.entity_id = nfd.nid');
    $query->leftJoin('node_revision__field_tipo_de_articulo', 'fta', 'fta.revision_id = nfd.vid');


    $query->addField('b', 'body_value', 'body');
    $query->addField('b', 'body_summary', 'body_summary');
    $query->addField('dee', 'field_display_edge_to_edge_value', 'display_edge_to_edge');
    $query->addField('hb', 'field_hide_breadcrumb_value', 'hide_breadcrumb');
    $query->addField('hs', 'field_hide_sidebars_value', 'hide_sidebars');
    $query->addField('ht', 'field_hide_title_value', 'hide_title');
    $query->addField('fi', 'field_image_target_id', 'image');
    $query->addField('cl', 'field_custom_library_value', 'custom_library');
    $query->addField('fta', 'field_tipo_de_articulo_target_id', 'tipo_de_articulo');

    $query->condition('nfd.type', 'article');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'title' => $this->t('Title'),
      'uid' => $this->t('Author user ID'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Changed timestamp'),
      'body' => $this->t('Body text'),
      'body_summary' => $this->t('Body summary'),
      'display_edge_to_edge' => $this->t('Display edge to edge'),
      'hide_breadcrumb' => $this->t('Hide breadcrumb'),
      'hide_sidebars' => $this->t('Hide sidebars'),
      'hide_title' => $this->t('Hide title'),
      'image' => $this->t('Image media ID'),
      'custom_library' => $this->t('Custom library'),
      'tipo_de_articulo' => $this->t('Tipo de artículo term ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'nfd',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $tipo = $row->getSourceProperty('tipo_de_articulo');
    \Drupal::logger('custom_migrate')->notice('Tipo de Artículo para nid @nid: @tipo', [
      '@nid' => $row->getSourceProperty('nid'),
      '@tipo' => $tipo,
    ]);
    return parent::prepareRow($row);
  }

}
