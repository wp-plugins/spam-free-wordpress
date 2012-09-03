<?php

if( !class_exists('SFW_KEY' ) ) {
	
	class SFW_KEY {
	
		function validate_key( $args ) {
			$request = wp_remote_post( SFW_API_KEY_CHECK, array( 'body' => $args ) );
			
			if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			// Request failed
				return false;
			}
			$response = wp_remote_retrieve_body( $request );
			
			return $response;
		}
		
		function get_key() {
			$sfw_options = get_option('spam_free_wordpress');
			$api_key = $sfw_options['api_key'];
			
			return $api_key;
		}
	
	}

	$sfw_key = new SFW_KEY();

}

?>