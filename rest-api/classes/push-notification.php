<?php
/*----- 	Push  Notifications Starts Here	 -----*/
add_action( 'rest_api_init', 'adforestAPI_firebase_get_hook', 0 );
function adforestAPI_firebase_get_hook() {
    register_rest_route( 'adforest/v1', '/push/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'adforestAPI_firebase_get',
				'permission_callback' => function () { return true;  },
        	)
    );
}

if( !function_exists('adforestAPI_firebase_get' ) )
{
	function adforestAPI_firebase_get($request)
	{
		global $adforestAPI;
		if(isset( $adforestAPI['api_firebase_id'] ) && $adforestAPI['api_firebase_id'] != "" )
		{
			$api_firebase_id = $adforestAPI['api_firebase_id'];
			define( 'API_ACCESS_KEY', $api_firebase_id );
			$registrationIds = array( $reg_id );
			$msg = array
			(
				'message' 	=> 'here is a message. message',
				'title'		=> 'This is a title. title',
				'subtitle'	=> 'This is a subtitle. subtitle',
				'name'	=> 'Humayun',
				'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
			$fields = array( 'registration_ids' => $registrationIds, 'data' => $msg );
			$headers = array('Authorization: key=' . API_ACCESS_KEY, 'Content-Type: application/json' );
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			
			return $response = array( 'success' => true, 'data' => $result, 'message'  => ''  );
		}
		
	}
}