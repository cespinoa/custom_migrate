<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for user accounts from another Drupal 11 site.
 *
 * @MigrateSource(
 *   id = "embedded_user"
 * )
 */
class EmbeddedUser extends SqlBase {

  public function query() {
    return $this->select('users_field_data', 'u')
      ->fields('u', [
        'uid',
        'name',
        'mail',
        'status',
        'created',
        'access',
        'login',
        'timezone',
      ])
      ->condition('uid', 0, '>');
  }

  public function fields() {
    return [
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'mail' => $this->t('Email'),
      'status' => $this->t('Status'),
      'created' => $this->t('Created timestamp'),
      'access' => $this->t('Last access timestamp'),
      'login' => $this->t('Last login timestamp'),
      'timezone' => $this->t('Time zone'),
      'roles' => $this->t('User roles'),
    ];
  }

  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'u',
      ],
    ];
  }

  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');

    $roles = $this->select('user__roles', 'r')
      ->fields('r', ['roles_target_id'])
      ->condition('entity_id', $uid)
      ->execute()
      ->fetchCol();

    // Asegurarse de que es un array.
    $row->setSourceProperty('roles', is_array($roles) ? $roles : []);

    return parent::prepareRow($row);
  }
}
