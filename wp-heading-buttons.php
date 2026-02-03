<?php
/*
 * Plugin Name: WP Heading Buttons
 * Plugin URI: http://tercan.net/wp-heading-buttons/
 * Description: One-click heading buttons (H1-H6) for WordPress Classic Editor and Gutenberg (Block Editor).
 * Version: 1.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Tercan Keskin
 * Author URI: http://tercan.net/
 * Text Domain: wp-heading-buttons
 * Domain Path: /languages
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined('ABSPATH') ) {
	exit;
}

define('WPHB_VER', '1.0');
define('WPHB_URL', plugin_dir_url( __FILE__ ));
define('WPHB_OPTION', 'wphb_settings');

function wphb_get_default_levels() {
	return array(2, 3);
}

function wphb_get_all_levels() {
	return array(1, 2, 3, 4, 5, 6);
}

function wphb_get_default_classic_block() {
	return 1;
}

function wphb_get_i18n_strings() {
	$heading_labels = array();
	foreach ( wphb_get_all_levels() as $level ) {
		// translators: %d is the heading level number.
		$heading_labels[ $level ] = sprintf( __( 'Heading %d', 'wp-heading-buttons' ), $level );
	}

	return array(
		'headingLevels' => __( 'Heading Levels', 'wp-heading-buttons' ),
		'headingLabels' => $heading_labels,
	);
}

function wphb_get_icon_svg( $level ) {
	$level = (int) $level;
	if ( $level < 1 || $level > 6 ) {
		return '';
	}

	return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false">'
		. '<rect x="1" y="1" width="18" height="18" rx="2" ry="2" fill="none" stroke="currentColor" stroke-width="1"/>'
		. '<text x="10" y="13" text-anchor="middle" font-size="9" font-family="sans-serif" font-weight="700" fill="currentColor">H'
		. $level
			. '</text></svg>';
}

function wphb_get_icon_svg_allowlist() {
	return array(
		'svg' => array(
			'xmlns' => true,
			'width' => true,
			'height' => true,
			'viewBox' => true,
			'viewbox' => true,
			'aria-hidden' => true,
			'focusable' => true,
		),
		'rect' => array(
			'x' => true,
			'y' => true,
			'width' => true,
			'height' => true,
			'rx' => true,
			'ry' => true,
			'fill' => true,
			'stroke' => true,
			'stroke-width' => true,
		),
		'text' => array(
			'x' => true,
			'y' => true,
			'text-anchor' => true,
			'font-size' => true,
			'font-family' => true,
			'font-weight' => true,
			'fill' => true,
		),
	);
}

function wphb_normalize_levels( $levels ) {
	if ( ! is_array( $levels ) ) {
		return array();
	}

	$normalized = array();
	foreach ( $levels as $level ) {
		$level = (int) $level;
		if ( $level < 1 || $level > 6 ) {
			continue;
		}
		if ( in_array( $level, $normalized, true ) ) {
			continue;
		}
		$normalized[] = $level;
	}

	sort( $normalized );

	return $normalized;
}

function wphb_get_settings() {
	$settings = get_option( WPHB_OPTION, array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	$levels = array_key_exists( 'levels', $settings ) ? wphb_normalize_levels( $settings['levels'] ) : wphb_get_default_levels();
	if ( array_key_exists( 'classic_block', $settings ) ) {
		$classic_block = ! empty( $settings['classic_block'] ) ? 1 : 0;
	} else {
		$classic_block = wphb_get_default_classic_block();
	}

	return array(
		'levels' => $levels,
		'classic_block' => $classic_block,
	);
}

function wphb_sanitize_settings( $input ) {
	$output = array();
	if ( ! is_array( $input ) ) {
		return $output;
	}

	if ( array_key_exists( 'levels', $input ) ) {
		$output['levels'] = wphb_normalize_levels( $input['levels'] );
	}

	$output['classic_block'] = ! empty( $input['classic_block'] ) ? 1 : 0;

	return $output;
}

function wphb_register_settings() {
	register_setting(
		'wphb_settings',
		WPHB_OPTION,
		array(
			'type' => 'array',
			'sanitize_callback' => 'wphb_sanitize_settings',
			'default' => array(
				'levels' => wphb_get_default_levels(),
				'classic_block' => wphb_get_default_classic_block(),
			),
		)
	);
}

function wphb_register_settings_page() {
	add_options_page(
		__( 'WP Heading Buttons', 'wp-heading-buttons' ),
		__( 'WP Heading Buttons', 'wp-heading-buttons' ),
		'manage_options',
		'wphb-settings',
		'wphb_render_settings_page'
	);
}

function wphb_should_show_settings_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( get_option( 'wphb_notice_dismissed', false ) ) {
		return false;
	}

	if ( false !== get_option( WPHB_OPTION, false ) ) {
		return false;
	}

	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if ( $screen && 'settings_page_wphb-settings' === $screen->id ) {
			return false;
		}
	}

	return true;
}

function wphb_handle_notice_dismissal() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_GET['wphb-dismiss-notice'] ) ) {
		return;
	}

	check_admin_referer( 'wphb_dismiss_notice' );
	update_option( 'wphb_notice_dismissed', 1 );

	$redirect = remove_query_arg( array( 'wphb-dismiss-notice', '_wpnonce' ) );
	wp_safe_redirect( esc_url_raw( $redirect ) );
	exit;
}

function wphb_admin_notice_settings() {
	if ( ! wphb_should_show_settings_notice() ) {
		return;
	}

	$settings_url = admin_url( 'options-general.php?page=wphb-settings' );
	$dismiss_url = wp_nonce_url(
		add_query_arg( 'wphb-dismiss-notice', '1' ),
		'wphb_dismiss_notice'
	);
	?>
	<div class="notice notice-info is-dismissible wphb-notice">
		<p><?php echo esc_html__( 'WP Heading Buttons is ready. Configure which heading buttons appear in the editor on the settings page.', 'wp-heading-buttons' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $settings_url ); ?>">
				<?php echo esc_html__( 'Open Settings', 'wp-heading-buttons' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( $dismiss_url ); ?>">
				<?php echo esc_html__( 'Dismiss', 'wp-heading-buttons' ); ?>
			</a>
		</p>
	</div>
	<?php
}

function wphb_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = wphb_get_settings();
	$levels = $settings['levels'];
	$classic_block = ! empty( $settings['classic_block'] );
	?>
	<div class="wrap wphb-settings">
		<div class="wphb-admin-header" style="display: flex; justify-content: space-between;">
			<h1>
				<span class="dashicons dashicons-heading"></span>
				<?php echo esc_html__( 'WP Heading Buttons', 'wp-heading-buttons' ); ?>
					<a href="https://github.com/tercan/wp-heading-buttons/blob/main/CHANGELOG.md" target="_blank" class="wphb-admin-header-action" title="<?php echo esc_attr__( 'View Changelog', 'wp-heading-buttons' ); ?>" rel="noopener noreferrer">
						(v<?php echo esc_html( WPHB_VER ); ?>)
					</a>
			</h1>
			<div class="wphb-admin-header-actions">
				<a href="https://tercan.net/wp-heading-buttons" target="_blank" class="wphb-admin-header-action" title="<?php echo esc_attr__( 'Visit Documentation', 'wp-heading-buttons' ); ?>" rel="noopener noreferrer">
					<?php echo esc_html__( 'Documentation', 'wp-heading-buttons' ); ?>
				</a>
			</div>
		</div>

		<p class="wphb-intro"><?php echo esc_html__( 'Manage heading button visibility and Classic Block behavior here.', 'wp-heading-buttons' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( 'wphb_settings' ); ?>
			<div class="wphb-grid">
				<section class="wphb-card">
					<h2><?php echo esc_html__( 'Heading Buttons', 'wp-heading-buttons' ); ?></h2>
					<p class="description"><?php echo esc_html__( 'Choose which levels appear in Classic and Block editor toolbars.', 'wp-heading-buttons' ); ?></p>
					<fieldset class="wphb-fieldset">
						<?php foreach ( wphb_get_all_levels() as $level ) : ?>
							<label class="wphb-check">
								<input
									type="checkbox"
									name="<?php echo esc_attr( WPHB_OPTION ); ?>[levels][]"
									value="<?php echo esc_attr( $level ); ?>"
									<?php checked( in_array( $level, $levels, true ) ); ?>
								/>
								<?php echo esc_html( 'H' . $level ); ?>
							</label>
						<?php endforeach; ?>
					</fieldset>
				</section>
				<section class="wphb-card">
					<h2><?php echo esc_html__( 'Classic Block', 'wp-heading-buttons' ); ?></h2>
					<p class="description"><?php echo esc_html__( 'Control toolbar behavior inside the Classic block while using the Block Editor.', 'wp-heading-buttons' ); ?></p>
					<label class="wphb-check">
						<input
							type="checkbox"
							name="<?php echo esc_attr( WPHB_OPTION ); ?>[classic_block]"
							value="1"
							<?php checked( $classic_block ); ?>
						/>
						<?php echo esc_html__( 'Show heading buttons in the Classic block', 'wp-heading-buttons' ); ?>
					</label>
				</section>
				<section class="wphb-card wphb-card-full">
					<h2><?php echo esc_html__( 'Description / Preview', 'wp-heading-buttons' ); ?></h2>
					<p class="description"><?php echo esc_html__( 'Selected buttons appear like this in Classic and Block editor toolbars.', 'wp-heading-buttons' ); ?></p>
					<div class="wphb-preview">
						<?php if ( empty( $levels ) ) : ?>
							<span class="description"><?php echo esc_html__( 'No buttons are currently selected.', 'wp-heading-buttons' ); ?></span>
						<?php else : ?>
								<?php foreach ( $levels as $level ) : ?>
									<span class="wphb-preview-item">
										<?php echo wp_kses( wphb_get_icon_svg( $level ), wphb_get_icon_svg_allowlist() ); ?>
										<?php echo esc_html( 'H' . $level ); ?>
									</span>
								<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</section>
			</div>
			<div class="wphb-actions">
				<?php submit_button(); ?>
			</div>
		</form>
	</div>
	<?php
}

function wphb_register_assets() {
	wp_register_script(
		'wphb-heading-icons',
		WPHB_URL . 'js/heading-icons.js',
		array(),
		WPHB_VER,
		false
	);

	wp_register_style(
		'wphb-classic-editor',
		WPHB_URL . 'css/classic-editor.css',
		array(),
		WPHB_VER
	);
}

function wphb_enqueue_settings_script() {
	static $enqueued = false;

	$settings = wphb_get_settings();
	$i18n = wphb_get_i18n_strings();
	wp_enqueue_script( 'wphb-heading-icons' );
	if ( $enqueued ) {
		return;
	}
	$enqueued = true;
	wp_add_inline_script(
		'wphb-heading-icons',
		'window.wphbHeadingButtonsSettings = ' . wp_json_encode( $settings ) . ';' .
		'window.wphbHeadingButtonsI18n = ' . wp_json_encode( $i18n ) . ';',
		'after'
	);
}

function wphb_user_can_use_editor() {
	return current_user_can('edit_posts') || current_user_can('edit_pages');
}

function wphb_should_load_classic_editor( $screen ) {
	if ( ! $screen || 'post' !== $screen->base ) {
		return false;
	}

	if ( ! wphb_user_can_use_editor() ) {
		return false;
	}

	if ( ! user_can_richedit() ) {
		return false;
	}

	if ( method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
		$settings = wphb_get_settings();
		if ( empty( $settings['classic_block'] ) ) {
			return false;
		}
	}

	return true;
}

function wphb_maybe_setup_classic_editor( $hook ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! wphb_should_load_classic_editor( $screen ) ) {
		return;
	}

	add_filter( 'mce_external_plugins', 'wphb_add_heading_tinymce' );
	add_filter( 'mce_buttons', 'wphb_register_heading_buttons' );
	wphb_enqueue_settings_script();
	wphb_enqueue_classic_editor_styles();
}

function wphb_register_heading_buttons( $buttons ) {
	$settings = wphb_get_settings();
	$levels = $settings['levels'];

	if ( empty( $levels ) ) {
		return $buttons;
	}

	$buttons[] = '|';
	foreach ( $levels as $level ) {
		$buttons[] = 'h' . $level;
	}
	return $buttons;
}

function wphb_add_heading_tinymce( $plugin_array ) {
	$plugin_array['wpheadingbuttons'] = WPHB_URL . 'js/editor_plugin.js';
	return $plugin_array;
}

function wphb_enqueue_block_editor_assets() {
	wphb_enqueue_settings_script();
	wp_enqueue_script(
		'wphb-block-editor',
		WPHB_URL . 'js/block-editor.js',
		array('wphb-heading-icons', 'wp-blocks', 'wp-element', 'wp-components', 'wp-compose', 'wp-data', 'wp-block-editor', 'wp-hooks'),
		WPHB_VER,
		true
	);
}

function wphb_enqueue_classic_editor_styles() {
	wp_enqueue_style( 'wphb-classic-editor' );
}

function wphb_enqueue_admin_settings_assets( $hook ) {
	if ( 'settings_page_wphb-settings' !== $hook ) {
		return;
	}

	wp_enqueue_style(
		'wphb-admin-settings',
		WPHB_URL . 'css/admin-settings.css',
		array(),
		WPHB_VER
	);
}

add_action('init', 'wphb_register_assets');
add_action('admin_init', 'wphb_register_settings');
add_action('admin_init', 'wphb_handle_notice_dismissal');
add_action('admin_menu', 'wphb_register_settings_page');
add_action('admin_enqueue_scripts', 'wphb_maybe_setup_classic_editor');
add_action('admin_enqueue_scripts', 'wphb_enqueue_admin_settings_assets');
add_action('admin_notices', 'wphb_admin_notice_settings');
add_action('enqueue_block_editor_assets', 'wphb_enqueue_block_editor_assets');
