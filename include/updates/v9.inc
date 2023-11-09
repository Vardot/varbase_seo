<?php

/**
 * @file
 * Contains varbase_seo_update_9###(s) hook updates.
 */

use Vardot\Installer\ModuleInstallerFactory;

/**
 * Issue #3265632: Fix Errors for messing Enabled entity types and Safe tokens.
 *
 * On the URL aliases settings page.
 */
function varbase_seo_update_90001() {

  // Get editable pathauto settings.
  $pathauto_settings = \Drupal::service('config.factory')->getEditable('pathauto.settings');

  // Add enable entity types if it was notin the pathauto settings
  // in the active site configs.
  $enabled_entity_types = $pathauto_settings->get('enabled_entity_types');
  if (!isset($enabled_entity_types)
    || (isset($enabled_entity_types) && is_array($enabled_entity_types) && !(count($enabled_entity_types) > 0))) {
    $pathauto_settings->set('enabled_entity_types', ['user'])->save(TRUE);
  }

  // Add the list of safe tokens if it was not in the pathauto
  // settings in the active site configs.
  $safe_tokens = $pathauto_settings->get('safe_tokens');
  if (!isset($safe_tokens)
    || (isset($safe_tokens) && is_array($safe_tokens) && !(count($safe_tokens) > 0))) {

    $safe_tokens = [
      'alias',
      'path',
      'join-path',
      'login-url',
      'url',
      'url-brief',
    ];
    $pathauto_settings->set('safe_tokens', $safe_tokens)->save(TRUE);
  }

}

/**
 * Issue #3272296: Switch to use the Module Installer Factory.
 *
 * In the Varbase SEO module.
 */
function varbase_seo_update_90002() {
  // Set the weight of the module after installation of list of modules.
  // To make sure that any hook or event subscriber worker
  // after all used modules.
  ModuleInstallerFactory::setModuleWeightAfterInstallation('varbase_seo');
}
