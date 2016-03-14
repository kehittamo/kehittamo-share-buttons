<?php

namespace Kehittamo\Plugins\ShareButtons;


class FrontEnd{
    /**
     * Holds the options of the plugin
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
      // Set options
      $this->options = get_option( 'kehittamo_share_buttons_settings' );
      add_filter( 'the_content' , array( $this, 'maybe_add_share_buttons' ), 10, 1 );
    }

    /**
     * Maybe add share buttons to post
     */
    public function maybe_add_share_buttons( $content ) {
      global $wp_current_filter;

      if( ! is_singular() || ! is_singular( 'post' ) || ! in_array( 'the_content', $wp_current_filter ) || !$this->options ) {
        return $content;
      }
      $top = $this->options[ 'share_buttons_visible_post_top' ];
      $bottom = $this->options[ 'share_buttons_visible_post_bottom' ];
      $new_content = $content;

      if( in_the_loop() && ( $top || $bottom ) ){
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
    private function add_buttons( $id, $url, $title, $content, $top, $bottom ){
      $tweet_text = ( strlen( $title ) > 117 ) ? substr( $title, 0, 117 ) . '...' : $title;
      $new_content = "";
      $new_content_bottom = "";
      $new_content_top = "";
      if( $top ){
        $new_content_top .= "<div class='share-buttons-block top'>";
          $new_content_top .= "<a class='share-button btn btn-default fb' href='http://www.facebook.com/share.php?u=" . $url . "' target='_blank' title='". __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'><i class='kaf-icon kaf-icon-facebook-squared'></i><span>Facebook</span></a>";
          $new_content_top .= "<a class='share-button btn btn-default twitter' href='https://twitter.com/share?url=" . $url . "&text=" . $tweet_text . "' target='_blank' title='". __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ). "'><i class='kaf-icon kaf-icon-twitter'></i><span>Twitter</span></a>";
          $new_content_top .= "<a class='share-button btn btn-default whatsapp' href='whatsapp://send?text=" . $title . "?" . $url . "' data-href='" . $url . "' data-text='" . $title . "' title='". __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ). "'><i class='kaf-icon kaf-icon-whatsapp'></i><span>Whatsapp</span></a>";
        $new_content_top .= "</div>";
      }
      if( $bottom ){
        $new_content_bottom .= "<div class='share-buttons-block bottom'>";
          $new_content_bottom .= $this->get_share_counts_html( $id, $url );
          $new_content_bottom .= "<a class='share-button btn btn-default fb' href='http://www.facebook.com/share.php?u=" . $url . "' target='_blank' title='". __( 'Share This on Facebook', SHARE_BUTTONS_SLUG ) . "'><i class='kaf-icon kaf-icon-facebook-squared'></i>Facebook</a>";
          $new_content_bottom .= "<a class='share-button btn btn-default twitter' href='https://twitter.com/share?url=" . $url . "&text=" . $tweet_text . "' target='_blank' title='". __( 'Share This on Twitter', SHARE_BUTTONS_SLUG ). "'><i class='kaf-icon kaf-icon-twitter'></i>Twitter</a>";
          $new_content_bottom .= "<a class='share-button btn btn-default whatsapp' href='whatsapp://send?text=" . $title . "?" . $url . "' data-href='" . $url . "' data-text='" . $title . "' title='". __( 'Share This on Whatsapp', SHARE_BUTTONS_SLUG ). "'><i class='kaf-icon kaf-icon-whatsapp'></i>Whatsapp</a>";
        $new_content_bottom .= "</div>";
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
    private function get_share_counts_html( $id, $url ){
      $total_share_count = $this->get_share_counts( $id, $url, false, true );
      $html = '';
      $html .= "<h1>" . $total_share_count . "<span class='total-share-count-text'>" . __( 'shares', SHARE_BUTTONS_SLUG ) . "</span></h1>";
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
  public function get_share_counts( $id, $url ){

    $escaped_url = esc_url( $url );
    if( $escaped_url && $id ) :
      if( is_string( $total_shares_count_cache = get_transient( 'total_shares_count_' . $id ) )) {
        return $total_shares_count_cache;
      }

      $twitter_json = $this->get_data( 'http://cdn.api.twitter.com/1/urls/count.json?url=' . $escaped_url );
      $twitter_obj = json_decode( $twitter_json );
      $twitter_shares = $twitter_obj->count ? $twitter_obj->count : '0';
      $total_share_count = $twitter_shares;

      $facebook_json = $this->get_data( 'https://api.facebook.com/method/links.getStats?format=json&urls=' . $escaped_url );
      $facebook_obj = json_decode( $facebook_json );
      $facebook_obj = is_array( $facebook_obj ) ? $facebook_obj[0] : $facebook_obj;
      $fb_shares = isset( $facebook_obj->share_count ) ? $facebook_obj->share_count : '0';
      $fb_comments = isset( $facebook_obj->commentsbox_count ) ? $facebook_obj->commentsbox_count : '0';
      $fb_likes = isset( $facebook_obj->like_count ) ? $facebook_obj->like_count : '0';
      $total_share_count = $total_share_count + $fb_shares + $fb_likes;

      // Set 5min cache
      set_transient( 'total_shares_count_' . $id, $total_share_count, 60 * 5 );

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
  private function get_data( $url ){
    if( function_exists( 'curl_version' )) {
      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_USERAGENT, 'getShareCount/0.1 by kehittamo' );
      curl_setopt( $ch, CURLOPT_URL, $url );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
      curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 1 );
      $data = curl_exec( $ch );
      curl_close( $ch );
    }
    else {
      $data = @file_get_contents( $url );
    }

    return $data;
  }

}

$kehittamo_share_buttons_front_end = new \Kehittamo\Plugins\ShareButtons\FrontEnd();
