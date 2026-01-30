<?php
/**
 * Early Blocker - Prevents tracking scripts from loading for opted-out users
 *
 * SAFE APPROACH:
 * - Google Analytics: Uses official ga-disable-* flags (supported by Google)
 * - Microsoft Clarity: Blocks script injection via MutationObserver (no monkey-patching)
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

        function log(msg) {
            if (ECC_DEBUG) console.log('[Cookie Consent] ' + msg);
        }

        // Check localStorage for opt-out status
        try {
            var consentData = localStorage.getItem('enspyred_cookie_consent');
            if (!consentData) return;

            var consent = JSON.parse(consentData);

            // Check if expired
            if (consent.expiresAt && Date.now() > consent.expiresAt) {
                localStorage.removeItem('enspyred_cookie_consent');
                return;
            }

            // Only block if opted out
            if (consent.choice !== 'opted-out') return;

            log('User opted out - blocking tracking');

            // === GOOGLE ANALYTICS (Safe approach using official flags) ===

            // Official GA disable flags - these are supported by Google
            window['ga-disable'] = true;

            // GA4 specific disable (catches G-XXXXXXX properties)
            // Google checks window['ga-disable-' + measurementId] before tracking
            window['ga-disable-G-'] = true;

            // Delete existing GA cookies
            var gaCookies = ['_ga', '_gid', '_gat', '_gat_gtag', '_gac_gb'];
            deleteCookies(gaCookies);

            // === MICROSOFT CLARITY (Safe approach - block script loading) ===

            // Instead of monkey-patching window.clarity, we prevent the script from loading
            // Using MutationObserver to intercept and remove Clarity scripts before execution

            var clarityObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.tagName === 'SCRIPT') {
                            var src = node.src || '';
                            var content = node.textContent || '';

                            // Block Clarity script by src
                            if (src.indexOf('clarity.ms') !== -1) {
                                node.remove();
                                log('Blocked Clarity script (src): ' + src);
                                return;
                            }

                            // Block inline Clarity initialization
                            if (content.indexOf('clarity') !== -1 &&
                                (content.indexOf('clarity.ms') !== -1 ||
                                 content.indexOf('window.clarity') !== -1)) {
                                node.remove();
                                log('Blocked Clarity inline script');
                                return;
                            }
                        }
                    });
                });
            });

            // Start observing immediately (before Clarity can load)
            clarityObserver.observe(document.documentElement, {
                childList: true,
                subtree: true
            });

            // Store reference for cleanup after page load
            window.__eccClarityObserver = clarityObserver;

            // Stop observing after page fully loads (performance optimization)
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (window.__eccClarityObserver) {
                        window.__eccClarityObserver.disconnect();
                        log('Clarity observer disconnected (page loaded)');
                    }
                }, 1000);
            });

            // Delete existing Clarity cookies
            var clarityCookies = ['_clck', '_clsk', 'CLID', 'ANONCHK', 'MR', 'MUID', 'SM'];
            deleteCookies(clarityCookies);

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
