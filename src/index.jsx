import { createRoot } from "react-dom/client";
import CookieConsentBanner from "./CookieConsentBanner";
import { hasValidConsent, isOptedOut } from "./core/consent";
import { blockAll } from "./core/trackingBlocker";
import { debugLog, debugError } from "./core/debug";

const mountBanner = () => {
    // Check if user already made a choice
    if (hasValidConsent()) {
        debugLog('User already made a choice, not showing banner');

        // If opted out, block tracking on every page load
        if (isOptedOut()) {
            debugLog('User opted out - blocking tracking');
            blockAll();
        }

        return; // Don't show banner
    }

    // Find mount point
    const container = document.getElementById('enspyred-cookie-consent-root');

    if (!container) {
        debugError('Mount point not found');
        return;
    }

    // Get settings from WordPress
    const settings = window.ECC_DATA?.settings || {};

    // Check if banner is enabled
    if (!settings.enabled) {
        debugLog('Banner disabled in settings');
        return;
    }

    // Mount banner
    const root = createRoot(container);
    root.render(<CookieConsentBanner settings={settings} />);

    debugLog('Banner mounted');
};

// Mount when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mountBanner);
} else {
    mountBanner();
}

// Support Vite HMR during development
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        mountBanner();
    });
}
