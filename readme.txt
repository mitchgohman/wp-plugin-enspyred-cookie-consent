=== Enspyred Cookie Consent ===
Contributors: enspyred
Tags: cookies, privacy, ccpa, gdpr, consent
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.5
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

CCPA-compliant cookie consent banner with opt-out functionality for tracking scripts.

== Description ==

Enspyred Cookie Consent provides a simple, compliant way to handle cookie consent on your WordPress site. The plugin displays a consent banner and allows visitors to opt-out of tracking scripts, helping you comply with CCPA and GDPR requirements.

**Key Features:**

* CCPA and GDPR compliant cookie consent
* Customizable consent banner
* Opt-out functionality for tracking scripts
* React-based frontend for smooth user experience
* Block tracking scripts until consent is given
* Persistent consent storage
* Easy configuration through WordPress admin

== Installation ==

= IMPORTANT: Download the Correct ZIP File =

**DO NOT** download the repository ZIP from the main branch! The repository includes development files (node_modules, source code, etc.) totaling ~129MB and will not work properly when installed in WordPress.

**ALWAYS** download the official release ZIP from GitHub Releases. These are clean, production-ready distributions (~2-3MB) that contain only the necessary files.

= Installation Steps =

1. Download the latest **release ZIP** from GitHub: https://github.com/enspyred/enspyred-cookie-consent/releases
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded release ZIP file and click "Install Now"
5. Activate the plugin
6. Configure settings at Settings > Cookie Consent

== Configuration ==

1. Navigate to **Settings > Cookie Consent** in your WordPress admin
2. Customize the consent banner text and appearance
3. Configure which tracking scripts should be blocked until consent
4. Save your settings

== Frequently Asked Questions ==

= Is this plugin CCPA compliant? =

Yes, the plugin provides opt-out functionality as required by CCPA regulations. However, you should consult with legal counsel to ensure your full site complies with privacy laws.

= Is this plugin GDPR compliant? =

The plugin provides cookie consent functionality, but full GDPR compliance requires more than just a consent banner. Consult with legal counsel for complete compliance.

= How do I customize the banner appearance? =

You can customize the banner text, colors, and positioning through the plugin settings page in your WordPress admin.

= What happens when a user opts out? =

When a user opts out, the plugin blocks configured tracking scripts from loading on your site for that visitor.

== Changelog ==

= 1.0.0 =
* Initial release
* CCPA-compliant cookie consent banner
* Opt-out functionality
* React-based frontend
* Admin configuration panel
* Tracking script blocking

== Download & Updates ==

Download the latest version from GitHub: https://github.com/enspyred/wp-plugin-enspyred-cookie-consent/releases

The plugin includes automatic update notifications - you'll be notified in your WordPress admin when new versions are available.
