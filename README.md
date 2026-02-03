# WP Heading Buttons

Add one-click heading buttons (H1-H6) to the WordPress Classic Editor and Gutenberg (Block Editor).

## Features
- Heading buttons for Classic Editor (TinyMCE) and Block Editor toolbars.
- Shared SVG icon set for consistent visuals.
- Settings page to choose which heading levels appear.
- Optional heading buttons inside the Classic block in Gutenberg.
- Custom CSS hooks for Classic Editor button styling.
- Translations via standard WordPress `.po`/`.mo` files.

## Installation
1. Upload the `wp-heading-buttons` folder to `wp-content/plugins/`.
2. Activate **WP Heading Buttons** in the Plugins screen.

## Usage
1. Go to **Settings → WP Heading Buttons**.
2. Select which heading levels to show (default: H2 + H3).
3. Toggle **Show heading buttons in the Classic block** if needed.
4. Save changes.

## Styling (Classic Editor)
The plugin exposes predictable selectors so you can style the Classic Editor toolbar group:
- `.wphb-container`
- `.wphb-container .mce-active`

Add your custom styles in `css/classic-editor.css`.

## Localization
Text domain: `wp-heading-buttons`

Translation files live in `languages/` (e.g., `wp-heading-buttons-tr_TR.po`).

## Files
- `wp-heading-buttons.php` — main plugin file
- `js/` — editor integrations
- `css/` — admin/settings and Classic Editor styles
- `languages/` — translation files

## Changelog
See `CHANGELOG.md`.

## License
GPLv3
