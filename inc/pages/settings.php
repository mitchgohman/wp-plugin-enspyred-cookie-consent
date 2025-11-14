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
                    <p class="description">Link to your cookie policy page.</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="cookie_policy_new_window">Cookie Policy Link Behavior</label></th>
                <td>
                    <input type="checkbox" name="cookie_policy_new_window" id="cookie_policy_new_window" value="1" <?php checked($settings['cookie_policy_new_window'] ?? false); ?>>
                    <label for="cookie_policy_new_window">Open in new window</label>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="privacy_policy_text">Privacy Policy Link Text</label></th>
                <td>
                    <input type="text" name="privacy_policy_text" id="privacy_policy_text" value="<?php echo esc_attr($settings['privacy_policy_text'] ?? ''); ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="privacy_policy_url">Privacy Policy URL</label></th>
                <td>
                    <input type="text" name="privacy_policy_url" id="privacy_policy_url" value="<?php echo esc_attr($settings['privacy_policy_url'] ?? ''); ?>" class="regular-text">
                    <p class="description">Link to your privacy policy page. Leave blank to hide the privacy policy link.</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="privacy_policy_new_window">Privacy Policy Link Behavior</label></th>
                <td>
                    <input type="checkbox" name="privacy_policy_new_window" id="privacy_policy_new_window" value="1" <?php checked($settings['privacy_policy_new_window'] ?? false); ?>>
                    <label for="privacy_policy_new_window">Open in new window</label>
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

    <hr style="margin: 40px 0;">

    <h2>Shortcode Usage</h2>
    <p>You can add a CCPA-compliant "Do Not Sell" opt-out link anywhere on your site using the shortcode:</p>

    <h3>Basic Usage</h3>
    <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #0073aa;">[enspyred_cookie_optout]</pre>
    <p>This displays: <strong>Do Not Sell or Share My Personal Information</strong></p>

    <h3>Custom Text</h3>
    <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #0073aa;">[enspyred_cookie_optout text="Manage Cookie Preferences"]</pre>
    <p>This displays your custom text as a clickable link.</p>

    <h3>Custom Styling</h3>
    <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #0073aa;">[enspyred_cookie_optout class="my-custom-class another-class"]</pre>
    <p>Add CSS classes to style the link with your theme's styles.</p>

    <h3>Where to Add</h3>
    <p>You can add this shortcode to:</p>
    <ul style="list-style: disc; margin-left: 20px;">
        <li>Your privacy/cookie policy page</li>
        <li>Footer content or widgets</li>
        <li>Any page or post content</li>
        <li>Custom HTML blocks in the block editor</li>
    </ul>

    <p><strong>Note:</strong> The link is styled as a button with underline styling to maintain accessibility while looking like a standard text link.</p>

    <?php
}
