<?php

namespace Kehittamo\Plugins\ShareButtons;


class SettingsPage {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {

		// This page will be under "Settings"
		add_options_page(
			__( 'Share Buttons', SHARE_BUTTONS_SLUG ),
			__( 'Share Buttons', SHARE_BUTTONS_SLUG ),
			'manage_options',
			SHARE_BUTTONS_SETTINGS_PAGE_NAME,
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( SHARE_BUTTONS_SETTINGS_NAME );
		?>
		<div class="wrap kehittamo-share-buttons">
			<h2><?php _e( 'Share Buttons Settings', SHARE_BUTTONS_SLUG ); ?></h2>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'kehittamo_share_buttons_settings_group' );
				do_settings_sections( SHARE_BUTTONS_SETTINGS_PAGE_NAME );
				submit_button();
			?>
			</form>
			<footer class="kehittamo-share-buttons__footer">
				<a href="https://kehittamo.fi" title="Kehittämö" target="_blank">
					<strong><?php _e( 'Developed by Kehittämö', SHARE_BUTTONS_SLUG ); ?></strong>
					<img src="<?php echo PLUGIN_URL; ?>/includes/svg/digitoimisto-kehittamo.svg" alt="Kehittämö" />
				</a>
			</footer>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'kehittamo_share_buttons_settings_group', // Option group
			SHARE_BUTTONS_SETTINGS_NAME, // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'kehittamo_share_buttons_default', // ID
			__( 'Info', SHARE_BUTTONS_SLUG ), // Title
			array( $this, 'print_section_info' ), // Callback
			SHARE_BUTTONS_SETTINGS_PAGE_NAME // Page
		);

		add_settings_field(
			SHARE_BUTTONS_VISIBLE_POST_TOP, // ID
			__( 'Show share buttons at the top of posts?', SHARE_BUTTONS_SLUG ), // Title
			array( $this, 'share_buttons_visible_post_top_callback' ), // Callback
			SHARE_BUTTONS_SETTINGS_PAGE_NAME, // Page
			'kehittamo_share_buttons_default' // Section
		);
		add_settings_field(
			SHARE_BUTTONS_VISIBLE_POST_BOTTOM, // ID
			__( 'Show share buttons at the bottom of posts?', SHARE_BUTTONS_SLUG ), // Title
			array( $this, 'share_buttons_visible_post_bottom_callback' ), // Callback
			SHARE_BUTTONS_SETTINGS_PAGE_NAME, // Page
			'kehittamo_share_buttons_default' // Section
		);
		add_settings_field(
			SHARE_BUTTONS_FB_APP_ID, // ID
			__( 'Add Facebook App ID', SHARE_BUTTONS_SLUG ), // Title
			array( $this, 'share_buttons_fb_app_id_callback' ), // Callback
			SHARE_BUTTONS_SETTINGS_PAGE_NAME, // Page
			'kehittamo_share_buttons_default' // Section
		);
		add_settings_field(
			SHARE_BUTTONS_FB_APP_SECRET, // ID
			__( 'Add Facebook App Secret', SHARE_BUTTONS_SLUG ), // Title
			array( $this, 'share_buttons_fb_app_secret_callback' ), // Callback
			SHARE_BUTTONS_SETTINGS_PAGE_NAME, // Page
			'kehittamo_share_buttons_default' // Section
		);
		add_settings_field(
			SHARE_BUTTONS_FB_APP_SECRET, // ID
			__( 'Use default styles', SHARE_BUTTONS_SLUG ), // Title
			array( $this, 'share_buttons_default_styles_callback' ), // Callback
			SHARE_BUTTONS_SETTINGS_PAGE_NAME, // Page
			'kehittamo_share_buttons_default' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input[ SHARE_BUTTONS_VISIBLE_POST_TOP ] ) ) {
			$new_input[ SHARE_BUTTONS_VISIBLE_POST_TOP ] = absint( $input[ SHARE_BUTTONS_VISIBLE_POST_TOP ] );
		}
		if ( isset( $input[ SHARE_BUTTONS_VISIBLE_POST_BOTTOM ] ) ) {
			$new_input[ SHARE_BUTTONS_VISIBLE_POST_BOTTOM ] = absint( $input[ SHARE_BUTTONS_VISIBLE_POST_BOTTOM ] );
		}
		if ( isset( $input[ SHARE_BUTTONS_FB_APP_ID ] ) ) {
			$new_input[ SHARE_BUTTONS_FB_APP_ID ] = esc_attr( $input[ SHARE_BUTTONS_FB_APP_ID ] );
		}
		if ( isset( $input[ SHARE_BUTTONS_FB_APP_SECRET ] ) ) {
			$new_input[ SHARE_BUTTONS_FB_APP_SECRET ] = esc_attr( $input[ SHARE_BUTTONS_FB_APP_SECRET ] );
		}
		if ( isset( $input[ SHARE_BUTTONS_USE_DEFAULT_STYLES ] ) ) {
			$new_input[ SHARE_BUTTONS_USE_DEFAULT_STYLES ] = absint( $input[ SHARE_BUTTONS_USE_DEFAULT_STYLES ] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		echo '<div class="kehittamo-share-buttons__info">';
			echo '<p>' . __( 'By default share buttons are set in to the posts top and bottom. <br /> You can choose to hide share buttons from top or/and bottom if you want.', SHARE_BUTTONS_SLUG ) . '</p>';
			echo '<p>' . __( 'You can add share buttons to other content too via shortcode: ', SHARE_BUTTONS_SLUG ) . '</p>';
			echo '<code>[share-buttons]</code>';
			echo '<p>' . __( 'By default shortcode add buttons with share count. If you want to add buttons without share counts, use:', SHARE_BUTTONS_SLUG ) . '</p>';
			echo '<code>[share-buttons hide_counter=true]</code>';
		echo '</div>';
	}

	/**
	 * Print share_buttons_visible_post_top
	 */
	public function share_buttons_visible_post_top_callback() {
		printf(
			'<input type="checkbox" id="' . SHARE_BUTTONS_VISIBLE_POST_TOP . '" name="kehittamo_share_buttons_settings[' . SHARE_BUTTONS_VISIBLE_POST_TOP . ']" value="1"' . checked( 1, $this->options[ SHARE_BUTTONS_VISIBLE_POST_TOP ], false ) . '/>'
		);
	}

	/**
	 * Print share_buttons_visible_post_bottom
	 */
	public function share_buttons_visible_post_bottom_callback() {
		printf(
			'<input type="checkbox" id="' . SHARE_BUTTONS_VISIBLE_POST_BOTTOM . '" name="kehittamo_share_buttons_settings[' . SHARE_BUTTONS_VISIBLE_POST_BOTTOM . ']" value="1"' . checked( 1, $this->options[ SHARE_BUTTONS_VISIBLE_POST_BOTTOM ], false ) . '/>'
		);
	}

	/**
	 * Print kehittamo_share_buttons_fb_app_id
	 * TODO: test that app id is valid
	 */
	public function share_buttons_fb_app_id_callback() {
		printf(
			'<input type="text" id="' . SHARE_BUTTONS_FB_APP_ID . '" name="kehittamo_share_buttons_settings[' . SHARE_BUTTONS_FB_APP_ID . ']" value="' . $this->options[ SHARE_BUTTONS_FB_APP_ID ] . '" />'
		);
	}

	/**
	 * Print kehittamo_share_buttons_fb_app_secret
	 * TODO: test that app secret is valid
	 */
	public function share_buttons_fb_app_secret_callback() {
		printf(
			'<input type="text" id="' . SHARE_BUTTONS_FB_APP_SECRET . '" name="kehittamo_share_buttons_settings[' . SHARE_BUTTONS_FB_APP_SECRET . ']" value="' . $this->options[ SHARE_BUTTONS_FB_APP_SECRET ] . '" />'
		);
	}
	/**
	 * Print share_buttons_default_styles
	 */
	public function share_buttons_default_styles_callback() {
		printf(
			'<input type="checkbox" id="' . SHARE_BUTTONS_USE_DEFAULT_STYLES . '" name="kehittamo_share_buttons_settings[' . SHARE_BUTTONS_USE_DEFAULT_STYLES . ']" value="1"' . checked( 1, $this->options[ SHARE_BUTTONS_USE_DEFAULT_STYLES ], false ) . '/>'
		);
	}
}

if ( is_admin() ) {
	$kehittamo_share_buttons_settings_page = new \Kehittamo\Plugins\ShareButtons\SettingsPage();
}
