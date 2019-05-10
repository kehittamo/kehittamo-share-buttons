<?php

namespace Kehittamo\Plugins\ShareButtons;
use Facebook;
class Share_Buttons_Facebook {
	/**
	 * Holds the options of the plugin
	 */
	private $options;

	/**
	 * Facebook instance
	 * @since    0.2.0alpha
	 * @var      object
	 */
	private $fb = null;

	/**
	* Start up
	*/
	public function __construct() {
		// Set options
		$this->options = get_option( SHARE_BUTTONS_SETTINGS_NAME );
	}

	/**
	* Get and set access_token
	* @return string $token
	*/
	private function retrieve_and_save_access_token() {
		if ( ! isset( $this->fb ) ) {
			return false;
		}

		try {
			$query        = http_build_query(
				[
					'client_id'     => $this->options[ SHARE_BUTTONS_FB_APP_ID ],
					'client_secret' => $this->options[ SHARE_BUTTONS_FB_APP_SECRET ],
					'grant_type'    => 'client_credentials',
				]
			);
			$access_token = $this->fb->get( '/oauth/access_token?' . $query, $this->fb->getApp()->getAccessToken() );
			$token        = $access_token->getGraphObject()->getProperty( 'access_token' );
			// Set 5 minute transient for access_token
			set_transient( SHARE_BUTTONS_FB_APP_TOKEN_TRANSIENT, (string) $token, MINUTE_IN_SECONDS * 5 );
			return (string) $token;
		} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
			// There was an error communicating with Graph
			// Or there was a problem validating the signed request
			error_log( print_r( $e->getMessage(), true ) );
		}
	}
	/**
	 * Return access_token.
	 * @since     0.2.0alpha
	 * @return    string
	 */
	public function get_access_token() {
		if ( ! empty( $this->options[ SHARE_BUTTONS_FB_APP_ID ] ) && ! empty( $this->options[ SHARE_BUTTONS_FB_APP_SECRET ] ) ) {
			return $this->options[ SHARE_BUTTONS_FB_APP_ID ] . '|' . $this->options[ SHARE_BUTTONS_FB_APP_SECRET ];
		}
		return '';
	}
}
