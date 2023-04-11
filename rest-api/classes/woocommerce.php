<?php
/*----- Woo Products Starts Here -----*/
add_action( 'rest_api_init', 'adforestAPI_woocommerce_hook', 0 );
function adforestAPI_woocommerce_hook() {
    register_rest_route(
        		'adforest/v1', '/packages/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'adforestAPI_woocommerce_get',
				'permission_callback' => function () { return adforestAPI_basic_auth();  },
        	)
    );
} 
if (!function_exists('adforestAPI_woocommerce_get'))
{
	function adforestAPI_woocommerce_get( $request )
	{ 
		$user = wp_get_current_user();	
		$user_id = $user->data->ID;		
		$response = array( 'success' => $success, 'data' => $data, 'message' => $message, 'extra' => $extra );
		return $response;
	}
}