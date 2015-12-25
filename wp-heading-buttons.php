<?php
/*
Plugin Name: WP Heading Buttons
Plugin URI: http://tercan.net/wp-heading-buttons/
Description: Adding heading buttons (H1, H2, H3, H4, H5, H6) to TinyMCE editor.
Version: 0.3
Author: Tercan Keskin
Author URI: http://tercan.net/
License: GPLv3
*/

define('WPHB_VER', '0.3');
define('WPHB_URL', plugin_dir_url( __FILE__ ));


function add_heading_button() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;

	if ( get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'add_heading_tinymce');
		add_filter('mce_buttons', 'register_heading_buttons');
	}
}

function register_heading_buttons($buttons) {
	array_push($buttons, '|', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
	return $buttons;
}

function add_heading_tinymce($plugin_array) {
	$plugin_array['wpheadingbuttons'] = WPHB_URL . '/js/editor_plugin.js';
	return $plugin_array;
}

function wphb_admin_css() {
	echo '
		<style>
			i.wphb-ico { background: url("' . WPHB_URL . '/images/heading-buttons.png") no-repeat; }
			i.btn-h1  { background-position: 0 0; }
			i.btn-h2  { background-position: 0 -20px; }
			i.btn-h3  { background-position: 0 -40px; }
			i.btn-h4  { background-position: 0 -60px; }
			i.btn-h5  { background-position: 0 -80px; }
			i.btn-h6  { background-position: 0 -100px; }
		</style>
';
}

add_action('admin_head', 'wphb_admin_css');
add_action('init', 'add_heading_button');
