// Tracking Blocker - Disables Google Analytics, Google Ads, and Microsoft Clarity
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
 * Block Google Analytics
 */
const blockGoogleAnalytics = () => {
    // Delete GA cookies
    deleteGACookies();

    // Disable GA globally
    if (window.gtag) {
        // Find GA measurement ID from dataLayer
        const dataLayer = window.dataLayer || [];
        const gaConfig = dataLayer.find(item =>
            Array.isArray(item) && item[0] === 'config' && item[1]?.startsWith('G-')
        );

        if (gaConfig && gaConfig[1]) {
            const measurementId = gaConfig[1];
            window[`ga-disable-${measurementId}`] = true;
            debugLog('GA disabled for:', measurementId);
        }

        // Generic disable
        window['ga-disable'] = true;
    }

    // Disable classic GA
    if (window.ga) {
        window.ga = function() {};
    }
};

/**
 * Delete Google Analytics cookies
 */
const deleteGACookies = () => {
    const gaCookies = ['_ga', '_gid', '_gat', '_gat_gtag'];

    gaCookies.forEach(cookieName => {
        // Delete for current domain
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;

        // Delete for parent domain
        const domain = window.location.hostname;
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${domain};`;
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.${domain};`;
    });

    debugLog('GA cookies deleted');
};

/**
 * Block Google Ads
 */
const blockGoogleAds = () => {
    if (window.gtag) {
        // Disable conversion tracking
        window.gtag('set', 'ads_data_redaction', true);
        window.gtag('set', 'allow_ad_personalization_signals', false);

        // Disable specific Google Ads account (AW-1050709691)
        window.gtag('config', 'AW-1050709691', {
            'send_page_view': false,
            'allow_google_signals': false,
            'allow_ad_personalization_signals': false
        });

        debugLog('Google Ads blocked');
    }
};

/**
 * Block Microsoft Clarity
 */
const blockMicrosoftClarity = () => {
    // Remove Clarity script if present
    const clarityScripts = document.querySelectorAll('script[src*="clarity.ms"]');
    clarityScripts.forEach(script => {
        script.remove();
        debugLog('Clarity script removed');
    });

    // Disable Clarity if loaded
    if (window.clarity) {
        window.clarity = function() {};
        debugLog('Clarity function disabled');
    }

    // Delete Clarity cookies
    const clarityCookies = ['_clck', '_clsk', 'CLID', 'ANONCHK', 'MR', 'MUID', 'SM'];
    clarityCookies.forEach(cookieName => {
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
    });

    debugLog('Clarity blocked and cookies deleted');
};
