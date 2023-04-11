<?php

/* ----	Register Starts Here ---- */
add_action('rest_api_init', 'adforestAPI_register_api_hooks_post', 0);

function adforestAPI_register_api_hooks_post() {
    register_rest_route('adforest/v1', '/register/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_register_me_post',
        'permission_callback' => function () {
            return true;
        },
            )
    );

    register_rest_route('adforest/v1', '/register_check_user/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_register_check_user',
        'permission_callback' => function () {
            return true;
        },
            )
    );

    register_rest_route('adforest/v1', '/register_otp_user/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_register_otp_user',
        'permission_callback' => function () {
            return true;
        },
            )
    );
}

function adforestAPI_register_me_post($request) {
    $json_data = $request->get_json_params();
    if (empty($json_data) || !is_array($json_data)) {
        $response = array('success' => false, 'data' => '', 'message' => __("Please fill out all fields.", "adforest-rest-api"));
        return rest_ensure_response($response);
    }
    $output = array();

    $from = (isset($json_data['from'])) ? $json_data['from'] : '';
    $name = (isset($json_data['name'])) ? $json_data['name'] : '';
    $email = (isset($json_data['email'])) ? $json_data['email'] : '';
    $phone = (isset($json_data['phone'])) ? $json_data['phone'] : '';
    $password = (isset($json_data['password'])) ? $json_data['password'] : '';
    $user_subscribe = (isset($json_data['user_subscribe'])) ? $json_data['user_subscribe'] : '';

    if ($name == "") {
        //$response = array('success' => false, 'data' => '', 'message' => __("Please enter name.", "adforest-rest-api"));
        //return $response;
    }
    if ($email == "") {
        $response = array('success' => false, 'data' => '', 'message' => __("Please enter email.", "adforest-rest-api"));
        return $response;
    }
    if ($password == "") {
        $response = array('success' => false, 'data' => '', 'message' => __("Please enter password.", "adforest-rest-api"));
        return $response;
    }
    if (email_exists($email) == true) {
        $response = array('success' => false, 'data' => '', 'message' => __("Email Already Exists.", "adforest-rest-api"));
        return $response;
    }

    $username = stristr($email, "@", true);
    /* Generate Username */
    $u_name = adforestAPI_check_username($username);
    /* Register User With WP */
    $uid = wp_create_user($u_name, $password, $email);

    global $adforestAPI;

    /* do_action('adforest_subscribe_newsletter_on_regisster', $adforestAPI, $uid); */
    if (isset($adforestAPI['subscriber_checkbox_on_register']) && $adforestAPI['subscriber_checkbox_on_register'] == true) {
        if ($user_subscribe == true) {
            do_action('adforest_subscribe_newsletter_on_regisster', $adforestAPI, $uid);
        }
    } else {
        do_action('adforest_subscribe_newsletter_on_regisster', $adforestAPI, $uid);
    }



    if (isset($adforestAPI['sb_phone_verification']) && $adforestAPI['sb_phone_verification'] && in_array('wp-twilio-core/core.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        update_user_meta($uid, '_sb_is_ph_verified', '0');
    }

    
     if($name == ""){
         $name   =  $u_name;
     }
    wp_update_user(array('ID' => $uid, 'display_name' => $name));
    update_user_meta($uid, '_sb_contact', $phone);
    if (isset($adforestAPI['sb_allow_ads']) && $adforestAPI['sb_allow_ads']) {

        $freeAds = adforestAPI_getReduxValue('sb_free_ads_limit', '', false);
        $freeAds = ( isset($adforestAPI['sb_allow_ads']) && $adforestAPI['sb_allow_ads'] ) ? $freeAds : 0;
        $featured = adforestAPI_getReduxValue('sb_featured_ads_limit', '', false);
        $featured = ( isset($adforestAPI['sb_allow_featured_ads']) && $adforestAPI['sb_allow_featured_ads'] ) ? $featured : 0;
        $bump = adforestAPI_getReduxValue('sb_bump_ads_limit', '', false);
        $bump = ( isset($adforestAPI['sb_allow_bump_ads']) && $adforestAPI['sb_allow_bump_ads'] ) ? $bump : 0;
        $validity = adforestAPI_getReduxValue('sb_package_validity', '', false);

        update_user_meta($uid, '_sb_simple_ads', $freeAds);
        update_user_meta($uid, '_sb_featured_ads', $featured);
        update_user_meta($uid, '_sb_bump_ads', $bump);

        if ($validity == '-1') {
            update_user_meta($uid, '_sb_expire_ads', $validity);
        } else {
            $expiry_date = date('Y-m-d', strtotime("+$validity days"));
            update_user_meta($uid, '_sb_expire_ads', $expiry_date);
        }
    } else {
        update_user_meta($uid, '_sb_simple_ads', 0);
        update_user_meta($uid, '_sb_featured_ads', 0);
        update_user_meta($uid, '_sb_bump_ads', 0);
        update_user_meta($uid, '_sb_expire_ads', date('Y-m-d'));
    }

    update_user_meta($uid, '_sb_pkg_type', 'free');

    $user_info = get_userdata($uid);
    $profile_arr = array();
    $profile_arr['id'] = $user_info->ID;
    $profile_arr['user_email'] = $user_info->user_email;
    $profile_arr['display_name'] = $user_info->display_name;
    $profile_arr['phone'] = get_user_meta($user_info->ID, '_sb_contact', true);
    $profile_arr['profile_img'] = adforestAPI_user_dp($user_info->ID);

    $message_text = __("Registered successfully.", "adforest-rest-api");
    adforestAPI_email_on_new_user($uid, '');
    if (isset($adforestAPI['sb_new_user_email_verification']) && $adforestAPI['sb_new_user_email_verification']) {
        $message_text = __("Registered successfully. Please verify your email address.", "adforest-rest-api");
        /* Remove User Role For Email Verifications */
        $user = new WP_User($uid);
        foreach ($user->roles as $role) {
            $user->remove_role($role);
        }
    }

    $response = array('success' => true, 'data' => $profile_arr, 'message' => $message_text);
    return $response;
}

add_action('rest_api_init', 'adforestAPI_register_api_hooks_get', 0);

function adforestAPI_register_api_hooks_get() {
    register_rest_route('adforest/v1', '/register/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_register_me_get',
        'permission_callback' => function () {
            return true;
        },
            )
    );
}

if (!function_exists('adforestAPI_register_me_get')) {

    function adforestAPI_register_me_get() {
        global $adforestAPI;
        $data['bg_color'] = '#000';
        $data['logo'] = adforestAPI_appLogo();
        $data['heading'] = __("Register With Us!", "adforest-rest-api");
        $data['name_placeholder'] = __("Full Name", "adforest-rest-api");
        $data['email_placeholder'] = __("Email Address", "adforest-rest-api");
        $data['phone_placeholder'] = __("Phone Number", "adforest-rest-api");
        $data['password_placeholder'] = __("Password", "adforest-rest-api");
        $data['form_btn'] = __("Register", "adforest-rest-api");
        $data['separator'] = __("OR", "adforest-rest-api");
        $data['facebook_btn'] = __("Continue with Facebook", "adforest-rest-api");
        $data['google_btn'] = __("Continue with Google", "adforest-rest-api");
        $data['linkedin_btn'] = __("Continue with Linkedin", "adforest-rest-api");
        $data['apple_btn'] = __("Continue with Apple", "adforest-rest-api");
        $data['email_btn'] = __("Continue with Email", "adforest-rest-api");
        $data['phone_btn'] = __("Continue with Phone", "adforest-rest-api");
        $data['login_text'] = __("Already Have Account? Login Here", "adforest-rest-api");
        $data['phone_email'] = __("Enter your Email / Phone Number", "adforest-rest-api");

        $subscriber_is_show = false;
        $subscriber_text = '';
        if (isset($adforestAPI['subscribe_on_user_register']) && $adforestAPI['subscribe_on_user_register'] == true) {

            if (isset($adforestAPI['subscriber_checkbox_on_register']) && $adforestAPI['subscriber_checkbox_on_register'] == true) {
                $subscriber_is_show = true;
                $subscriber_text = $adforestAPI['subscriber_checkbox_on_register_text'];
            }
        }

        $data['subscriber_is_show'] = $subscriber_is_show;
        $data['subscriber_checkbox'] = 'user_subscribe';
        $data['subscriber_checkbox_text'] = $subscriber_text;

        $verified = (isset($adforestAPI['sb_new_user_email_verification']) && $adforestAPI['sb_new_user_email_verification'] == false) ? false : true;
        $data['is_verify_on'] = $verified;
        $data['term_page_id'] = (isset($adforestAPI['sb_new_user_register_policy'])) ? $adforestAPI['sb_new_user_register_policy'] : '';
        $checkbox_text = (isset($adforestAPI['sb_new_user_register_checkbox_text']) && $adforestAPI['sb_new_user_register_checkbox_text'] != "") ? $adforestAPI['sb_new_user_register_checkbox_text'] : __("Agree With Our Term and Conditions.", "adforest-rest-api");
        $data['terms_text'] = $checkbox_text;


             $data['phone_verification'] = __("Phone verification", "adforest-rest-api");
        $data['otp_text'] = __("We will send you one time OTP on your device.", "adforest-rest-api");
         $data['welcome_txt'] = isset($adforestAPI['welcome_txt'])? $adforestAPI['welcome_txt'] : "";
        $data['intro_text'] = isset($adforestAPI['intro_text'])? $adforestAPI['intro_text'] : "";

        $data['page_title']   =  __("Register", "adforest-rest-api");
        $data['phone_number']   =  __("Enter phone Number", "adforest-rest-api");
        $data['input_number']   =  __("Please input phone number", "adforest-rest-api");
        $data['phone_number']   =  __("Enter phone Number", "adforest-rest-api");
         $data['phone_email']   =  __("Email/Phone Number", "adforest-rest-api");
        $data['code_sent'] = esc_html__('Verification code is sent to','adforest-rest-api');
        $data['not_received'] = esc_html__('Didn,t receive a code?','adforest-rest-api');
        $data['try_again'] = esc_html__('Try again','adforest-rest-api');       
        $data['verify_number'] = esc_html__('Verify number','adforest-rest-api');
        $data['verify_success'] = esc_html__('Verified successfully','adforest-rest-api');

       
        return $response = array('success' => true, 'data' => $data, 'message' => '');
    }

}

/* Forgot */
add_action('rest_api_init', 'adforestAPI_forgot_api_hooks_get', 0);

function adforestAPI_forgot_api_hooks_get() {

    register_rest_route('adforest/v1', '/forgot/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_forgot_me_get',
        'permission_callback' => function () {
            return true;
        },
            )
    );
}

if (!function_exists('adforestAPI_forgot_me_get')) {

    function adforestAPI_forgot_me_get() {
        $data['bg_color'] = '#000';
        $data['logo'] = adforestAPI_appLogo();
        $data['heading'] = __("Forgot Password?", "adforest-rest-api");
        $data['text'] = __("Please enter your email address below.", "adforest-rest-api");
        $data['email_placeholder'] = __("Email Address", "adforest-rest-api");
        $data['submit_text'] = __("Submit", "adforest-rest-api");
        $data['back_text'] = __("Back", "adforest-rest-api");
        return $response = array('success' => true, 'data' => $data, 'message' => '');
    }

}


/* check user name and phone number not already registrerd */

function adforestAPI_register_check_user($request) {

    global $wpdb;
    $json_data = $request->get_json_params();
    $user_name = (isset($json_data['name'])) ? $json_data['name'] : '';
    $phone = (isset($json_data['phone'])) ? $json_data['phone'] : '';

    if ($phone == "") {
        return $response = array('success' => false, 'data' => array(), 'message' => esc_html__('Please Enter Phone Numer', 'adforest-rest-api'));
    }
    
    $query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_sb_contact' AND meta_value  =  '$phone'";

   

    $result = $wpdb->get_results($query);
   
    if (isset($result) && !empty($result)) {   

                    
          return $response = array('success' => false, 'data' => $result, 'message' => esc_html__('Phone Number already exist','adforest'));
    }
    return $response = array('success' => true, 'data' => array(), 'message' => esc_html__('No issue', 'adforest'));
}

/* Register user with phone number and user name */
if (!function_exists('adforestAPI_register_otp_user')) {

    function adforestAPI_register_otp_user($request) {

         global $adforestAPI;
        $json_data = $request->get_json_params();
        $phone = (isset($json_data['phone'])) ? $json_data['phone'] : '';
        $random = mt_rand(0, 1000);
      

         $user_name = (isset($json_data['name'])) ? $json_data['name'] : '';

         if($user_name = ""){
         $user_name = isset($adforestAPI['sb_register_user_txt']) ? $adforestAPI['sb_register_user_txt']  : "user";
           $user_name = $user_name . '-' . $random;
         }

        $user_name = adforestAPI_check_user_name($user_name);
        $password    = wp_generate_password(12);
        $info = array();
        $info['user_login']    = $user_name;
        $info['user_nicename'] = $user_name;
        $info['user_pass']     = $password;
        if ($phone == "") {
            return $response = array('success' => false, 'data' => array(), 'message' => esc_html__('Please Enter Valid Phone Number', 'adforest-rest-api'));
        }
        
        $user_id = wp_insert_user($info);
        if (is_wp_error($user_id)) {

            return $response = array('success' => false, 'data' => array(), 'message' => $user_id->get_error_message());
        }
        $saved_num = update_user_meta($user_id, '_sb_contact', $phone);                
        global $adforestAPI;

        /* do_action('adforest_subscribe_newsletter_on_regisster', $adforestAPI, $uid); */
        if (isset($adforestAPI['subscriber_checkbox_on_register']) && $adforestAPI['subscriber_checkbox_on_register'] == true) {
            if ($user_subscribe == true) {
                do_action('adforest_subscribe_newsletter_on_regisster', $adforestAPI, $uid);
            }
        } else {
            do_action('adforest_subscribe_newsletter_on_regisster', $adforestAPI, $uid);
        }
        if (isset($adforestAPI['sb_phone_verification']) && $adforestAPI['sb_phone_verification'] && in_array('wp-twilio-core/core.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            update_user_meta($uid, '_sb_is_ph_verified', '0');
        }   
        if (isset($adforestAPI['sb_allow_ads']) && $adforestAPI['sb_allow_ads']) {
            $freeAds = adforestAPI_getReduxValue('sb_free_ads_limit', '', false);
            $freeAds = ( isset($adforestAPI['sb_allow_ads']) && $adforestAPI['sb_allow_ads'] ) ? $freeAds : 0;
            $featured = adforestAPI_getReduxValue('sb_featured_ads_limit', '', false);
            $featured = ( isset($adforestAPI['sb_allow_featured_ads']) && $adforestAPI['sb_allow_featured_ads'] ) ? $featured : 0;
            $bump = adforestAPI_getReduxValue('sb_bump_ads_limit', '', false);
            $bump = ( isset($adforestAPI['sb_allow_bump_ads']) && $adforestAPI['sb_allow_bump_ads'] ) ? $bump : 0;
            $validity = adforestAPI_getReduxValue('sb_package_validity', '', false);

            update_user_meta($user_id, '_sb_simple_ads', $freeAds);
            update_user_meta($user_id, '_sb_featured_ads', $featured);
            update_user_meta($user_id, '_sb_bump_ads', $bump);

            if ($validity == '-1') {
                update_user_meta($user_id, '_sb_expire_ads', $validity);
            } else {
                $expiry_date = date('Y-m-d', strtotime("+$validity days"));
                update_user_meta($user_id, '_sb_expire_ads', $expiry_date);
            }
        } else {
            update_user_meta($user_id, '_sb_simple_ads', 0);
            update_user_meta($user_id, '_sb_featured_ads', 0);
            update_user_meta($user_id, '_sb_bump_ads', 0);
            update_user_meta($user_id, '_sb_expire_ads', date('Y-m-d'));
        }       
        update_user_meta($user_id, '_sb_pkg_type', 'free');
        update_user_meta($user_id, '_sb_is_ph_verified', '1');

         adforestAPI_email_on_new_user($user_id, '');
        $user_info = get_userdata($user_id);
        $profile_arr = array();
        $profile_arr['id'] = $user_info->ID;
        $profile_arr['user_email']     = $phone;
        $profile_arr['display_name']   = $user_info->display_name;
        $profile_arr['phone'] = get_user_meta($user_info->ID, '_sb_contact', true);
        $profile_arr['profile_img']    = adforestAPI_user_dp($user_info->ID);
        $profile_arr['user_login']     =   $user_name;
        return $response = array('success' => true, 'data' => $profile_arr, 'message' => esc_html__('User Registered Succesfully', 'adforest-rest-api'));
    }

}
                
