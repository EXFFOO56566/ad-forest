<?php
/*-----	Logout Starts Here ----*/
add_action( 'rest_api_init', 'adforestAPI_logout_api_hooks', 0 );
function adforestAPI_logout_api_hooks() {
    register_rest_route(
        'wp/v2', '/logout/',
        array(
				'methods'  => 'GET',
				'callback' => 'adforestAPI_logoutMe',
				'permission_callback' => function () { return adforestAPI_basic_auth();  },
        	)
    );
    register_rest_route(
        'adforest/v1', '/logout/',
        array(
				'methods'  => 'GET',
				'callback' => 'adforestAPI_logoutMe',
				'permission_callback' => function () { return adforestAPI_basic_auth();  },
        	)
    );	
}

if (!function_exists('adforestAPI_logoutMe'))
{
	function adforestAPI_logoutMe()
	{
		$logout = wp_logout_url();
		$response = array( 'success' => true, 'data' => __("You are logout successfully.", "adforest-rest-api") );
		return $response;	
	}
}