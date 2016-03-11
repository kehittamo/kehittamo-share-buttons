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
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'kehittamo_share_buttons_settings' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e('Share Buttons Settings', 'kehittamo-share-buttons') ?></h2>
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
            'kehittamo_share_buttons_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'kehittamo_share_buttons_default', // ID
            __('General settings', 'kehittamo-share-buttons'), // Title
            array( $this, 'print_section_info' ), // Callback
            'kehittamo-share-buttons-admin' // Page
        );

        add_settings_field(
            'share_buttons_visible_post_top', // ID
              __( 'Show share buttons at the top of posts?', 'kehittamo-share-buttons' ), // Title
            array( $this, 'share_buttons_visible_post_top_callback' ), // Callback
            'kehittamo-share-buttons-admin', // Page
            'kehittamo_share_buttons_default' // Section
        );
        add_settings_field(
            'share_buttons_visible_post_bottom', // ID
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
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['share_buttons_visible_post_top'] ) ){
          $new_input['share_buttons_visible_post_top'] = absint( $input['share_buttons_visible_post_top'] );
        }
        if( isset( $input['share_buttons_visible_post_bottom'] ) ){
          $new_input['share_buttons_visible_post_bottom'] = absint( $input['share_buttons_visible_post_bottom'] );
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info(){
    }

    /**
     * Print share_buttons_visible_post_top
     */
    public function share_buttons_visible_post_top_callback(){
        printf(

            '<input type="checkbox" id="share_buttons_visible_post_top" name="kehittamo_share_buttons_settings[share_buttons_visible_post_top]" value="1"' . checked( 1, $this->options['share_buttons_visible_post_top'], false ) . '/>'

        );
    }

    /**
     * Print share_buttons_visible_post_bottom
     */
    public function share_buttons_visible_post_bottom_callback(){
        printf(

            '<input type="checkbox" id="share_buttons_visible_post_bottom" name="kehittamo_share_buttons_settings[share_buttons_visible_post_bottom]" value="1"' . checked( 1, $this->options['share_buttons_visible_post_bottom'], false ) . '/>'

        );
    }

}

if( is_admin() ){
  $kehittamo_share_buttons_settings_page = new \Kehittamo\Plugins\ShareButtons\SettingsPage();
}
