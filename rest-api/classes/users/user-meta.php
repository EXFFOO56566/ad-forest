<?php
//get URL for post 1's meta
$url = rest_url( 'wp/v2/users/2/meta' );
 exit;
//add basic auth headers (don't use in production)
$headers = array (
	'Authorization' => 'Basic ' . base64_encode( 'admin' . ':' . 'password' ),
);
 
//prepare body of request with meta key/ value
$body = array(
	'key' => 'lightsaber_color',
	'value' => 'blue'
);
 
//make POST request
 
$response = wp_remote_request( $url, array(
		'method' => 'POST',
		'headers' => $headers,
		'body' => $body
	) 
);
 
 
//if response is not an error, echo color and get meta ID of new meta key
$body = wp_remote_retrieve_body( $response );
if ( ! is_wp_error( $body ) ) {
	$body = json_decode( $body );
	$meta_id = $body->id;
	echo "Color is " . $body->value;
	if ( $meta_id ) {
 
		//add meta ID to end of URL
		$url .= '/' . $meta_id;
 
		//this time just need to send value
		$body = array(
 
			'value' => 'green'
 
		);
 
		$response = wp_remote_request( $url, array(
				'method' => 'POST',
				'headers' => $headers,
				'body' => $body
			) 
		);
 
		//if not an error echo new color
		$body = wp_remote_retrieve_body( $response );
		if ( ! is_wp_error( $body ) ) {
			$body = json_decode( $body );
			echo "Color  is  now " . $body->value;
		}
 
	}
	
}