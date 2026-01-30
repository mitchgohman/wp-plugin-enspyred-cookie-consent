// Tracking Blocker - Disables Google Analytics, Google Ads, and Microsoft Clarity
// SAFE APPROACH: No monkey-patching of third-party globals
import { debugLog } from "./debug";

/**
 * Block all tracking scripts
 */
export const blockAll = () => {
    debugLog('Blocking all tracking scripts...');

    blockGoogleAnalytics();
    blockGoogleAds();
    blockMicrosoftClarity();

    debugLog('All tracking scripts blocked');
};

/**
 * Block Google Analytics using official disable flags
 * (No monkey-patching of window.ga)
 */
const blockGoogleAnalytics = () => {
    // Delete GA cookies first
    deleteGACookies();

    // Use official GA disable flags (supported by Google)
    window['ga-disable'] = true;

    // Find and disable specific GA4 measurement IDs
    if (window.gtag && window.dataLayer) {
        const dataLayer = window.dataLayer || [];

        dataLayer.forEach(item => {
            if (Array.isArray(item) && item[0] === 'config' && typeof item[1] === 'string') {
                const measurementId = item[1];
                if (measurementId.startsWith('G-') || measurementId.startsWith('UA-')) {
                    window[`ga-disable-${measurementId}`] = true;
                    debugLog('GA disabled for:', measurementId);
                }
            }
        });
    }

    // Generic GA4 pattern disable
    window['ga-disable-G-'] = true;

    debugLog('Google Analytics blocked (using official disable flags)');
};

/**
 * Delete Google Analytics cookies
 */
const deleteGACookies = () => {
    const gaCookies = ['_ga', '_gid', '_gat', '_gat_gtag', '_gac_gb'];
    deleteCookies(gaCookies);
    debugLog('GA cookies deleted');
};

/**
 * Block Google Ads conversion tracking
 */
const blockGoogleAds = () => {
    if (window.gtag) {
        // Disable data collection (official gtag API)
        window.gtag('set', 'ads_data_redaction', true);
        window.gtag('set', 'allow_ad_personalization_signals', false);

        // Find and disable Google Ads accounts in dataLayer
        if (window.dataLayer) {
            window.dataLayer.forEach(item => {
                if (Array.isArray(item) && item[0] === 'config' &&
                    typeof item[1] === 'string' && item[1].startsWith('AW-')) {
                    window.gtag('config', item[1], {
                        'send_page_view': false,
                        'allow_google_signals': false,
                        'allow_ad_personalization_signals': false
                    });
                    debugLog('Google Ads disabled for:', item[1]);
                }
            });
        }

        debugLog('Google Ads blocked');
    }
};

/**
 * Block Microsoft Clarity
 * SAFE APPROACH: Remove script tags, do NOT monkey-patch window.clarity
 */
const blockMicrosoftClarity = () => {
    // Remove any existing Clarity scripts from DOM
    const clarityScripts = document.querySelectorAll('script[src*="clarity.ms"]');
    clarityScripts.forEach(script => {
        script.remove();
        debugLog('Clarity script removed from DOM');
    });

    // Remove inline Clarity initialization scripts
    document.querySelectorAll('script:not([src])').forEach(script => {
        const content = script.textContent || '';
        if (content.includes('clarity.ms') ||
            (content.includes('window.clarity') && content.includes('function'))) {
            script.remove();
            debugLog('Clarity inline script removed');
        }
    });

    // Delete Clarity cookies
    const clarityCookies = ['_clck', '_clsk', 'CLID', 'ANONCHK', 'MR', 'MUID', 'SM'];
    deleteCookies(clarityCookies);

    debugLog('Clarity blocked (scripts removed, cookies deleted)');

    // NOTE: We intentionally do NOT do this anymore:
    // window.clarity = function() {};  // <-- CAUSES MIME ERROR
};

/**
 * Helper to delete cookies with multiple domain/path combinations
 */
const deleteCookies = (names) => {
    const hostname = window.location.hostname;
    names.forEach(cookieName => {
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${hostname};`;
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.${hostname};`;
    });
};
