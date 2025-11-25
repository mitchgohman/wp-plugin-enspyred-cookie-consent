// Opt-Out Link Handler - For use with [enspyred_cookie_optout] shortcode
import { setConsent } from "./core/consent";
import { blockAll } from "./core/trackingBlocker";
import { debugLog } from "./core/debug";

/**
 * Handle opt-out link clicks
 * @param {Event} e - Click event
 */
const handleOptOutClick = (e) => {
    e.preventDefault();

    debugLog('Opt-out link clicked');

    // Block all tracking immediately
    blockAll();

    // Save opt-out choice
    setConsent("opted-out");

    // Show confirmation message
    alert("Your preference has been saved. Tracking has been disabled.");
};

// Initialize on DOM ready
const initOptOutLinks = () => {
    const links = document.querySelectorAll('.enspyred-cookie-optout-link');

    if (links.length === 0) {
        debugLog('No opt-out links found');
        return;
    }

    links.forEach((link) => {
        link.addEventListener('click', handleOptOutClick);
        debugLog('Opt-out link initialized');
    });
};

// Run when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initOptOutLinks);
} else {
    initOptOutLinks();
}

// Support Vite HMR during development
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        initOptOutLinks();
    });
}
