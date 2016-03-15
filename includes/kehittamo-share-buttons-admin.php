<?php

namespace Kehittamo\Plugins\ShareButtons;


class SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {

        // This page will be under "Settings"
        add_options_page(
            __('Share Buttons', 'kehittamo-share-buttons'),
            __('Share Buttons', 'kehittamo-share-buttons'),
            'manage_options',
            'kehittamo-share-buttons-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page(){
        // Set class property
        $this->options = get_option( SHARE_BUTTONS_SETTINGS_NAME );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e( 'Share Buttons Settings', 'kehittamo-share-buttons' ) ?></h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'kehittamo_share_buttons_settings_group' );
                do_settings_sections( 'kehittamo-share-buttons-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'kehittamo_share_buttons_settings_group', // Option group
            SHARE_BUTTONS_SETTINGS_NAME, // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'kehittamo_share_buttons_default', // ID
            __('General settings', 'kehittamo-share-buttons'), // Title
            array( $this, 'print_section_info' ), // Callback
            'kehittamo-share-buttons-admin' // Page
        );

        add_settings_field(
            SHARE_BUTTONS_VISIBLE_POST_TOP, // ID
              __( 'Show share buttons at the top of posts?', 'kehittamo-share-buttons' ), // Title
            array( $this, 'share_buttons_visible_post_top_callback' ), // Callback
            'kehittamo-share-buttons-admin', // Page
            'kehittamo_share_buttons_default' // Section
        );
        add_settings_field(
            SHARE_BUTTONS_VISIBLE_POST_BOTTOM, // ID
              __( 'Show share buttons at the bottom of posts?', 'kehittamo-share-buttons' ), // Title
            array( $this, 'share_buttons_visible_post_bottom_callback' ), // Callback
            'kehittamo-share-buttons-admin', // Page
            'kehittamo_share_buttons_default' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){
        $new_input = array();
        if( isset( $input[SHARE_BUTTONS_VISIBLE_POST_TOP] ) ){
          $new_input[SHARE_BUTTONS_VISIBLE_POST_TOP] = absint( $input[SHARE_BUTTONS_VISIBLE_POST_TOP] );
        }
        if( isset( $input[SHARE_BUTTONS_VISIBLE_POST_BOTTOM] ) ){
          $new_input[SHARE_BUTTONS_VISIBLE_POST_BOTTOM] = absint( $input[SHARE_BUTTONS_VISIBLE_POST_BOTTOM] );
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info(){}

    /**
     * Print share_buttons_visible_post_top
     */
    public function share_buttons_visible_post_top_callback(){
      printf(
        '<input type="checkbox" id="' . SHARE_BUTTONS_VISIBLE_POST_TOP . '" name="kehittamo_share_buttons_settings[' . SHARE_BUTTONS_VISIBLE_POST_TOP . ']" value="1"' . checked( 1, $this->options[SHARE_BUTTONS_VISIBLE_POST_TOP], false ) . '/>'
      );
    }

    /**
     * Print share_buttons_visible_post_bottom
     */
    public function share_buttons_visible_post_bottom_callback(){
        printf(

            '<input type="checkbox" id="' . SHARE_BUTTONS_VISIBLE_POST_BOTTOM . '" name="kehittamo_share_buttons_settings[' . SHARE_BUTTONS_VISIBLE_POST_BOTTOM . ']" value="1"' . checked( 1, $this->options[SHARE_BUTTONS_VISIBLE_POST_BOTTOM], false ) . '/>'

        );
    }

}

if( is_admin() ){
  $kehittamo_share_buttons_settings_page = new \Kehittamo\Plugins\ShareButtons\SettingsPage();
}
