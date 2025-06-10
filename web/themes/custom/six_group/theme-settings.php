<?php

declare(strict_types=1);

/**
 * @file
 * Theme settings form for Six Group theme.
 */

use Drupal\Core\Form\FormState;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function six_group_form_system_theme_settings_alter(array &$form, FormState $form_state): void {

  $form['six_group'] = [
    '#type' => 'details',
    '#title' => t('Six Group'),
    '#open' => TRUE,
  ];

  $form['six_group']['example'] = [
    '#type' => 'textfield',
    '#title' => t('Example'),
    '#default_value' => theme_get_setting('example'),
  ];

}
