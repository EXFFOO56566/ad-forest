<?php

/* -----	Login Starts Here	----- */
add_action('rest_api_init', 'adforestAPI_login_api_hooks_post', 0);

function adforestAPI_login_api_hooks_post() {
    register_rest_route('adforest/v1', '/login/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_loginMe_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route('adforest/v1', '/login_check_user/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_login_check_user',
        'permission_callback' => function () {
            return true;
        },
            )
    );
    register_rest_route('adforest/v1', '/login_otp_user/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_login_user_with_otp',
        'permission_callback' => function () {
            return true;
        },
            )
    );
}
if (!function_exists('adforestAPI_loginMe_post')) {

    function adforestAPI_loginMe_post($request) {
        $json_data = $request->get_json_params();
        $email = (isset($json_data['email'])) ? $json_data['email'] : '';
        $password = (isset($json_data['password'])) ? $json_data['password'] : '';
        $remember = (isset($json_data['remember'])) ? $json_data['remember'] : '';
        $type = (isset($json_data['type'])) ? $json_data['type'] : 'normal';
        $LinkedIn_img = (isset($json_data['LinkedIn_img'])) ? $json_data['LinkedIn_img'] : '';

        $creds = array();
        $creds['remember'] = $remember;
        $creds['user_login'] = $email;
        $creds['user_password'] = $password;

        if ($type == 'social') {
            $user = get_user_by('email', $email);
            if ($user) {
                $user_id = $user->ID;
                $profile_arr = array();
                $profile_arr['id'] = $user->ID;
                $profile_arr['user_email'] = $user->user_email;
                $profile_arr['display_name'] = $user->display_name;
                $profile_arr['phone'] = get_user_meta($user->ID, '_sb_contact', true);

                if (isset($LinkedIn_img) && $LinkedIn_img != '') {
                    $profile_arr['profile_img'] = $LinkedIn_img;
                    update_user_meta($user->ID, '_sb_user_linkedin_pic', $LinkedIn_img);
                } else {
                    $profile_arr['profile_img'] = adforestAPI_user_dp($user->ID);
                }
                adforestAPI_setLastLogin2($user->ID);
                $response = array('success' => true, 'data' => $profile_arr, 'message' => __("Login Successfull", "adforest-rest-api"));
            } else {
                $response = array('success' => false, 'data' => '', 'message' => __("Something went wrong", "adforest-rest-api"));
            }
        } else {
            $user = wp_signon($creds, false);
            if (is_wp_error($user)) {
                if (isset($user->errors['incorrect_password'])) {
                    $response = array('success' => false, 'data' => $profile_arr, 'message' => __('Invalid Login Details', 'adforest-rest-api'));
                } else if (isset($user->errors['not_verified_user'])) {
                    $user = get_user_by('email', $email);
                    $profile_arr['id'] = $user->ID;
                    $profile_arr['is_account_confirm'] = false;
                    $response = array('success' => false, 'data' => $profile_arr, 'message' => __('Your account is not verified yet.', 'adforest-rest-api'));
                } else {
                    $response = array('success' => false, 'data' => $profile_arr, 'message' => wp_strip_all_tags($user->get_error_message()));
                }
            } else {
                $profile_arr = array();
                $profile_arr['id'] = $user->ID;
                $profile_arr['user_email'] = $user->user_email;
                $profile_arr['display_name'] = $user->display_name;
                $profile_arr['phone'] = get_user_meta($user->ID, '_sb_contact', true);
                $profile_arr['profile_img'] = adforestAPI_user_dp($user->ID);
                $profile_arr['is_account_confirm'] = true;
                adforestAPI_setLastLogin2($user->ID);
                global $adforestAPI;
                if (isset($adforestAPI['sb_new_user_email_verification']) && $adforestAPI['sb_new_user_email_verification']) {
                    $token = get_user_meta($user->ID, 'sb_email_verification_token', true);
                    if ($token && $token != "") {
                        $profile_arr['is_account_confirm'] = false;
                        return array('success' => true, 'data' => $profile_arr, 'message' => __("Please verify your email address to login.", "adforest-rest-api"));
                    }
                }
                $response = array('success' => true, 'data' => $profile_arr, 'message' => __("Login Successfull", "adforest-rest-api"));
            }
        }
        return $response;
    }

}
/* add_action('wp_login', 'adforestAPI_setLastLogin'); */
if (!function_exists('adforestAPI_setLastLogin')) {
    function adforestAPI_setLastLogin($login, $user) {
        $cur_user = get_user_by('login', $login);
        update_user_meta($cur_user->ID, '_sb_last_login', time());
    }
}
if (!function_exists('adforestAPI_setLastLogin2')) {

    function adforestAPI_setLastLogin2($userID = '') {
        update_user_meta($userID, '_sb_last_login', time());
    }

}
add_action('rest_api_init', 'adforestAPI_login_api_hooks_get', 0);
function adforestAPI_login_api_hooks_get() {
    register_rest_route('adforest/v1', '/login/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_loginMe_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}
if (!function_exists('adforestAPI_loginMe_get')) {

    function adforestAPI_loginMe_get() {
        global $adforestAPI;
        $data['bg_color'] = '#000';
        $data['logo'] = adforestAPI_appLogo();
        $data['heading'] = __("Welcome Back", "adforest-rest-api");
        $data['email_placeholder'] = __("Your Email Address", "adforest-rest-api");
        $data['password_placeholder'] = __("Your Password", "adforest-rest-api");
        $data['forgot_text'] = __("Forgot Password", "adforest-rest-api");
        $data['form_btn'] = __("Submit", "adforest-rest-api");
        $data['separator'] = __("OR", "adforest-rest-api");
        $data['facebook_btn'] = __("Continue with Facebook", "adforest-rest-api");
        $data['google_btn'] = __("Continue with Google", "adforest-rest-api");
        $data['linkedin_btn'] = __("Continue with Linkedin", "adforest-rest-api");
        $data['apple_btn'] = __("Continue with Apple", "adforest-rest-api");
        $data['email_btn'] = __("Continue with Email", "adforest-rest-api");
        $data['phone_btn'] = __("Continue with Phone", "adforest-rest-api");
        $data['register_text'] = __("Not a Member Yet? Register with us.", "adforest-rest-api");
        $data['guest_login'] = __("Guest Login", "adforest-rest-api");
        $data['guest_text'] = __("Guest", "adforest-rest-api");
        $verified = (isset($adforestAPI['sb_new_user_email_verification']) && $adforestAPI['sb_new_user_email_verification'] == false) ? false : true;
        $data['is_verify_on'] = $verified;
        $data['phone_email'] = __("Enter your Email / Phone Number", "adforest-rest-api");


        $data['phone_verification'] = __("Phone verification", "adforest-rest-api");
        $data['otp_text'] = __("We will send you one time OTP on your device.", "adforest-rest-api");
        $data['welcome_txt'] = isset($adforestAPI['welcome_txt'])? $adforestAPI['welcome_txt'] : "";
        $data['intro_text'] = isset($adforestAPI['intro_text'])? $adforestAPI['intro_text'] : "";
        $data['page_title']   =  __("Login", "adforest-rest-api");
        $data['register']   =  __("Register", "adforest-rest-api");
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
add_action('rest_api_init', 'adforestAPI_profile_forgotpass_hooks_post', 0);
function adforestAPI_profile_forgotpass_hooks_post() {
    register_rest_route(
            'adforest/v1', '/forgot/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_forgotpass_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_forgotpass_post')) {

    function adforestAPI_profile_forgotpass_post($request) {
        $json_data = $request->get_json_params();
        $email = (isset($json_data['email'])) ? trim($json_data['email']) : '';
        if (ADFOREST_API_ALLOW_EDITING == false) {
            $response = array('success' => false, 'data' => '', 'message' => __("Editing Not Alloded In Demo", "adforest-rest-api"));
            return $response;
        }

        if (email_exists($email) == true) {
            $my_theme = wp_get_theme();
            if ($my_theme->get('Name') != 'adforest' && $my_theme->get('Name') != 'adforest child') {
                $response = adforestAPI_forgot_pass_email_text($email);
            } else {
                $response = adforestAPI_forgot_pass_email_link($email);
            }
        } else {
            $success = false;
            $message = __('Email is not resgistered with us.', 'adforest-rest-api');
            $response = array('success' => $success, 'data' => '', 'message' => $message);
        }
        return $response;
    }
}
/* Account Confirmation */
add_action('rest_api_init', 'adforestAPI_login_confirm_api_hooks_get', 0);
function adforestAPI_login_confirm_api_hooks_get() {
    register_rest_route('adforest/v1', '/login/confirm/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_login_confirm_get',
        'permission_callback' => function () {
            return true;
        },
            )
    );
    register_rest_route('adforest/v1', '/login/confirm/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_login_confirm_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route('adforest/v1', '/login/confirm/resend/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_resend_email_func',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_resend_email_func')) {
    function adforestAPI_resend_email_func($request) {
        $data = array();
        $json_data = $request->get_json_params();
        $user_id = (isset($json_data['user_id'])) ? $json_data['user_id'] : '';

        if ($user_id == "") {
            $response = array('success' => false, 'data' => $data, 'message' => __("Something went wrong", "adforest-rest-api"));
        }
        $user = get_user_by('id', $user_id);
        if (!is_wp_error($user)) {

            if (get_user_meta($user->ID, 'sb_resent_email', true) != 'yes') {
                adforestAPI_email_on_new_user($user->ID, '', false);
                update_user_meta($user->ID, 'sb_resent_email', 'yes');
                $response = array('success' => true, 'data' => $data, 'message' => __("Confirmation email sent", "adforest-rest-api"));
            } else {
                $response = array('success' => false, 'data' => $data, 'message' => __("Please contact with admin.", "adforest-rest-api"));
            }
        } else {
            $response = array('success' => false, 'data' => $data, 'message' => __("Something went wrong", "adforest-rest-api"));
        }
        return $response;
    }
}
if (!function_exists('adforestAPI_login_confirm_get')) {
    function adforestAPI_login_confirm_get() {
        global $adforestAPI;
        $data['bg_color'] = '#000';
        $data['logo'] = adforestAPI_appLogo();
        $data['heading'] = __("Account Confirmation", "adforest-rest-api");
        $data['text'] = __("Please enter your confirmation code below.", "adforest-rest-api");
        $data['confirm_placeholder'] = __("Confirmation Code Here", "adforest-rest-api");
        $data['submit_text'] = __("Confirm Account", "adforest-rest-api");
        $data['back_text'] = __("Back", "adforest-rest-api");
        $data['confirmation_text'] = __("Did not get an email?", "adforest-rest-api");
        $data['confirmation_resend'] = __("Resend now", "adforest-rest-api");
        $data['confirmation_contact_admin'] = __("Contact with admin", "adforest-rest-api");
        $admin_contact_page_id = (isset($adforestAPI['admin_contact_page'])) ? $adforestAPI['admin_contact_page'] : '';
        $page_title = __("Contact Us", "adforest-rest-api");
        if ($admin_contact_page_id != "") {
            $page_title = get_the_title($admin_contact_page_id);
        }
        $data['contact_page_title'] = $page_title;
        $data['contact_page_id'] = ($admin_contact_page_id != "") ? get_the_permalink($admin_contact_page_id) : "";
        return $response = array('success' => true, 'data' => $data, 'message' => '');
    }
}

if (!function_exists('adforestAPI_login_confirm_post')) {

    function adforestAPI_login_confirm_post($request) {
        $json_data = $request->get_json_params();
        $confirm_code = (isset($json_data['confirm_code'])) ? $json_data['confirm_code'] : '';
        $user_id = (isset($json_data['user_id'])) ? $json_data['user_id'] : '';
        if ($user_id == "") {
            $message = __('Invalid Access', 'adforest-rest-api');
            return $response = array('success' => false, 'data' => '', 'message' => $message);
        }
        if ($confirm_code == "") {
            $message = __('Please enter the confirmation code.', 'adforest-rest-api');
            return $response = array('success' => false, 'data' => '', 'message' => $message);
        }
        $token = get_user_meta($user_id, 'sb_email_verification_token', true);
        if ($token && $confirm_code != $token) {
            $message = __('You eneter invalid confirmation code.', 'adforest-rest-api');
            return $response = array('success' => false, 'data' => '', 'message' => $message);
        } else if ($token && $confirm_code == $token) {
            update_user_meta($user_id, 'sb_email_verification_token', '');
            /* Set the user's role after email verification . */
            $user = new WP_User($user_id);
            $user->set_role('subscriber');
            $message = __('You account confirmed successfully.', 'adforest-rest-api');
            return $response = array('success' => true, 'data' => '', 'message' => $message);
        } else {
            $message = __('Invalid Access or token code.', 'adforest-rest-api');
            return $response = array('success' => false, 'data' => '', 'message' => $message);
        }
    }

}

/* check user name and phone number not already registrerd */
function adforestAPI_login_check_user($request) {

    global $wpdb;
    $json_data = $request->get_json_params();
    $user_name = (isset($json_data['name'])) ? $json_data['name'] : '';
    $phone = (isset($json_data['phone'])) ? $json_data['phone'] : '';   
    $data   =   array();
    if ($phone == "") {
        return $response = array('success' => false, 'data' => array(), 'message' => esc_html__('Please Enter Valid Phone Number', 'adforest-rest-api'));
    } 
      $query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_sb_contact' AND meta_value  =  '$phone'";
      $result = $wpdb->get_results($query);  
        if (isset($result) && !empty($result)) {
            $user_id = isset($result[0]->user_id) ? $result[0]->user_id : "";            
            if($user_id != ""){             
                $secure_token  =   mt_rand(0,1000);
                update_user_meta($user_id, 'secure_token', $secure_token);
                 $user = get_user_by('ID', $user_id);
                 $user_login   =   $user->user_login;          
                $data['user_login']   = $user_login;
                $data['token']   =   $secure_token;
                $data['display_name']   =   $user->display_name;
               return $response = array('success' => true, 'data' => $data , 'message' => esc_html__('proceed', 'adforest-rest-api'));              
            }
        }
        return $response = array('success' => false, 'data' => array(), 'message' => esc_html__('Phone Number not registered', 'adforest-rest-api'));  
}
/* login user with otp */
function adforestAPI_login_user_with_otp($request) {
    global $wpdb;
    $json_data = $request->get_json_params();
    $user_name = (isset($json_data['name'])) ? $json_data['name'] : '';
    $phone = (isset($json_data['phone'])) ? $json_data['phone'] : '';
    if ($user_name == "") {
        return $response = array('success' => false, 'data' => array(), 'message' => esc_html__('Please Enter user name', 'adforest-rest-api'));
    }
    if (!username_exists($user_name)) {
        return $response = array('success' => false, 'data' => array(), 'message' => esc_html__('User name does not exists', 'adforest-rest-api'));
    }
    $user = get_user_by('login', $user_name);
    $user_id = isset($user->ID) ? $user->ID : "";
    if ($user_id != "") {
        $profile_arr = array();
        $profile_arr['id'] = $user->ID;
        $profile_arr['user_email'] = $phone;
        $profile_arr['display_name'] = $user->display_name;
        $profile_arr['phone'] = get_user_meta($user->ID, '_sb_contact', true);
        $profile_arr['profile_img'] = adforestAPI_user_dp($user->ID);
        $profile_arr['is_account_confirm'] = true;
        $profile_arr['user_login'] = $user_name;
        adforestAPI_setLastLogin2($user->ID);
        return $response = array('success' => true, 'data' => $profile_arr, 'message' => esc_html__('You are successfully logged in', 'adforest-rest-api'));
    }
    return $response = array('success' => true, 'data' => array(), 'message' => esc_html__('Something went wrong', 'adforest-rest-api'));
}
