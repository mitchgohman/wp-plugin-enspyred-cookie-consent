<?php
/**
 * Debug Logging Helper
 */
if (!function_exists('enspyred_log')) {
    function enspyred_log($message) {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            file_put_contents('php://stderr', '[ECC] ' . $message . PHP_EOL);
            error_log('[ECC] ' . $message);
        }
    }
}

// Get cookie consent settings
function ecc_get_settings() {
    return get_option('ecc_settings', []);
}

// Update cookie consent settings
function ecc_update_settings($settings) {
    update_option('ecc_settings', $settings, false);
}
