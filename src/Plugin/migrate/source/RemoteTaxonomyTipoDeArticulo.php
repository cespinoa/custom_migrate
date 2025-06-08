<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin para la taxonomía tipo_de_articulo.
 *
 * @MigrateSource(
 *   id = "remote_taxonomy_term__tipo_de_articulo"
 * )
 */
class RemoteTaxonomyTipoDeArticulo extends SqlBase {

  public function query() {
    $database = \Drupal\Core\Database\Database::getConnection('default', 'remote');

    $query = $database->select('taxonomy_term_field_data', 't');
    $query->fields('t', ['tid', 'vid', 'name', 'description__value', 'langcode', 'changed', 'status']);
    $query->condition('vid', 'tipo_de_articulo');

    // Join campos personalizados.
    $query->leftJoin('taxonomy_term__field_cuerpo', 'fc', 'fc.entity_id = t.tid');
    $query->leftJoin('taxonomy_term__field_display_edge_to_edge', 'fdee', 'fdee.entity_id = t.tid');
    $query->leftJoin('taxonomy_term__field_hide_breadcrumb', 'fhb', 'fhb.entity_id = t.tid');
    $query->leftJoin('taxonomy_term__field_hide_hide_sidebars', 'fhs', 'fhs.entity_id = t.tid');
    $query->leftJoin('taxonomy_term__field_hide_sidebars', 'fhsb', 'fhsb.entity_id = t.tid');
    $query->leftJoin('taxonomy_term__field_hide_title', 'fht', 'fht.entity_id = t.tid');
    $query->leftJoin('taxonomy_term__field_imagen', 'fi', 'fi.entity_id = t.tid');
    $query->leftJoin('taxonomy_term__field_custom_library', 'fcl', 'fcl.entity_id = t.tid');

    $query->addField('fc', 'field_cuerpo_value', 'field_cuerpo');
    $query->addField('fdee', 'field_display_edge_to_edge_value', 'display_edge_to_edge');
    $query->addField('fhb', 'field_hide_breadcrumb_value', 'hide_breadcrumb');
    $query->addField('fhs', 'field_hide_hide_sidebars_value', 'hide_hide_sidebars');
    $query->addField('fhsb', 'field_hide_sidebars_value', 'hide_sidebars');
    $query->addField('fht', 'field_hide_title_value', 'hide_title');
    $query->addField('fi', 'field_imagen_target_id', 'media_image_id');
    $query->addField('fcl', 'field_custom_library_value', 'custom_library');

    return $query;
  }

  public function fields() {
    return [
      'tid' => $this->t('Term ID'),
      'name' => $this->t('Name'),
      'langcode' => $this->t('Language code'),
      'field_cuerpo' => $this->t('Texto largo'),
      'display_edge_to_edge' => $this->t('Edge to edge'),
      'hide_breadcrumb' => $this->t('Ocultar breadcrumb'),
      'hide_hide_sidebars' => $this->t('Ocultar sidebars doble'),
      'hide_sidebars' => $this->t('Ocultar sidebars'),
      'hide_title' => $this->t('Ocultar título'),
      'media_image_id' => $this->t('ID imagen (media)'),
      'custom_library' => $this->t('Librería asociada'),
    ];
  }

  public function getIds() {
    return [
      'tid' => [
        'type' => 'integer',
        'alias' => 't',
      ],
    ];
  }
}
