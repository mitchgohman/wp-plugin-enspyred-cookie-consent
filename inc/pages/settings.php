<?php

function ecc_admin_settings_page() {
    $settings = ecc_get_settings();

    // Show any messages
    settings_errors('ecc_settings');
    ?>

    <form method="post" action="">
        <?php wp_nonce_field('ecc_settings', 'ecc_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="enabled">Enable Banner</label></th>
                <td>
                    <input type="checkbox" name="enabled" id="enabled" value="1" <?php checked($settings['enabled'] ?? true); ?>>
                    <p class="description">Display the cookie consent banner on all pages.</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="banner_heading">Banner Heading</label></th>
                <td>
                    <input type="text" name="banner_heading" id="banner_heading" value="<?php echo esc_attr($settings['banner_heading'] ?? ''); ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="banner_text">Banner Text</label></th>
                <td>
                    <textarea name="banner_text" id="banner_text" rows="4" class="large-text"><?php echo esc_textarea($settings['banner_text'] ?? ''); ?></textarea>
                    <p class="description">Main description text shown in the banner.</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="cookie_policy_text">Cookie Policy Link Text</label></th>
                <td>
                    <input type="text" name="cookie_policy_text" id="cookie_policy_text" value="<?php echo esc_attr($settings['cookie_policy_text'] ?? ''); ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="cookie_policy_url">Cookie Policy URL</label></th>
                <td>
                    <input type="text" name="cookie_policy_url" id="cookie_policy_url" value="<?php echo esc_attr($settings['cookie_policy_url'] ?? ''); ?>" class="regular-text">
                    <p class="description">Link to your cookie/privacy policy page.</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="accept_button_text">Accept Button Text</label></th>
                <td>
                    <input type="text" name="accept_button_text" id="accept_button_text" value="<?php echo esc_attr($settings['accept_button_text'] ?? ''); ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="opt_out_button_text">Opt Out Button Text</label></th>
                <td>
                    <input type="text" name="opt_out_button_text" id="opt_out_button_text" value="<?php echo esc_attr($settings['opt_out_button_text'] ?? ''); ?>" class="large-text">
                    <p class="description">Text for the CCPA-compliant opt-out button.</p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" class="button button-primary" value="Save Settings">
        </p>
    </form>

    <hr style="margin: 40px 0;">

    <h2>How to Use</h2>
    <p>The cookie consent banner will automatically appear at the bottom of all pages when enabled. It will:</p>
    <ul style="list-style: disc; margin-left: 20px;">
        <li>Show to new visitors automatically</li>
        <li>Hide after user accepts or opts out</li>
        <li>Store the user's choice for 12 months</li>
        <li>Block Google Analytics, Google Ads, and Microsoft Clarity if user opts out</li>
        <li>Keep functional cookies (reCAPTCHA, Google Maps) always active</li>
    </ul>

    <?php
}
