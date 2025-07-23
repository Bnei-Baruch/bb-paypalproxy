<?php

require_once 'bb-paypalproxy.civix.php';

/**
 * Implements hook_civicrm_config().
 */
function bb_paypalproxy_civicrm_config(&$config) {
    _bb_paypalproxy_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 */
function bb_paypalproxy_civicrm_install() {
    $params = array(
        'version' => 3,
        'name' => 'PayPalProxy',
        'title' => 'PayPal Proxy',
        'description' => 'PayPal Proxy Payment Processor - forwards all requests to PayPal_Standard',
        'class_name' => 'Payment_BbPayPalProxy',
        'billing_mode' => 'notify',
        'user_name_label' => 'Merchant Account Email',
        'password_label' => 'API Password',
        'signature_label' => 'Target PayPal Processor ID',
        'url_site_default' => 'https://www.paypal.com/',
        'url_api_default' => 'https://api-3t.paypal.com/',
        'url_recur_default' => 'https://www.paypal.com/',
        'url_button_default' => 'https://www.paypal.com/',
        'url_site_test_default' => 'https://www.sandbox.paypal.com/',
        'url_api_test_default' => 'https://api-3t.sandbox.paypal.com/',
        'url_recur_test_default' => 'https://www.sandbox.paypal.com/',
        'url_button_test_default' => 'https://www.sandbox.paypal.com/',
        'is_recur' => 1,
        'payment_type' => 1,
    );

    civicrm_api('PaymentProcessorType', 'create', $params);
    _bb_paypalproxy_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 */
function bb_paypalproxy_civicrm_uninstall() {
    $params = array(
        'version' => 3,
        'sequential' => 1,
        'name' => 'PayPalProxy',
    );
    $result = civicrm_api('PaymentProcessorType', 'get', $params);
    if ($result["count"] == 1) {
        $params = array(
            'version' => 3,
            'sequential' => 1,
            'id' => $result["id"],
        );
        civicrm_api('PaymentProcessorType', 'delete', $params);
    }
}

/**
 * Implements hook_civicrm_enable().
 */
function bb_paypalproxy_civicrm_enable() {
    _bb_paypalproxy_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 */
function bb_paypalproxy_civicrm_disable() {
    _bb_paypalproxy_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 */
function bb_paypalproxy_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
    return;
}

/**
 * Implements hook_civicrm_managed().
 */
function bb_paypalproxy_civicrm_managed(&$entities) {
}

/**
 * Implements hook_civicrm_caseTypes().
 */
function bb_paypalproxy_civicrm_caseTypes(&$caseTypes) {
}

/**
 * Implements hook_civicrm_angularModules().
 */
function bb_paypalproxy_civicrm_angularModules(&$angularModules) {
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 */
function bb_paypalproxy_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
}

