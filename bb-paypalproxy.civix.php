<?php

// AUTO-GENERATED FILE -- Civix may overwrite any changes made to this file

/**
 * The ExtensionUtil class provides small stubs for accessing resources of this
 * extension.
 */
class CRM_Bbpaypalproxy_ExtensionUtil {
  const SHORT_NAME = 'bb-paypalproxy';
  const LONG_NAME = 'bb-paypalproxy';
  const CLASS_PREFIX = 'CRM_Bbpaypalproxy';

  /**
   * Translate a string using the extension's domain.
   */
  public static function ts($text, $params = []) {
    if (!array_key_exists('domain', $params)) {
      $params['domain'] = [self::LONG_NAME, NULL];
    }
    return ts($text, $params);
  }

  /**
   * Get the URL of a resource file (in this extension).
   */
  public static function url($file = NULL) {
    if ($file === NULL) {
      return rtrim(CRM_Core_Resources::singleton()->getUrl(self::LONG_NAME), '/');
    }
    return CRM_Core_Resources::singleton()->getUrl(self::LONG_NAME, $file);
  }

  /**
   * Get the path of a resource file (in this extension).
   */
  public static function path($file = NULL) {
    return __DIR__ . ($file === NULL ? '' : (DIRECTORY_SEPARATOR . $file));
  }

  /**
   * Get the name of a class within this extension.
   */
  public static function findClass($suffix) {
    return self::CLASS_PREFIX . '_' . str_replace('\\', '_', $suffix);
  }
}

use CRM_Bbpaypalproxy_ExtensionUtil as E;

/**
 * (Delegated) Implements hook_civicrm_config().
 */
function _bb_paypalproxy_civix_civicrm_config($config = NULL) {
  static $configured = FALSE;
  if ($configured) {
    return;
  }
  $configured = TRUE;

  $extRoot = __DIR__ . DIRECTORY_SEPARATOR;
  $include_path = $extRoot . PATH_SEPARATOR . get_include_path();
  set_include_path($include_path);
}

/**
 * Implements hook_civicrm_install().
 */
function _bb_paypalproxy_civix_civicrm_install() {
  _bb_paypalproxy_civix_civicrm_config();
}

/**
 * (Delegated) Implements hook_civicrm_enable().
 */
function _bb_paypalproxy_civix_civicrm_enable() {
  _bb_paypalproxy_civix_civicrm_config();
}

/**
 * (Delegated) Implements hook_civicrm_disable().
 */
function _bb_paypalproxy_civix_civicrm_disable() {
}

/**
 * (Delegated) Implements hook_civicrm_uninstall().
 */
function _bb_paypalproxy_civix_civicrm_uninstall() {
}
