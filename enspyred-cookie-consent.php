<?php
/*
Plugin Name: Enspyred Cookie Consent
Description: CCPA-compliant cookie consent banner with opt-out functionality for tracking scripts.
Version: 1.0.3
Author: Enspyred
Author URI: https://enspyred.com
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: enspyred-cookie-consent
*/

// Plugin Update Checker
require plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5p6\PucFactory;

$enspyredCookieConsentUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/enspyred/wp-plugin-enspyred-cookie-consent',
	__FILE__,
	'enspyred-cookie-consent'
);
$enspyredCookieConsentUpdateChecker->getVcsApi()->enableReleaseAssets();

// general
require_once plugin_dir_path(__FILE__) . 'inc/helpers.php';
require_once plugin_dir_path(__FILE__) . 'inc/router.php';
require_once plugin_dir_path(__FILE__) . 'inc/menus.php';
require_once plugin_dir_path(__FILE__) . 'inc/shortcodes.php';

// tools
require_once plugin_dir_path(__FILE__) . 'inc/early-blocker.php';
require_once plugin_dir_path(__FILE__) . 'inc/react.php';

// pages
require_once plugin_dir_path(__FILE__) . 'inc/pages/settings.php';

// start her up
require_once plugin_dir_path(__FILE__) . 'inc/init.php';
