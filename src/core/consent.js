// Consent Manager - Handles localStorage for cookie consent choices
// Also sets a cookie for PHP-side script blocking
import { debugLog, debugError } from "./debug";

const STORAGE_KEY = "enspyred_cookie_consent";
const COOKIE_NAME = "ecc_opted_out";
const EXPIRY_MONTHS = 12;

/**
 * Set a cookie (for PHP to read consent status)
 */
const setCookie = (name, value, days) => {
    const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
    document.cookie = `${name}=${value}; expires=${expires}; path=/; SameSite=Lax`;
};

/**
 * Delete a cookie
 */
const deleteCookie = (name) => {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
};

/**
 * Get current consent status
 * @returns {Object|null} Consent object or null if not set
 */
export const getConsent = () => {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (!stored) return null;

        const consent = JSON.parse(stored);

        // Check if expired
        if (consent.expiresAt && Date.now() > consent.expiresAt) {
            clearConsent();
            return null;
        }

        return consent;
    } catch (error) {
        debugError("Error reading consent:", error);
        return null;
    }
};

/**
 * Set consent choice
 * @param {string} choice - 'accepted', 'opted-out', or 'dismissed'
 */
export const setConsent = (choice) => {
    try {
        const now = Date.now();
        const expiresAt = now + EXPIRY_MONTHS * 30 * 24 * 60 * 60 * 1000; // ~12 months

        const consent = {
            choice,
            timestamp: now,
            expiresAt,
        };

        localStorage.setItem(STORAGE_KEY, JSON.stringify(consent));

        // Also set/clear cookie for PHP-side blocking
        if (choice === "opted-out") {
            setCookie(COOKIE_NAME, "1", EXPIRY_MONTHS * 30);
            debugLog("Opt-out cookie set for PHP");
        } else {
            deleteCookie(COOKIE_NAME);
        }

        debugLog("Choice saved:", choice);
    } catch (error) {
        debugError("Error saving consent:", error);
    }
};

/**
 * Check if user has made a valid consent choice
 * @returns {boolean}
 */
export const hasValidConsent = () => {
    const consent = getConsent();
    return consent !== null;
};

/**
 * Check if user opted out
 * @returns {boolean}
 */
export const isOptedOut = () => {
    const consent = getConsent();
    return consent?.choice === "opted-out";
};

/**
 * Clear consent (for testing/reset)
 */
export const clearConsent = () => {
    try {
        localStorage.removeItem(STORAGE_KEY);
        deleteCookie(COOKIE_NAME);
        debugLog("Consent cleared");
    } catch (error) {
        debugError("Error clearing consent:", error);
    }
};
