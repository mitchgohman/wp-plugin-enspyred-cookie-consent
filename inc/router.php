<?php

// Router for Cookie Consent settings
function ecc_admin_settings_router() {
    // Handle form submissions
    if ($_POST && isset($_POST['ecc_nonce']) && wp_verify_nonce(wp_unslash($_POST['ecc_nonce']), 'ecc_settings')) {
        ecc_handle_settings_submission();
    }

    // Display settings page
    echo '<div class="wrap">';
    echo '<h1>Cookie Consent Settings</h1>';
    ecc_admin_settings_page();
    echo '</div>';
}

// Handle settings form submission
function ecc_handle_settings_submission() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $settings = [
        'enabled' => !empty($_POST['enabled']),
        'banner_heading' => isset($_POST['banner_heading']) ? sanitize_text_field(wp_unslash($_POST['banner_heading'])) : '',
        'banner_text' => isset($_POST['banner_text']) ? sanitize_textarea_field(wp_unslash($_POST['banner_text'])) : '',
        'cookie_policy_text' => isset($_POST['cookie_policy_text']) ? sanitize_text_field(wp_unslash($_POST['cookie_policy_text'])) : '',
        'cookie_policy_url' => isset($_POST['cookie_policy_url']) ? esc_url_raw(wp_unslash($_POST['cookie_policy_url'])) : '',
        'accept_button_text' => isset($_POST['accept_button_text']) ? sanitize_text_field(wp_unslash($_POST['accept_button_text'])) : '',
        'opt_out_button_text' => isset($_POST['opt_out_button_text']) ? sanitize_text_field(wp_unslash($_POST['opt_out_button_text'])) : ''
    ];

    ecc_update_settings($settings);
    add_settings_error('ecc_settings', 'settings_updated', 'Settings saved successfully.', 'updated');
}
