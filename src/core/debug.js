/**
 * Debug logging utilities for Enspyred Cookie Consent
 * Only logs to console when debug mode is enabled in plugin settings
 */

const isDebugEnabled = () => {
  // Check both data sources (banner uses ECC_DATA, optout uses ECC_OPTOUT_DATA)
  return window.ECC_DATA?.settings?.debug_mode === true ||
         window.ECC_OPTOUT_DATA?.debug === true;
};

export const debugLog = (...args) => {
  if (isDebugEnabled()) {
    console.log('[Cookie Consent]', ...args);
  }
};

export const debugError = (...args) => {
  if (isDebugEnabled()) {
    console.error('[Cookie Consent]', ...args);
  }
};
