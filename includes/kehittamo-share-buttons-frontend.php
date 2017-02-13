<?php

namespace Kehittamo\Plugins\ShareButtons;
use Kehittamo\Plugins\ShareButtons\Facebook;

class FrontEnd {
    /**
     * Holds the options of the plugin
     */
    private $options;

    /**
     * Holds the options of the plugin
     */
    private $facebook;

    /**
     * Start up
     */
    public function __construct() {
        // Set options
        $this->options = get_option( SHARE_BUTTONS_SETTINGS_NAME );
        require_once( PLUGIN_PATH . '/includes/kehittamo-share-buttons-facebook.php' );
        add_filter( 'the_content' , array( $this, 'maybe_add_share_buttons' ), 90, 1 );
        add_shortcode( 'share-buttons', array( $this, 'sharebuttons_func' ) );
        $this->facebook = new \Kehittamo\Plugins\ShareButtons\Share_Buttons_Facebook();
    }


    /**
     *  Shortcode
     *  @param $atts    parameters
     */
    public function sharebuttons_func( $atts = array() ) {
            extract(
                shortcode_atts(
                    array(
                        'hide_counter' => 'false',
                    ),
                $atts )
            );

        $top = false;
        $bottom = true;

        if ( 'true' == $hide_counter ) {
            $top = true;
            $bottom = false;
        }

        $content = '';
        $shortcode_html = $this->add_buttons( get_the_ID(), get_the_permalink(), get_the_title(), $content, $top, $bottom );

        return $shortcode_html;

    }


    /**
     * Maybe add share buttons to post
     * @param $content post content
     */
    public function maybe_add_share_buttons( $content ) {
        global $wp_current_filter;

        if ( ! is_singular() || ! is_singular( 'post' ) || ! in_array( 'the_content', $wp_current_filter ) || in_array( 'get_the_excerpt', $wp_current_filter ) || ! $this->options ) {
            return $content;
        }
        $top = $this->options[ SHARE_BUTTONS_VISIBLE_POST_TOP ];
        $bottom = $this->options[ SHARE_BUTTONS_VISIBLE_POST_BOTTOM ];

        $new_content = $content;

        if ( in_the_loop() && ( $top || $bottom ) ) {
            $new_content = $this->add_buttons( get_the_ID(), get_the_permalink(), get_the_title(), $content, $top, $bottom );
        }

        return $new_content;

    }

    /**
    * Add share buttons to post
    * @param int $id           WP post / page id
    * @param string $url       WP post / page url
    * @param string $title     WP post / page title
    * @param string $content   WP post / page content
    * @param string $top position of buttons
    * @param string $bottom position of buttons
    *
    * @return string $content with share buttons
    */
    private function add_buttons( $id, $url, $title, $content, $top, $bottom ) {
        $tweet_text = ( strlen( $title ) > 113 ) ? mb_substr( $title, 0, 113, 'UTF-8' ) . '...' : $title;
        $new_content = '';
        $new_content_bottom = '';
        $new_content_top = '';
        $content_empty = empty( $content );
        if ( $top && ! $content_empty ) {
            $new_content_top .= "<div class='share-buttons-block top no-print'>";
            $new_content_top .= "<a class='share-button btn btn-default fb' href='http://www.facebook.com/share.php?u=" . $url . "' target='_blank' title='". __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'><img src='" . PLUGIN_URL . "includes/svg/facebook-logo.svg' alt='" . __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'/><span>Facebook</span></a>";
            $new_content_top .= "<a class='share-button btn btn-default twitter' href='https://twitter.com/share?url=" . $url . '&text=' . $tweet_text . "' target='_blank' title='". __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/twitter-logo.svg' alt='" . __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ) . "'/><span>Twitter</span></a>";
            $new_content_top .= "<a class='share-button btn btn-default whatsapp' href='whatsapp://send?text=" . $title . '?' . $url . "' data-href='" . $url . "' data-text='" . $title . "' title='". __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/whatsapp-logo.svg' alt='" . __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ) . "' /><span>Whatsapp</span></a>";
            $new_content_top .= '</div>';
        }
        if ( $bottom && ! $content_empty ) {
            $new_content_bottom .= "<div class='share-buttons-block bottom no-print'>";
            $new_content_bottom .= $this->get_share_counts_html( $id, $url );
            $new_content_bottom .= "<a class='share-button btn btn-default fb' href='http://www.facebook.com/share.php?u=" . $url . "' target='_blank' title='". __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'><img src='" . PLUGIN_URL . "includes/svg/facebook-logo.svg' alt='" . __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'/>Facebook</a>";
            $new_content_bottom .= "<a class='share-button btn btn-default twitter' href='https://twitter.com/share?url=" . $url . '&text=' . $tweet_text . "' target='_blank' title='". __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/twitter-logo.svg' alt='" . __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ) . "'/>Twitter</a>";
            $new_content_bottom .= "<a class='share-button btn btn-default whatsapp' href='whatsapp://send?text=" . $title . '?' . $url . "' data-href='" . $url . "' data-text='" . $title . "' title='". __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/whatsapp-logo.svg' alt='" . __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ) . "' />Whatsapp</a>";
            $new_content_bottom .= '</div>';
        }
        // If content is empty add only one button set to $new_content_top
        if ( $content_empty && $top && ! $bottom ) {
            $new_content_top .= "<div class='share-buttons-block top no-print'>";
                $new_content_top .= "<a class='share-button btn btn-default fb' href='http://www.facebook.com/share.php?u=" . $url . "' target='_blank' title='". __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'><img src='" . PLUGIN_URL . "includes/svg/facebook-logo.svg' alt='" . __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'/><span>Facebook</span></a>";
                $new_content_top .= "<a class='share-button btn btn-default twitter' href='https://twitter.com/share?url=" . $url . '&text=' . $tweet_text . "' target='_blank' title='". __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/twitter-logo.svg' alt='" . __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ) . "'/><span>Twitter</span></a>";
                $new_content_top .= "<a class='share-button btn btn-default whatsapp' href='whatsapp://send?text=" . $title . '?' . $url . "' data-href='" . $url . "' data-text='" . $title . "' title='". __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/whatsapp-logo.svg' alt='" . __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ) . "' /><span>Whatsapp</span></a>";
            $new_content_top .= '</div>';
        } elseif ( $content_empty && $bottom ) {
            $new_content_top .= "<div class='share-buttons-block bottom no-print'>";
                $new_content_top .= $this->get_share_counts_html( $id, $url );
                $new_content_top .= "<a class='share-button btn btn-default fb' href='http://www.facebook.com/share.php?u=" . $url . "' target='_blank' title='". __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'><img src='" . PLUGIN_URL . "includes/svg/facebook-logo.svg' alt='" . __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'/><span>Facebook</span></a>";
                $new_content_top .= "<a class='share-button btn btn-default twitter' href='https://twitter.com/share?url=" . $url . '&text=' . $tweet_text . "' target='_blank' title='". __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/twitter-logo.svg' alt='" . __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ) . "'/><span>Twitter</span></a>";
                $new_content_top .= "<a class='share-button btn btn-default whatsapp' href='whatsapp://send?text=" . $title . '?' . $url . "' data-href='" . $url . "' data-text='" . $title . "' title='". __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ). "'><img src='" . PLUGIN_URL . "includes/svg/whatsapp-logo.svg' alt='" . __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ) . "' /><span>Whatsapp</span></a>";
            $new_content_top .= '</div>';
        }

        $new_content .= $new_content_top;
        $new_content .= $content;
        $new_content .= $new_content_bottom;

        return $new_content;

    }

    /**
     * Get share counts html
     *
     * @param int $id WP post / page id
     * @param string $url WP post / page url
     *
     * @return string html sharecount h1 element
     */
    private function get_share_counts_html( $id, $url ) {
        $total_share_count = $this->get_share_counts( $id, $url );
        $html = '';
        $html .= '<h1>' . $total_share_count . "<span class='total-share-count-text'>" . __( 'shares', SHARE_BUTTONS_SLUG ) . '</span></h1>';
        return $html;
    }


    /**
    * Get share counts from Facebook and Twitter based on page/post url
    *
    * @param int $id WP post / page id
    * @param string $url WP post / page url
    *
    * @return string sharecount as a number
    */
    public function get_share_counts( $id, $url ) {
        $escaped_url = esc_url( $url );
        if ( $escaped_url && $id ) :
            // Get current shares from post meta
            $current_shares = get_post_meta( $id, SHARE_BUTTONS_POST_META_KEY, true );
            if ( is_string( $total_shares_count_cache = get_transient( SHARE_BUTTONS_TRANSIENT_PREFIX_KEY . $id ) ) ) {
                return $total_shares_count_cache;
            }
            // Disable Twitter for now because they do not have this endpoint anymore
            // $twitter_json = $this->get_data( 'http://cdn.api.twitter.com/1/urls/count.json?url=' . $escaped_url );
            // $twitter_obj = json_decode( $twitter_json );
            // $twitter_shares = $twitter_obj->count ? $twitter_obj->count : '0';
            // $twitter_shares = '0';
            // $total_share_count = $twitter_shares;
            $total_share_count = '0';
            // Try to get access_token and add it to the url if retrieved
            $token = $this->facebook->get_acces_token();
            $fb_url = is_string( $token ) ? 'https://graph.facebook.com/?access_token=' . $token .'&id=' . $escaped_url : 'https://graph.facebook.com/?id=' . $escaped_url;
            $facebook_json = $this->get_data( $fb_url );
            $facebook_obj = json_decode( $facebook_json );
            $facebook_obj = is_array( $facebook_obj ) ? $facebook_obj[0] : $facebook_obj;
            $fb_shares = isset( $facebook_obj->share->share_count ) ? $facebook_obj->share->share_count : '0';
            $fb_comments = isset( $facebook_obj->share->comment_count ) ? $facebook_obj->share->comment_count : '0';

            $total_share_count = $total_share_count + $fb_shares + $fb_comments;

            // If FB returns 0 shares (for any reason) and there's earlier sharecount available in post meta, use it instead
            if ( 0 === (int) $total_share_count && 0 < (int) $current_shares ) {
                $total_share_count = $current_shares;
            }

            // Set 5min cache
            set_transient( SHARE_BUTTONS_TRANSIENT_PREFIX_KEY . $id, $total_share_count, MINUTE_IN_SECONDS * 5 );

            // Add share count to post meta so the data can be used also elswhere
            update_post_meta( $id, SHARE_BUTTONS_POST_META_KEY,  $total_share_count );

            return $total_share_count;

        endif;
    }

    /**
    * Get data from url with curl or file_get_contents
    *
    * @param string $url
    *
    * @return mixed
    */
    private function get_data( $url ) {
        if ( function_exists( 'curl_version' ) ) {
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_USERAGENT, 'getShareCount/0.1 by kehittamo' );
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 1 );
            $data = curl_exec( $ch );
            curl_close( $ch );
        } else {
            $data = file_get_contents( $url );
        }

        return $data;
    }
}

$kehittamo_share_buttons_front_end = new \Kehittamo\Plugins\ShareButtons\FrontEnd();
