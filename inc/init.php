<?php

// Plugin activation hook
register_activation_hook(dirname(__DIR__) . '/enspyred-cookie-consent.php', 'ecc_activate_plugin');

function ecc_activate_plugin() {
    ecc_seed_default_settings();
}

// Check for plugin updates on init
add_action('init', function () {
    $current_version = '1.0.0';
    $stored_version = get_option('ecc_plugin_version', '0.0.0');

    if (version_compare($stored_version, $current_version, '<')) {
        ecc_seed_default_settings();
        update_option('ecc_plugin_version', $current_version, false);
    }
});

function ecc_seed_default_settings() {
    $default_settings = [
        'enabled' => true,
        'banner_heading' => 'We value your privacy',
        'banner_text' => 'This website or its third-party tools process personal data. You can opt out of the sale of your personal information by clicking on the "Reject Cookies" button.',
        'cookie_policy_text' => 'Cookie Policy',
        'cookie_policy_url' => '/privacy-policy',
        'accept_button_text' => 'Accept Cookies',
        'opt_out_button_text' => 'Reject Cookies'
    ];

    $existing_settings = get_option('ecc_settings');
    if (!$existing_settings) {
        update_option('ecc_settings', $default_settings, false);
    } else {
        // Merge with existing settings to add any new fields
        $merged = array_merge($default_settings, $existing_settings);
        update_option('ecc_settings', $merged, false);
    }
}
