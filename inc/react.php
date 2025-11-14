<?php
// React Scripts - Auto-loads banner on all frontend pages
function ecc_enqueue_react_assets() {
    // Only load on frontend
    if (is_admin()) {
        return;
    }

    try {
        $plugin_file = dirname(__DIR__) . '/enspyred-cookie-consent.php';
        $base = plugin_dir_path($plugin_file);
        $uri  = plugin_dir_url($plugin_file);

        $manifest_path = $base . '/build/.vite/manifest.json';
        if ( ! file_exists($manifest_path) ) {
            enspyred_log('React: Manifest missing at ' . $manifest_path);
            return;
        }

        $manifest_raw = file_get_contents($manifest_path);
        $manifest = json_decode($manifest_raw, true);
        if ( ! is_array($manifest) ) {
            enspyred_log('React: Manifest invalid JSON');
            return;
        }

        $entries = array_values(array_filter($manifest, fn($v) => !empty($v['isEntry'])));
        if ( empty($entries) ) {
            enspyred_log('React: No entry found in manifest');
            return;
        }

        $entry = $entries[0];
        wp_enqueue_script(
            'ecc-app',
            $uri . 'build/' . $entry['file'],
            [],
            null,
            [ 'in_footer' => true, 'type' => 'module' ]
        );

        if (!empty($entry['css'])) {
            foreach ($entry['css'] as $css) {
                wp_enqueue_style('ecc-style-' . md5($css), $uri . 'build/' . $css, [], null);
            }
        }

        // Get settings and pass to JS
        $settings = ecc_get_settings();

        wp_localize_script('ecc-app', 'ECC_DATA', [
            'settings' => $settings,
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    } catch (Throwable $e) {
        enspyred_log('React: Enqueue error - ' . $e->getMessage());
    }
}

add_action('wp_enqueue_scripts', 'ecc_enqueue_react_assets');

// Enqueue opt-out link handler on all frontend pages
// (It's tiny - 0.4kb gzipped - so no need for conditional loading)
function ecc_enqueue_optout_handler() {
    // Only load on frontend
    if (is_admin()) {
        return;
    }

    try {
        $plugin_file = dirname(__DIR__) . '/enspyred-cookie-consent.php';
        $base = plugin_dir_path($plugin_file);
        $uri  = plugin_dir_url($plugin_file);

        $manifest_path = $base . '/build/.vite/manifest.json';
        if ( ! file_exists($manifest_path) ) {
            return;
        }

        $manifest_raw = file_get_contents($manifest_path);
        $manifest = json_decode($manifest_raw, true);
        if ( ! is_array($manifest) ) {
            return;
        }

        // Find the optout entry
        if (isset($manifest['src/optOutLink.js'])) {
            $entry = $manifest['src/optOutLink.js'];
            wp_enqueue_script(
                'ecc-optout',
                $uri . 'build/' . $entry['file'],
                [],
                null,
                [ 'in_footer' => true, 'type' => 'module' ]
            );
        }
    } catch (Throwable $e) {
        enspyred_log('React: Optout enqueue error - ' . $e->getMessage());
    }
}

add_action('wp_enqueue_scripts', 'ecc_enqueue_optout_handler');

// Add mount point in footer
add_action('wp_footer', function() {
    $settings = ecc_get_settings();
    if (!empty($settings['enabled'])) {
        echo '<div id="enspyred-cookie-consent-root"></div>';
    }
}, 9999);

// Force module type on our app bundles
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle === 'ecc-app' || $handle === 'ecc-optout') {
        if (strpos($tag, ' type=') === false) {
            $tag = str_replace('<script ', '<script type="module" ', $tag);
        } else {
            $tag = preg_replace('/type=("|\')text\/javascript\1/i', 'type="module"', $tag);
        }
    }
    return $tag;
}, 10, 3);
