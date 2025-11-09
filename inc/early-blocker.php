<?php
/**
 * Early Blocker - Prevents tracking scripts from loading for opted-out users
 *
 * This runs BEFORE any tracking scripts (GA, Clarity, etc.) initialize,
 * ensuring zero tracking calls for users who opted out.
 */

// Inject blocking script in <head> with highest priority
add_action('wp_head', 'ecc_inject_early_blocker', 1);

function ecc_inject_early_blocker() {
    // Only on frontend
    if (is_admin()) {
        return;
    }

    ?>
    <script>
    (function() {
        'use strict';

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

            console.log('[Cookie Consent] Early blocker: User opted out, blocking tracking');

            // === Block Google Analytics ===

            // Set global GA disable flags (these prevent GA from initializing)
            window['ga-disable'] = true;

            // Also set for common GA4 property patterns
            // This will catch most GA4 properties
            window['ga-disable-G-'] = true;

            // Disable classic GA
            window.ga = function() {};

            // Delete GA cookies immediately
            var gaCookies = ['_ga', '_gid', '_gat', '_gat_gtag', '_gac_gb'];
            gaCookies.forEach(function(name) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname + ';';
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + window.location.hostname + ';';
            });

            // === Block Microsoft Clarity ===

            // Prevent Clarity from initializing
            window.clarity = function() {};
            window.clarity.q = [];

            // Delete Clarity cookies immediately
            var clarityCookies = ['_clck', '_clsk', 'CLID', 'ANONCHK', 'MR', 'MUID', 'SM'];
            clarityCookies.forEach(function(name) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname + ';';
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + window.location.hostname + ';';
            });

            console.log('[Cookie Consent] Early blocker: Tracking blocked successfully');

        } catch (e) {
            console.error('[Cookie Consent] Early blocker error:', e);
        }
    })();
    </script>
    <?php
}
