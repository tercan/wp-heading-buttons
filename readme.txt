=== WP Heading Buttons ===
Contributors: tercan
Donate link: http://tercan.net/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: heading buttons, editor buttons, block editor, gutenberg, classic editor
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0

One-click heading buttons (H1-H6) for WordPress Classic Editor and Gutenberg (Block Editor).

== Description ==

WP Heading Buttons adds quick heading buttons (H1-H6) to both Classic Editor (TinyMCE) and Block Editor toolbars.
Choose which levels appear, toggle Classic block support, and keep icons consistent with shared SVGs.

== Installation ==

1. Upload the `wp-heading-buttons` folder to the `/wp-content/plugins/` directory
2. Activate the WP Heading Buttons plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

Please use my <a href="http://tercan.net/wp-heading-buttons/">plugin page</a> for expedited help.

== Screenshots ==

1. Classic Editor toolbar with heading buttons.
2. Block Editor toolbar with heading buttons.

== Changelog ==

= 1.0 (2026-02-03) =
* Added Gutenberg editor toolbar with heading buttons.
* Added shared SVG icons for Classic and Block editors; removed PNG sprite.
* Added settings page for heading levels and Classic Block support (defaults: H2 + H3, enabled).
* Added active button state for Classic editor and container class hook.
* Added classic editor CSS hooks and modernized asset loading.
* Added translations (.po/.mo) and removed legacy TinyMCE language files.
* Added first-install admin notice linking to settings.

= 0.3 (2015-12-24) =
* Updated editor_plugin.js and language files for TinyMCE4 compatibility.
* Added Arabic, German, Italian and French language files.
* Checked WordPress 4.4 compatibility.

= 0.2 (2013-04-17) =
* Fixed the problem of self-deactivation.
* Removed buttons selection settings. All buttons is activate default.
* Added TinyMCE translation options.

= 0.1 (2013-04-02) =
* Initial release.

== Upgrade Notice ==
* 1.0 adds Block Editor support, shared SVG icons, and new settings defaults (H2/H3 enabled).
