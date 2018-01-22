<?php
/*
Plugin Name: Kehittämö Share Buttons
Plugin URI: http://www.kehittamo.fi
Description: Add Facebook, Twitter & Whatsapp share buttons to posts
Version: 0.2.2
Author: Kehittämö Oy / Janne Saarela
Author Email: asiakaspalvelu@kehittamo.fi
License: GPL2

Copyright 2016 Kehittämö Oy (asiakaspalvelu@kehittamo.fi)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


namespace Kehittamo\Plugins\ShareButtons;

	define( 'Kehittamo\Plugins\ShareButtons\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'Kehittamo\Plugins\ShareButtons\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_SLUG', 'kehittamo-share-buttons' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_SETTINGS_NAME', 'kehittamo_share_buttons_settings' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_SETTINGS_PAGE_NAME', 'kehittamo-share-buttons-admin' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_VISIBLE_POST_TOP', 'share_buttons_visible_post_top' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_VISIBLE_POST_BOTTOM', 'share_buttons_visible_post_bottom' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_POST_META_KEY', '_kehittamo_share_buttons_post_share_count' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_TRANSIENT_PREFIX_KEY', 'kehittamo_total_shares_count_' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_FB_APP_ID', 'kehittamo_share_buttons_fb_app_id' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_FB_APP_SECRET', 'kehittamo_share_buttons_fb_app_secret' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_FB_APP_TOKEN_TRANSIENT', 'kehittamo_share_buttons_fb_app_token' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_FB_APP_DEFAULT_API_VERSION', 'v2.7' );
	define( 'Kehittamo\Plugins\ShareButtons\SHARE_BUTTONS_USE_DEFAULT_STYLES', 'kehittamo_share_buttons_use_default_styles' );

class Load {

	/**
	 * Construct the plugin
	 */
	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'load_plugin' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		register_activation_hook( __FILE__, array( $this, 'init_plugin' ) );

		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );

	}

	/**
	 * Enable plugin by default
	 */
	function init_plugin() {
		$options = get_option( SHARE_BUTTONS_SETTINGS_NAME );
		if ( ! $options ) {
			$default_settings = array(
				SHARE_BUTTONS_VISIBLE_POST_TOP    => 1,
				SHARE_BUTTONS_VISIBLE_POST_BOTTOM => 1,
				SHARE_BUTTONS_USE_DEFAULT_STYLES  => 1,
			);
			update_option( SHARE_BUTTONS_SETTINGS_NAME, $default_settings );
		}
	}

	function deactivate_plugin() {
		// TODO
	}

	/**
	 * Load the plugin and its dependencies
	 */
	function load_plugin() {

		add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );

		$this->load_textdomain();

		$this->admin();

		add_action( 'init', array( $this, 'front_end' ) );

	}

	/**
	* Load plugin textdomain.
	*/
	function load_textdomain() {
		load_plugin_textdomain( SHARE_BUTTONS_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Plugin admin page
	 */
	function admin() {

		// Load plugin options page
		require_once( PLUGIN_PATH . '/includes/kehittamo-share-buttons-admin.php' );

	}

	/**
	 * Load front end
	 */
	function front_end() {

		// Load front end
		require_once( PLUGIN_PATH . '/includes/kehittamo-share-buttons-frontend.php' );

	}


	/**
	 * Load Frontend Styles
	 */
	function wp_enqueue_scripts() {
		// Check if plugin is enabled and include styles / scripts only if is
		$options = get_option( SHARE_BUTTONS_SETTINGS_NAME );
		if ( $options ) {
			// Javascript
			wp_register_script( 'kehittamo-share-buttons', PLUGIN_URL . 'includes/js/kehittamo-share-buttons.min.js', array( 'jquery' ), null, true );
			wp_enqueue_script( 'kehittamo-share-buttons' );
			if ( $options[ SHARE_BUTTONS_USE_DEFAULT_STYLES ] ) {
				//CSS Styles
				wp_register_style( 'kehittamo-share-buttons-frontend', PLUGIN_URL . 'includes/css/kehittamo-share-buttons-frontend.min.css' );
				wp_enqueue_style( 'kehittamo-share-buttons-frontend' );
			}
		}
	}

	/**
	 * Add async attr to script tag
	 */
	function add_async_attribute( $tag, $handle ) {
		if ( 'kehittamo-share-buttons' !== $handle ) {
			return $tag;
		}
		return str_replace( ' src', ' async src', $tag );
	}

	/**
	 * Load Admin Javascript and Styles
	 */
	function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( is_admin() && $screen && 'settings_page_' . SHARE_BUTTONS_SETTINGS_PAGE_NAME == $screen->id ) {

			// CSS Styles
			wp_register_style( 'kehittamo-share-buttons-admin', PLUGIN_URL . 'includes/css/kehittamo-share-buttons-admin.min.css' );
			wp_enqueue_style( 'kehittamo-share-buttons-admin' );

		}
	}
}

$kehittamo_share_buttons = new \Kehittamo\Plugins\ShareButtons\Load();
