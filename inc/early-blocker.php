<?php
/**
 * Early Blocker - Prevents tracking scripts from loading for opted-out users
 *
 * TWO-LAYER APPROACH:
 * 1. PHP output buffering - Strips Clarity scripts from HTML before browser receives it
 * 2. JavaScript flags - Sets ga-disable-* flags for Google Analytics
 *
 * The PHP layer reads the 'ecc_opted_out' cookie (set by JavaScript when user opts out)
 * to determine whether to strip tracking scripts server-side.
 */

// Cookie name must match the one set in consent.js
define('ECC_OPTOUT_COOKIE', 'ecc_opted_out');

/**
 * Check if user has opted out (via cookie)
 */
function ecc_is_opted_out() {
    return isset($_COOKIE[ECC_OPTOUT_COOKIE]) && $_COOKIE[ECC_OPTOUT_COOKIE] === '1';
}

/**
 * Start output buffering early to capture and modify HTML
 * Only buffers if user has opted out
 */
add_action('template_redirect', 'ecc_start_output_buffer', 1);

function ecc_start_output_buffer() {
    if (is_admin()) {
        return;
    }

    // Only buffer if user opted out - we'll need to strip scripts
    if (ecc_is_opted_out()) {
        ob_start('ecc_filter_tracking_scripts');
    }
}

/**
 * Filter callback - removes tracking scripts from HTML output
 */
function ecc_filter_tracking_scripts($html) {
    if (empty($html)) {
        return $html;
    }

    $settings = ecc_get_settings();
    $debug_enabled = !empty($settings['debug_mode']);

    // Pattern to match Microsoft Clarity scripts
    // Matches both external scripts (clarity.ms) and inline initialization
    $patterns = [
        // External Clarity script
        '/<script[^>]*src=["\'][^"\']*clarity\.ms[^"\']*["\'][^>]*>.*?<\/script>/is',
        // Inline Clarity initialization (window.clarity or clarity())
        '/<script[^>]*>(?=[^<]*(?:clarity\.ms|window\.clarity\s*=|clarity\s*\(\s*["\']|function\s+clarity\s*\()).*?<\/script>/is',
    ];

    $removed_count = 0;
    foreach ($patterns as $pattern) {
        $html = preg_replace($pattern, '<!-- ECC: Clarity script blocked -->', $html, -1, $count);
        $removed_count += $count;
    }

    // Add debug comment if enabled
    if ($debug_enabled && $removed_count > 0) {
        $html = str_replace('</head>', "<!-- ECC Debug: Removed {$removed_count} Clarity script(s) -->\n</head>", $html);
    }

    return $html;
}

/**
 * Inject JavaScript for GA blocking and cookie management
 * Runs in wp_head with highest priority
 */
add_action('wp_head', 'ecc_inject_early_blocker', 1);

function ecc_inject_early_blocker() {
    if (is_admin()) {
        return;
    }

    $settings = ecc_get_settings();
    $debug_enabled = !empty($settings['debug_mode']);

    ?>
    <script>
    (function() {
        'use strict';

        var ECC_DEBUG = <?php echo json_encode($debug_enabled); ?>;
        var ECC_COOKIE_NAME = '<?php echo ECC_OPTOUT_COOKIE; ?>';

        function log(msg) {
            if (ECC_DEBUG) console.log('[Cookie Consent] ' + msg);
        }

        /**
         * Set the opt-out cookie (for PHP to read on next page load)
         * This is also called from consent.js, but we check here too
         * in case localStorage has opt-out but cookie is missing
         */
        function syncCookie(optedOut) {
            if (optedOut) {
                var expires = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
                document.cookie = ECC_COOKIE_NAME + '=1; expires=' + expires + '; path=/; SameSite=Lax';
            } else {
                document.cookie = ECC_COOKIE_NAME + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            }
        }

        // Check localStorage for opt-out status
        try {
            var consentData = localStorage.getItem('enspyred_cookie_consent');
            if (!consentData) return;

            var consent = JSON.parse(consentData);

            // Check if expired
            if (consent.expiresAt && Date.now() > consent.expiresAt) {
                localStorage.removeItem('enspyred_cookie_consent');
                syncCookie(false);
                return;
            }

            // Only block if opted out
            if (consent.choice !== 'opted-out') {
                syncCookie(false);
                return;
            }

            log('User opted out - blocking tracking');

            // Ensure cookie is set for PHP (in case it was cleared)
            syncCookie(true);

            // === GOOGLE ANALYTICS (Safe approach using official flags) ===

            // Official GA disable flags - these are supported by Google
            window['ga-disable'] = true;

            // GA4 specific disable (catches G-XXXXXXX properties)
            window['ga-disable-G-'] = true;

            // Delete existing GA cookies
            var gaCookies = ['_ga', '_gid', '_gat', '_gat_gtag', '_gac_gb'];
            deleteCookies(gaCookies);

            // === MICROSOFT CLARITY ===
            // Clarity scripts are stripped server-side via PHP output buffering
            // But we still delete cookies and set up observer for any dynamic scripts

            // Delete existing Clarity cookies
            var clarityCookies = ['_clck', '_clsk', 'CLID', 'ANONCHK', 'MR', 'MUID', 'SM'];
            deleteCookies(clarityCookies);

            // MutationObserver as backup for dynamically-injected scripts
            var clarityObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.tagName === 'SCRIPT') {
                            var src = node.src || '';
                            if (src.indexOf('clarity.ms') !== -1) {
                                node.remove();
                                log('Blocked dynamically-added Clarity script');
                            }
                        }
                    });
                });
            });

            clarityObserver.observe(document.documentElement, {
                childList: true,
                subtree: true
            });

            // Stop observing after page load
            window.addEventListener('load', function() {
                setTimeout(function() {
                    clarityObserver.disconnect();
                    log('Clarity observer disconnected');
                }, 1000);
            });

            log('Tracking blocked successfully');

        } catch (e) {
            if (ECC_DEBUG) console.error('[Cookie Consent] Early blocker error:', e);
        }

        function deleteCookies(names) {
            var hostname = window.location.hostname;
            names.forEach(function(name) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + hostname + ';';
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + hostname + ';';
            });
        }
    })();
    </script>
    <?php
}
