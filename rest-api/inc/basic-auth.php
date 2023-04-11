<?php

function json_basic_auth_handler($user) {

    global $wp_json_basic_auth_error;
    $wp_json_basic_auth_error = null;

    // Don't authenticate twice
    if (!empty($user)) {
        //return $user;
    }

   

    $is_uname_null = false;
    if (isset($_SERVER['PHP_AUTH_USER'])) {
        //AdForest-Login-Type
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        $email = $username;
    } else {
        //echo "ssssssssss";

        if (isset($_SERVER['HTTP_PURCHASE_CODE'])) {
            
        } else {
            /* Only auth if it's not from apps */
            if (!empty($user)) {
                return $user;
            }
        }

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authUserData = explode(':', base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], 6)));
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authUserData = explode(':', base64_decode(substr($_SERVER["REDIRECT_HTTP_AUTHORIZATION"], 6)));
        } else if (isset($_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION'])) {
            $authUserData = explode(':', base64_decode(substr($_SERVER["REDIRECT_REDIRECT_HTTP_AUTHORIZATION"], 6)));
        }



        $is_uname_null = (isset($authUserData[0]) && $authUserData[0] != "" ) ? false : true;
        $username = @$authUserData[0];
        $password = @$authUserData[1];
        $email = $username;
    }



    // Check that we're trying to authenticate
      if (!isset($username) ||   $username == "" ) {
        return $user;
    }




    /**
     * In multi-site, wp_authenticate_spam_check filter is run on authentication. This filter calls
     * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
     * recursion and a stack overflow unless the current function is removed from the determine_current_user
     * filter during authentication.
     */
    remove_filter('determine_current_user', 'json_basic_auth_handler', 20);
    $user_id = '';
  
    if (isset($_SERVER['HTTP_ADFOREST_LOGIN_TYPE']) && $_SERVER['HTTP_ADFOREST_LOGIN_TYPE'] == 'social') {


        if (email_exists($username) == true) {
            $user = get_user_by('email', $username);
            $user_id = $user->ID;
            if ($user) {

                add_action('wp_authenticate', 'wp_authenticate_by_email');
                add_filter('determine_current_user', 'json_basic_auth_handler', 20);
                if (is_wp_error($user)) {
                    $wp_json_basic_auth_error = $user;
                    return null;
                }
                $wp_json_basic_auth_error = true;
                //  adforestAPI_setLastLogin2($user->ID);
                return $user->ID;
            }
        } else {
            // Here we need to register user.
            $password = mt_rand(1000, 999999);
            $uid = adforestAPI_do_register($username, $password);

            if (function_exists('adforest_email_on_new_user')) {
                adforestAPI_email_on_new_user($uid, $password);
            }

            $user = get_user_by('email', $username);
            $user_id = $user->ID;
            if ($user) {

                add_action('wp_authenticate', 'wp_authenticate_by_email');
                add_filter('determine_current_user', 'json_basic_auth_handler', 20);
                if (is_wp_error($user)) {
                    $wp_json_basic_auth_error = $user;
                    return null;
                }
                $wp_json_basic_auth_error = true;
                adforestAPI_setLastLogin2($user->ID);
                return $user->ID;
            }
        }

        return $user_id;
    } else {

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return $user;
        }

        if (isset($_SERVER['HTTP_ADFOREST_LOGIN_TYPE']) && $_SERVER['HTTP_ADFOREST_LOGIN_TYPE'] == 'otp') {   

            $user = get_user_by('login', $username);
            $user_id = $user->ID;
            if ($user) {

                add_action('wp_authenticate', 'wp_authenticate_by_login');
                add_filter('determine_current_user', 'json_basic_auth_handler', 20);
                if (is_wp_error($user)) {
                    $wp_json_basic_auth_error = $user;
                    return null;
                }
                $wp_json_basic_auth_error = true;
                adforestAPI_setLastLogin2($user->ID);
                return $user->ID;
            }
            return $user_id;
        }
        
        $user = wp_authenticate($username, $password);
        add_filter('determine_current_user', 'json_basic_auth_handler', 20);
        if (is_wp_error($user)) {

            //$wp_json_basic_auth_error = $user;
            if (isset($wp_json_basic_auth_error->errors['incorrect_password']))
            //$wp_json_basic_auth_error->errors['incorrect_password'] = __("Invalid Login Details.", "adforest-rest-api");
                if (isset($wp_json_basic_auth_error->errors['invalid_username']))
                //$wp_json_basic_auth_error->errors['invalid_username'] = __("Invalid Login Details.", "adforest-rest-api");
                    return null;
        } else {
            $wp_json_basic_auth_error = true;
              return $user->ID;
        }
    }
}

add_filter('determine_current_user', 'json_basic_auth_handler', 20);

function json_basic_auth_error($error) {
    // Passthrough other errors


    if (!empty($error)) {

        return $error;
    }

    global $wp_json_basic_auth_error;
    return $wp_json_basic_auth_error;
}

add_filter('rest_authentication_errors', 'json_basic_auth_error');

if (!function_exists('wp_authenticate_by_email')) {

    function wp_authenticate_by_email(&$username) {
        $user = get_user_by('email', $username);

        if (!$user) {
            $username = $user->user_login;
        }
    }

}


if (!function_exists('wp_authenticate_by_login')) {

    function wp_authenticate_by_login(&$username) {
        $user = get_user_by('login', $username);

        if (!$user) {
            $username = $user->user_login;
        }
    }

}




if (!function_exists('convert_array_to_obj_recursive')) {

    function convert_array_to_obj_recursive($a) {
        if (is_array($a)) {
            foreach ($a as $k => $v) {
                if (is_integer($k)) {
                    // only need this if you want to keep the array indexes separate
                    // from the object notation: eg. $o->{1}
                    $a['index'][$k] = convert_array_to_obj_recursive($v);
                } else {
                    $a[$k] = convert_array_to_obj_recursive($v);
                }
            }

            return (object) $a;
        }

        // else maintain the type of $a
        return $a;
    }

}

