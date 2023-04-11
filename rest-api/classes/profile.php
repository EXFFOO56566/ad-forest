<?php
/* ----- 	Profile Starts Here	 ----- */
add_action('rest_api_init', 'adforestAPI_reward_api_ads_update', 0);

function adforestAPI_reward_api_ads_update() {
    register_rest_route(
            'adforest/v1', '/profile/reward/update', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_reward_ads_update',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

function adforestAPI_reward_ads_update($request) {
    global $adforestAPI;

    $json_data = $request->get_json_params();
    $is_update = isset($json_data['is_update']) && $json_data['is_update'] ? TRUE : FALSE;
    $user_id = isset($json_data['user_id']) && $json_data['user_id'] != '' ? $json_data['user_id'] : '';

    $success_result = FALSE;
    $message = __("Something went wrong.", "adforest-rest-api");

    $featured_reward_ads = isset($adforestAPI['featured_reward_ads']) ? $adforestAPI['featured_reward_ads'] : '1';
    $bumpup_reward_ads = isset($adforestAPI['bumpup_reward_ads']) ? $adforestAPI['bumpup_reward_ads'] : '1';
    $free_reward_ads = isset($adforestAPI['free_reward_ads']) ? $adforestAPI['free_reward_ads'] : '1';

    if ($user_id != '' && $is_update) {
        $success_result = TRUE;


        $feature_ads = get_user_meta($user_id, '_sb_featured_ads', true);
        if (isset($feature_ads) && $feature_ads != '' && $feature_ads != '-1' && $feature_ads >= 0) {
            $featured_ads = $featured_ads + $featured_reward_ads;
            update_user_meta($user_id, '_sb_featured_ads', $featured_ads);
        }


        $_sb_bump_ads = get_user_meta($user_id, '_sb_bump_ads', true);
        if (isset($_sb_bump_ads) && $_sb_bump_ads != '' && $_sb_bump_ads != '-1' && $_sb_bump_ads >= 0) {
            $_sb_bump_ads = $_sb_bump_ads + $bumpup_reward_ads;
            update_user_meta($user_id, '_sb_bump_ads', $_sb_bump_ads);
        }

        $_sb_simple_ads = get_user_meta($user_id, '_sb_simple_ads', true);
        if (isset($_sb_simple_ads) && $_sb_simple_ads != '' && $_sb_simple_ads != '-1' && $_sb_simple_ads >= 0) {
            $_sb_simple_ads = $_sb_simple_ads + $free_reward_ads;
            update_user_meta($user_id, '_sb_simple_ads', $_sb_simple_ads);
        }

        $message = __("Reward Ads Update Successfully.", "adforest-rest-api");
    }


    $data['reward_featured_ads'] = $featured_reward_ads;
    $data['reward_bumpup_ads'] = $bumpup_reward_ads;
    $data['reward_simple_ads'] = $free_reward_ads;


    $response = array('success' => $success_result, 'message' => $message, 'data' => $data);
    return $response;
}

add_action('rest_api_init', 'adforestAPI_profile_api_update_img', 0);

function adforestAPI_profile_api_update_img() {
    register_rest_route(
            'adforest/v1', '/profile/image', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_update_img',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

function my_custom_upload_mimes_here($mimes = array()) {
    $mimes['image'] = "image/jpeg";
    return $mimes;
}

add_action('upload_mimes', 'my_custom_upload_mimes_here');

if (!function_exists('adforestAPI_profile_update_img')) {

    function adforestAPI_profile_update_img($request) {
        $user = wp_get_current_user();
        $user_id = @$user->data->ID;
        if ($user) {

            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            define('ALLOW_UNFILTERED_UPLOADS', true);
            $attach_id = media_handle_upload('profile_img', 0);
            /*             * ***** Assign image to user *********** */

            if (is_wp_error($attach_id)) {
                $response = array('success' => false, 'data' => '', 'message' => __("Something went wrong while uploading image.", "adforest-rest-api"),);
            } else {

                update_user_meta($user_id, '_sb_user_pic', $attach_id);
                $image_link = wp_get_attachment_image_src($attach_id, 'adforest-user-profile');
                $profile_arr = array();
                $profile_arr['id'] = $user->ID;
                $profile_arr['user_email'] = $user->user_email;
                $profile_arr['display_name'] = $user->display_name;
                $profile_arr['phone'] = get_user_meta($user->ID, '_sb_contact', true);
                $profile_arr['profile_img'] = $image_link[0];
                $response = array('success' => true, 'data' => $profile_arr, 'message' => __("Profile image updated successfully", "adforest-rest-api"));
            }
        } else {
            $response = array('success' => false, 'data' => '', 'message' => __("You must be login to update the profile image.", "adforest-rest-api"), "extra" => '');
        }

        return $response;
    }

}

if (!function_exists('adforestAPI_profile_update_img11')) {

    function adforestAPI_profile_update_img11($request) {
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        //if ( ! function_exists( 'wp_handle_upload' ) ){
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        //}
        $uploadedfile = $_FILES['profile_img'];
        /*         * ***** user_photo Upload code *********** */
        $upload_overrides = array('test_form' => false);
        $movefile = media_handle_upload($uploadedfile, $upload_overrides);

        /*         * ***** Assign image to user *********** */
        $filename = $movefile['url'];
        $absolute_file = $movefile['file'];

        $extraData = wp_read_image_metadata($filename);

        $parent_post_id = 0;
        $filetype = wp_check_filetype(basename($filename), null);
        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        /* Insert the attachment. */
        $attach_id = wp_insert_attachment($attachment, $absolute_file, $parent_post_id);
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata($attach_id, $absolute_file);
        //$attach_data = wp_get_attachment_image( $attach_id );
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($parent_post_id, $attach_id);
        update_user_meta($user_id, '_sb_user_pic', $attach_id);

        $idata['profile_img'] = $movefile['url'];
        $response = array('success' => true, 'data' => $idata, 'message' => __("Profile image updated successfully", "adforest-rest-api"), "extraData" => $extraData);
        return $response;
    }

}
/* Edit Profile */

add_action('rest_api_init', 'adforestAPI_profile_api_ads_hooks_post', 0);

function adforestAPI_profile_api_ads_hooks_post() {

    register_rest_route(
            'adforest/v1', '/profile/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
        
        register_rest_route(
            'adforest/v1', '/verifyfb_user_number/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_verify_user_number_firebase',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_post')) {

    function adforestAPI_profile_post($request) {

        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        $json_data = $request->get_json_params();
        $name = (isset($json_data['user_name'])) ? trim($json_data['user_name']) : '';
        $location = (isset($json_data['location'])) ? trim($json_data['location']) : '';
        $phone = (isset($json_data['phone_number'])) ? trim($json_data['phone_number']) : '';
        $accountType = (isset($json_data['account_type'])) ? trim($json_data['account_type']) : '';
        $user_introduction = (isset($json_data['user_introduction'])) ? trim($json_data['user_introduction']) : '';

        if ($name == "") {
            $response = array('success' => false, 'data' => '', 'message' => __("Please enter name.", "adforest-rest-api"));
            return $response;
        }
        if ($location == "") {
            $response = array('success' => false, 'data' => '', 'message' => __("Please enter location.", "adforest-rest-api"));
            return $response;
        }
        if ($phone == "") {
            $response = array('success' => false, 'data' => '', 'message' => __("Please enter phone number.", "adforest-rest-api"));
            return $response;
        }
        if ($accountType == "") {
            $response = array('success' => false, 'data' => '', 'message' => __("Please select account type.", "adforest-rest-api"));
            return $response;
        }

        $saved_ph = get_user_meta($user_id, '_sb_contact', true);
        if ($saved_ph != $phone) {
            update_user_meta($user_id, '_sb_is_ph_verified', '0');
        }

        if ($phone != "") {
            update_user_meta($user_id, '_sb_contact', $phone);
        }

        if ($accountType != "")
            update_user_meta($user_id, '_sb_user_type', $accountType);

        if ($location != "")
            update_user_meta($user_id, '_sb_address', $location);
        if ($name != "") {
            $user_id = wp_update_user(array('ID' => $user_id, 'display_name' => $name));
        }

        //update user info here
        update_user_meta($user_id, '_sb_user_intro', $user_introduction);
        /* Social profile Starts */
        $social_profiles = adforestAPI_social_profiles();
        if (isset($social_profiles) && count($social_profiles) > 0) {
            foreach ($social_profiles as $key => $val) {
                $keyName = '';
                $keyName = "_sb_profile_" . $key;
                /* $keyVal  = get_user_meta( $user->ID, $keyName, true ); */
                $social = (isset($json_data['social_icons'][$keyName])) ? trim($json_data['social_icons'][$keyName]) : '';
                update_user_meta($user_id, $keyName, sanitize_textarea_field($social));
            }
        }
        
        
        /* Social Profile Ends */
        $data = adforestAPI_basic_profile_data();
        $page_title['page_title'] = __("Edit Profile", "adforest-rest-api");
        $response = array('success' => true, 'data' => $data, 'message' => __("Profile Updated.", "adforest-rest-api"), 'page_title' => $page_title);
        return $response;
    }

}

if(!function_exists('adforestAPI_verify_user_number_firebase')){    
    function adforestAPI_verify_user_number_firebase($request){

        $user_id    = get_current_user_id();
        $saved_ph   = get_user_meta($user_id, '_sb_contact', true);
        $json_data   = $request->get_json_params();
        $phone_number = (isset($json_data['phone_number'])) ? trim($json_data['phone_number']) : '';             
        if($saved_ph == "" ||  $saved_ph != $phone_number){               
            $response = array('success' => false, 'data' => "", 'message' => __("Phone number is incorrect", "adforest-rest-api"));
             return $response;
        }
        else{          
            update_user_meta($user_id, '_sb_is_ph_verified', '1');
            $response = array('success' => true, 'data' => "", 'message' => __("Succesfully updated", "adforest-rest-api"));
             return $response;
        }      
  }
}
/* Edit Profile Ends */
add_action('rest_api_init', 'adforestAPI_profile_reset_pass_hooks_post', 0);

function adforestAPI_profile_reset_pass_hooks_post() {

    register_rest_route(
            'adforest/v1', '/profile/reset_pass', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_reset_pass_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_reset_pass_post')) {

    function adforestAPI_profile_reset_pass_post($request) {

        if (ADFOREST_API_ALLOW_EDITING == false) {
            $response = array('success' => false, 'data' => '', 'message' => __("Editing Not Alloded In Demo", "adforest-rest-api"));
            return $response;
        }

        $json_data = $request->get_json_params();
        $old_pass = (isset($json_data['old_pass'])) ? trim($json_data['old_pass']) : '';
        $new_pass = (isset($json_data['new_pass'])) ? trim($json_data['new_pass']) : '';
        $new_pass_con = (isset($json_data['new_pass_con'])) ? trim($json_data['new_pass_con']) : '';

        if ($old_pass == "") {
            $response = array('success' => false, 'data' => '', 'message' => __("Please enter current password", "adforest-rest-api"));
            return $response;
        }
        if ($new_pass == "") {
            $response = array('success' => false, 'data' => '', 'message' => __("Please enter new password", "adforest-rest-api"));
            return $response;
        }

        if ($new_pass != $new_pass_con) {
            $response = array('success' => false, 'data' => '', 'message' => __("Password confirm password mismatched", "adforest-rest-api"));
            return $response;
        }

        $user = get_user_by('ID', get_current_user_id());
        if ($user && wp_check_password($old_pass, $user->data->user_pass, $user->ID)) {
            wp_set_password($new_pass, $user->ID);
            $response = array('success' => true, 'data' => '', 'message' => __("Password successfully chnaged", "adforest-rest-api"));
            return $response;
        } else {
            $response = array('success' => false, 'data' => '', 'message' => __("Invalid old password", "adforest-rest-api"));
            return $response;
        }

        die();
    }

}

add_action('rest_api_init', 'adforestAPI_profile_forgot_pass_hooks_post', 0);

function adforestAPI_profile_forgot_pass_hooks_post() {

    register_rest_route(
            'adforest/v1', '/profile/forgot_pass/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_forgot_pass_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_forgot_pass_post')) {

    function adforestAPI_profile_forgot_pass_post($request) {
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        $json_data = $request->get_json_params();
        $old_pass = (isset($json_data['old_pass'])) ? trim($json_data['old_pass']) : '';
    }

}

/* API custom endpoints for WP-REST API */
add_action('rest_api_init', 'adforestAPI_profile_api_hooks_get', 0);

function adforestAPI_profile_api_hooks_get() {
    register_rest_route(
            'adforest/v1', '/profile/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_myProfile_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_myProfile_get')) {

    function adforestAPI_myProfile_get() {

        global $adforestAPI;
        $user = wp_get_current_user();

        adforestAPI_check_ads_validity();

        $profile_arr['id'] = $user->ID;
        $profile_arr['user_email'] = array("key" => __("Email", "adforest-rest-api"), "value" => $user->user_email, "field_name" => "user_email");
        $profile_arr['display_name'] = array("key" => __("Name", "adforest-rest-api"), "value" => $user->display_name, "field_name" => "user_name");
        $profile_arr['phone'] = array("key" => __("Phone Number", "adforest-rest-api"), "value" => get_user_meta($user->ID, '_sb_contact', true), "field_name" => "phone_number");

        $user_intro = esc_attr(get_user_meta($user->ID, '_sb_user_intro', true));
        $profile_arr['introduction'] = array("key" => __("Introduction", "adforest-rest-api"), "value" => $user_intro, "field_name" => "user_introduction");

        $social_profiles = adforestAPI_social_profiles();
        $profile_arr['is_show_social'] = false;
        if (isset($social_profiles) && count($social_profiles) > 0) {
            $profile_arr['is_show_social'] = true;
            foreach ($social_profiles as $key => $val) {
                $keyName = '';
                $keyName = "_sb_profile_" . $key;
                $keyVal = get_user_meta($user->ID, $keyName, true);
                $keyVal = ( $keyVal ) ? $keyVal : '';
                $is_disable = 'false';
                if ($key == 'linkedin') {
                    $is_disable = isset($adforestAPI['sb_disable_linkedin_edit']) && $adforestAPI['sb_disable_linkedin_edit'] ? 'true' : 'false';
                }
                $profile_arr['social_icons'][] = array("key" => $val, "value" => $keyVal, "field_name" => $keyName, "disable" => $is_disable);
            }
        }

        $package_type = get_user_meta($user->ID, '_sb_pkg_type', true);
        $package_type = ( $package_type == 'free' || $package_type == "") ? __('Free', 'adforest-rest-api') : __('Paid', 'adforest-rest-api');
        $sb_user_type_val = get_user_meta($user->ID, '_sb_user_type', true);
        $sb_user_type_text = __('Individual', 'adforest-rest-api');
        if ($sb_user_type_val == 'Dealer') {
            $sb_user_type_text = __('Dealer', 'adforest-rest-api');
        }
        $profile_arr['package_type'] = array("key" => __("Package Type", "adforest-rest-api"), "value" => $package_type, "field_name" => "package_type");
        $profile_arr['account_type'] = array("key" => __("Account Type", "adforest-rest-api"), "value" => $sb_user_type_text, "field_name" => "account_type");
        $profile_arr['location'] = array("key" => __("Location", "adforest-rest-api"), "value" => get_user_meta($user->ID, '_sb_address', true), "field_name" => "location");

        $profile_arr['profile_img'] = array("key" => __("Image", "adforest-rest-api"), "value" => adforestAPI_user_dp($user->ID), "field_name" => "profile_img");

        $sb_expire_ads = get_user_meta($user->ID, '_sb_expire_ads', true);
        $expiery_date = ( $sb_expire_ads != '-1' ) ? $sb_expire_ads : __("Never", "adforest-rest-api");


        $profile_arr['expire_date'] = array("key" => __("Expire Date", "adforest-rest-api"), "value" => $expiery_date, "field_name" => "expire_date");

        $profile_arr['blocked_users_show'] = (isset($adforestAPI['sb_user_allow_block']) && $adforestAPI['sb_user_allow_block']) ? true : false;
        $profile_arr['blocked_users'] = array("key" => __("Blocked Users", "adforest-rest-api"), "value" => __("Click Here", "adforest-rest-api"), "field_name" => "blocked_users");

        $sb_simple_ads = get_user_meta($user->ID, '_sb_simple_ads', true);
        $sb_simple_ads = ( $sb_simple_ads != "" ) ? $sb_simple_ads : 0;
        $sb_simple_ads = ( $sb_simple_ads >= 0 ) ? $sb_simple_ads : __("Unlimited", "adforest-rest-api");
        $profile_arr['simple_ads'] = array("key" => __("No. Of Simple Ads", "adforest-rest-api"), "value" => $sb_simple_ads, "field_name" => "simple_ads");

        $sb_featured_ads = get_user_meta($user->ID, '_sb_featured_ads', true);
        $sb_featured_ads = ( $sb_featured_ads != "" ) ? $sb_featured_ads : 0;
        $sb_featured_ads = ( $sb_featured_ads >= 0 ) ? $sb_featured_ads : __("Unlimited", "adforest-rest-api");

        $profile_arr['featured_ads'] = array("key" => __("No. Of Featured Ads", "adforest-rest-api"), "value" => $sb_featured_ads, "field_name" => "featured_ads");

        $sb_bump_ads = get_user_meta($user->ID, '_sb_bump_ads', true);
        $sb_bump_ads = ( $sb_bump_ads != "" ) ? $sb_bump_ads : 0;
        $sb_bump_ads = ( $sb_bump_ads >= 0 ) ? $sb_bump_ads : __("Unlimited", "adforest-rest-api");

        $bump_ad_is_show = false;
        if (isset($adforestAPI['sb_allow_free_bump_up']) && $adforestAPI['sb_allow_free_bump_up'] == true) {
            $bump_ad_is_show = true;
        } else if (isset($adforestAPI['sb_allow_bump_ads']) && $adforestAPI['sb_allow_bump_ads'] == true) {
            $bump_ad_is_show = true;
        }
        $profile_arr['bump_ads_is_show'] = $bump_ad_is_show;
        $profile_arr['bump_ads'] = array("key" => __("No. Of Bump Ads", "adforest-rest-api"), "value" => $sb_bump_ads, "field_name" => "bump_ads");


        $profile_arr['profile_extra'] = adforestAPI_basic_profile_data();

        $sb_user_type = get_user_meta($user->ID, '_sb_user_type', true);

        $usr_indiviual = array("key" => "Indiviual", "value" => __("Individual", "adforest-rest-api"));
        $usr_dealer = array("key" => "Dealer", "value" => __("Dealer", "adforest-rest-api"));

        if ($sb_user_type == "Dealer") {
            $profile_arr['account_type_select'][] = $usr_dealer;
            $profile_arr['account_type_select'][] = $usr_indiviual;
        } else {
            $profile_arr['account_type_select'][] = $usr_indiviual;
            $profile_arr['account_type_select'][] = $usr_dealer;
        }


        $extra_arr['profile_title'] = __("My Profile", "adforest-rest-api");
        $extra_arr['profile_edit_title'] = __("Edit Profile", "adforest-rest-api");
        $extra_arr['save_btn'] = __("Update", "adforest-rest-api");
        $extra_arr['cancel_btn'] = __("Cancel", "adforest-rest-api");
        $extra_arr['select_image'] = __("Select Image", "adforest-rest-api");

        $extra_arr['change_pass']['title'] = __("Change Password?", "adforest-rest-api");
        $extra_arr['change_pass']['old_pass'] = __("Old Password", "adforest-rest-api");
        $extra_arr['change_pass']['new_pass'] = __("New Password", "adforest-rest-api");
        $extra_arr['change_pass']['new_pass_con'] = __("Confirm New Password", "adforest-rest-api");
        $extra_arr['change_pass']['err_pass'] = __("Password Not Matched", "adforest-rest-api");

        $extra_arr['select_pic']['title'] = __("Add Photo!", "adforest-rest-api");
        $extra_arr['select_pic']['camera'] = __("Take Photo", "adforest-rest-api");
        $extra_arr['select_pic']['library'] = __("Choose From Gallery", "adforest-rest-api");
        $extra_arr['select_pic']['cancel'] = __("Cancel", "adforest-rest-api");
        $extra_arr['select_pic']['no_camera'] = __("camera Not Available", "adforest-rest-api");
     
       $gateway    = $adforestAPI['sb_select_sms_gateway']  ?   $adforestAPI['sb_select_sms_gateway'] : "";
       
        $contact_number       = get_user_meta($user->ID ,  '_sb_contact' ,true); 
       
        $code_message        =  esc_html__('Verification code is sent to','adforest-rest-api').$contact_number;
        $extra_arr['code_sent'] = $code_message;
        $extra_arr['not_received'] = esc_html__('Didn,t receive a code?','adforest-rest-api');
        $extra_arr['try_again'] = esc_html__('Try again','adforest-rest-api');       
        $extra_arr['verify_number'] = esc_html__('Verify number','adforest-rest-api');
        $extra_arr['sms_gateway'] = $gateway;
        $extra_arr['verify_success'] = esc_html__('Verified successfully','adforest-rest-api');
       

        $profile_arr['page_title'] = __("My Profile", "adforest-rest-api");
        $profile_arr['page_title_edit'] = __("Edit Profile", "adforest-rest-api");
        $is_verification_on = false;
        if (isset($adforestAPI['sb_phone_verification']) && $adforestAPI['sb_phone_verification'] == true) {
            $is_verification_on = true;
            $number_verified = get_user_meta($user->ID, '_sb_is_ph_verified', '1');
            $number_verified_text = ( $number_verified && $number_verified == 1 ) ? __("verified", "adforest-rest-api") : __("Not verified", "adforest-rest-api");
            $extra_arr['is_number_verified'] = ( $number_verified && $number_verified == 1 ) ? true : false;
            $extra_arr['is_number_verified_text'] = $number_verified_text;

            $extra_arr['phone_dialog'] = array(
                "text_field" => __("Verify Your Code", "adforest-rest-api"),
                "btn_cancel" => __("Cancel", "adforest-rest-api"),
                "btn_confirm" => __("Confirm", "adforest-rest-api"),
                "btn_resend" => __("Resend", "adforest-rest-api"),
            );


            $extra_arr['send_sms_dialog'] = array(
                "title" => __("Confirmation", "adforest-rest-api"),
                "text" => __("Send SMS verification code.", "adforest-rest-api"),
                "btn_send" => __("Send", "adforest-rest-api"),
                "btn_cancel" => __("Cancel.", "adforest-rest-api"),
            );
        }
        $extra_arr['is_verification_on'] = $is_verification_on;

        $delete_profile = (isset($adforestAPI['sb_new_user_delete_option']) && $adforestAPI['sb_new_user_delete_option'] ) ? true : false;
        $profile_arr['can_delete_account'] = $delete_profile;
        if ($delete_profile) {
            $profile_arr['delete_account']['text'] = __("Delete Account?", "adforest-rest-api");

            $delete_profile_text = (isset($adforestAPI['sb_new_user_delete_option_text']) && $adforestAPI['sb_new_user_delete_option_text'] != "" ) ? $adforestAPI['sb_new_user_delete_option_text'] : __("Are you sure you want to delete the account.", "adforest-rest-api");

            $profile_arr['delete_account']['popuptext'] = $delete_profile_text;
            $profile_arr['delete_account']['btn_cancel'] = __("Cancel", "adforest-rest-api");
            $profile_arr['delete_account']['btn_submit'] = __("Confirm", "adforest-rest-api");
        }



        $response = array('success' => true, 'data' => $profile_arr, "message" => '', "extra_text" => $extra_arr);
        return $response;
    }

}

/* Public profile starts */
add_action('rest_api_init', 'adforestAPI_userPublicProfile_hooks_get', 0);

function adforestAPI_userPublicProfile_hooks_get() {
    register_rest_route(
            'adforest/v1', '/profile/public/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_userPublicProfile_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_userPublicProfile_get')) {

    function adforestAPI_userPublicProfile_get($request) {

        global $adforestAPI;
        $json_data = $request->get_json_params();
        $user_id = (isset($json_data['user_id']) ) ? $json_data['user_id'] : '';
        $user = get_userdata($user_id);
        if (!$user) {
            $response = array('success' => false, 'data' => '', "message" => __("User doest not exists", "adforest-rest-api"));
            return $response;
        }

        $profile_arr['id'] = $user->ID;
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';
        $adsData = adforestApi_userAds($user->ID, '', '', $paged);
        $profile_arr['ads'] = $adsData['ads'];
        $profile_arr['pagination'] = $adsData['pagination'];
        $profile_arr['text']['ad_type'] = 'myads';
        $profile_arr['text']['editable'] = '0';
        $profile_arr['text']['show_dropdown'] = '0';

        $message = (count($profile_arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $user_intro = ( get_user_meta($user->ID, '_sb_user_intro', true) );
        $profile_arr['introduction'] = array("key" => __("Introduction", "adforest-rest-api"), "value" => $user_intro, "field_name" => "user_introduction");

        $social_profiles = adforestAPI_social_profiles();
        $profile_arr['is_show_social'] = false;
        if (isset($social_profiles) && count($social_profiles) > 0) {
            $profile_arr['is_show_social'] = true;
            foreach ($social_profiles as $key => $val) {
                $keyName = '';
                $keyName = "_sb_profile_" . $key;
                $keyVal = get_user_meta($user->ID, $keyName, true);
                $keyVal = ( $keyVal ) ? $keyVal : '';

                $is_disable = 'false';
                if ($key == 'linkedin') {
                    $is_disable = isset($adforestAPI['sb_disable_linkedin_edit']) && $adforestAPI['sb_disable_linkedin_edit'] ? 'true' : 'false';
                }

                $profile_arr['social_icons'][] = array("key" => $val, "value" => $keyVal, "field_name" => $keyName, "disable" => $is_disable);
            }
        }
        $profile_arr['profile_extra'] = adforestAPI_basic_profile_data($user_id);
        $profile_arr['page_title'] = __("User Profile", "adforest-rest-api");

        $response = array('success' => true, 'data' => $profile_arr, "message" => $message);
        return $response;
    }

}

/* Public profile ends */

add_action('rest_api_init', 'adforestAPI_user_ads_get', 0);

function adforestAPI_user_ads_get() {

    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_all_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_all_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));

    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/active/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_active_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/active/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_active_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/expired/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_expired_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/expired/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_expired_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/sold/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_sold_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/sold/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_sold_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/featured/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_featured_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/featured/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_featured_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/inactive/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_inactive_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/inactive/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_inactive_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/rejected/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_rejected_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/rejected/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_rejected_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/most-visited/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_most_visited_ads_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/most-visited/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_most_visited_ads_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/expire-sold/', array('methods' => WP_REST_Server::READABLE, 'callback' => 'adforestAPI_ad_expire_sold_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/expire-sold/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_ad_expire_sold_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));

    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/update/status/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_change_user_ad_status',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));

    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/delete/', array(
        'methods' => WP_REST_Server::DELETABLE,
        'callback' => 'adforestAPI_change_user_ad_delete',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
        'args' => array('force' => array('default' => true,),),
    ));
    register_rest_route(
            'adforest/v1', '/ad/delete/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_change_user_ad_delete',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
        'args' => array('force' => array('default' => true,),),
    ));

    /* favourite */
    register_rest_route(
            'adforest/v1', '/ad/favourite/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_user_ad_favourite',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/ad/favourite/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_user_ad_favourite',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    /* favourite remove */
    register_rest_route(
            'adforest/v1', '/ad/favourite/remove', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_user_ad_favourite_remove',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
}

/* Active Ads */
if (!function_exists('adforestAPI_ad_all_get')) {

    function adforestAPI_ad_all_get($request) {

        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';

        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds($userID->ID, '', '', $paged);
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];

        $arr['page_title'] = __('My Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'myads';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '1';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}

/* inActive Ads */
if (!function_exists('adforestAPI_ad_inactive_get')) {

    function adforestAPI_ad_inactive_get($request) {


        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';
        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds($userID->ID, 'active', '', $paged, 'pending', 'profile_inactive');

        $arr['notification'] = __("Waiting for admin approval.", "adforest-rest-api");
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Inactive Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'inactive';
        $arr['text']['editable'] = '0';
        $arr['text']['show_dropdown'] = '0';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}

/* rejected Ads */
if (!function_exists('adforestAPI_ad_rejected_get')) {

    function adforestAPI_ad_rejected_get($request) {
        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';
        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds($userID->ID, '', '', $paged, 'rejected', 'profile_rejected');

        $arr['notification'] = __("Waiting for admin approval.", "adforest-rest-api");
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Rejected Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'rejected';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '0';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}

/* inActive Ads */
if (!function_exists('adforestAPI_most_visited_ads_get')) {

    function adforestAPI_most_visited_ads_get($request) {

        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';
        //$userID = wp_get_current_user();
        $adsData = adforestApi_userAds('', '', '', $paged, 'publish', 'visited');

        //$arr['notification'] = __("Waiting for admin approval.", "adforest-rest-api");
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Most Visited Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'most_visited';
        $arr['text']['editable'] = '0';
        $arr['text']['show_dropdown'] = '0';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}






/* inActive Ads */
if (!function_exists('adforestAPI_ad_expire_sold_get')) {

    function adforestAPI_ad_expire_sold_get($request) {
        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';
        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds($userID->ID, '', '', $paged, 'draft', 'expire_sold');

        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Expire/Sold Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'draft';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '1';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}





/* Active Ads */
if (!function_exists('adforestAPI_ad_active_get')) {

    function adforestAPI_ad_active_get($request) {

        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';

        $userID = wp_get_current_user();
        $arr['page_title'] = __('Active Ads', 'adforest-rest-api');
        $adsData = adforestApi_userAds($userID->ID, 'active', '', $paged);
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'active';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '1';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}
/* expired Ads */
if (!function_exists('adforestAPI_ad_expired_get')) {

    function adforestAPI_ad_expired_get($request) {

        $arr['page_title'] = __('Expired Ads', 'adforest-rest-api');
        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';
        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds($userID->ID, 'expired', '', $paged);
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Expired Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'expired';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '1';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);

        return $response;
    }

}
/* sold Ads */
if (!function_exists('adforestAPI_ad_sold_get')) {

    function adforestAPI_ad_sold_get($request) {
        $arr['page_title'] = __('Sold Ads', 'adforest-rest-api');
        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';
        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds($userID->ID, 'sold', '', $paged);
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Sold Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'sold';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '1';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}
/* featured Ads */
if (!function_exists('adforestAPI_ad_featured_get')) {

    function adforestAPI_ad_featured_get($request) {
        $arr['page_title'] = __('Featured Ads', 'adforest-rest-api');
        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';

        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds($userID->ID, 'active', '1', $paged, 'publish', 'profile_featured');
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Featured Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'featured';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '0';

        $arr['profile'] = adforestAPI_basic_profile_data();

        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}

/* favourite Ads - Remove to favourites */
if (!function_exists('adforestAPI_user_ad_favourite_remove')) {

    function adforestAPI_user_ad_favourite_remove($request) {

        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id']) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        if (delete_user_meta($user_id, '_sb_fav_id_' . $ad_id)) {
            return array('success' => true, 'data' => '', 'message' => __("Ad removed successfully.", "adforest-rest-api"));
        } else {
            return array('success' => false, 'data' => '', 'message' => __("There'is some problem, please try again later.", "adforest-rest-api"));
        }
    }

}

/* favourite Ads */
if (!function_exists('adforestAPI_user_ad_favourite')) {

    function adforestAPI_user_ad_favourite($request) {
        $arr['page_title'] = __('Favourite Ads', 'adforest-rest-api');
        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';

        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds_fav($userID->ID, '', '', $paged, 'publish', 'profile_fav');
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['page_title'] = __('Favourite Ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'favourite';
        $arr['text']['editable'] = '0';
        $arr['text']['show_dropdown'] = '0';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}

if (!function_exists('adforestAPI_user_ad_strings')) {

    function adforestAPI_user_ad_strings() {
        $status_dropdown_value = array("active", "expired", "sold",);
        $status_dropdown_name = array(
            __("Active", "adforest-rest-api"),
            __("Expired", "adforest-rest-api"),
            __("Sold", "adforest-rest-api"),
        );

        $string["status_dropdown_value"] = $status_dropdown_value;
        $string["status_dropdown_name"] = $status_dropdown_name;
        $string["edit_text"] = __("Edit", "adforest-rest-api");
        $string["delete_text"] = __("Delete", "adforest-rest-api");
        return $string;
    }

}

if (!function_exists('adforestAPI_change_user_ad_status')) {

    function adforestAPI_change_user_ad_status($request) {
        $userID = wp_get_current_user();
        if (empty($userID)) {
            $message = __("Invalid Access", "adforest-rest-api");
            return $response = array('success' => true, 'data' => '', 'message' => $message);
        }
        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';
        $ad_status = (isset($json_data['ad_status'])) ? $json_data['ad_status'] : '';
        $post_tmp = get_post($ad_id);

        $sb_status_array = array(
            'expired' => 'draft',
            'sold' => 'draft',
            'active' => 'publish',
        );


        if (isset($post_tmp) && $post_tmp != "") {
            $author_id = $post_tmp->post_author;
            if (isset($userID) && $author_id == $userID->ID && $ad_id != "" && $ad_status != "") {

                $my_post = array(
                    'ID' => $ad_id,
                    'post_status' => $sb_status_array[$ad_status],
                );

                wp_update_post($my_post);

                update_post_meta($ad_id, "_adforest_ad_status_", $ad_status);
                $message = __("Ad Status Updated", "adforest-rest-api");
            } else {
                $message = __("Some error occured.", "adforest-rest-api");
            }
        } else {
            $message = __("Invalid Post Id", "adforest-rest-api");
        }
        $response = array('success' => true, 'data' => '', 'message' => $message);
        return $response;
    }

}

/* Delete ad */
if (!function_exists('adforestAPI_change_user_ad_delete')) {

    function adforestAPI_change_user_ad_delete($request) {
        $userID = wp_get_current_user();
        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';
        $post_data = get_post($ad_id);
        if (empty($userID)) {
            $message = __("Invalid Access", "adforest-rest-api");
            return $response = array('success' => false, 'data' => '', 'message' => $message);
        }

        // $status = get_post_status($ad_id);
        // if (get_post_status($ad_id) != "publish") {
        //     $message = __("You can't delete this ad.", "adforest-rest-api");
        //     return $response = array('success' => false, 'data' => $request, 'message' => $message);
        // }

        if (isset($post_data) && $post_data != "") {
            $author_id = $post_data->post_author;

            if ($author_id == $userID->ID && $post_data->ID != "") {
                $query = array('ID' => $post_data->ID, 'post_status' => 'trash',);
                wp_update_post($query, true);
                $message = __("Ad Deleted Successfully", "adforest-rest-api");
            } else {
                $message = __("Some error occured.", "adforest-rest-api");
            }
        } else {
            $message = __("Invalid Post Id", "adforest-rest-api");
        }
        $response = array('success' => true, 'data' => $query, 'message' => $message);
        return $response;
    }

}

/* Add To favs */
if (!function_exists('adforestAPI_ad_add_to_fav')) {

    function adforestAPI_ad_add_to_fav($request) {
        $userID = wp_get_current_user();
        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';
        $post_data = get_post($ad_id);
        if (empty($userID) || $userID == "") {
            $message = __("Invalid Access", "adforest-rest-api");
            return $response = array('success' => false, 'data' => '', 'message' => $message);
        }

        if (isset($post_data) && $post_data != "") {
            $author_id = $post_data->post_author;

            if ($author_id == $userID->ID && $post_data->ID != "") {
                $query = array('ID' => $post_data->ID, 'post_status' => 'trash',);

                $message = __("Added To Favourites", "adforest-rest-api");
            } else {
                $message = __("Already Added To Favourites", "adforest-rest-api");
            }
        } else {
            $message = __("Invalid Post Id", "adforest-rest-api");
        }
        $response = array('success' => true, 'data' => '', 'message' => $message);
        return $response;
    }

}
/* Add To favs ends */

if (!function_exists('adforestAPI_basic_profile_data')) {

    function adforestAPI_basic_profile_data($user_id = '', $poster_name = '') {

        global $adforestAPI;

        if ($user_id == "") {
            $user = wp_get_current_user();
            $user_id = $user->ID;
        } else {
            $user = get_userdata($user_id);
        }

        if (!$user_id)
            return '';

        $profile_arr['id'] = $user_id;
        $profile_arr['user_email'] = $user->user_email;
        if ($poster_name != "") {
            $profile_arr['display_name'] = $poster_name;
        } else {
            $profile_arr['display_name'] = $user->display_name;
        }
        $contact_number     =  get_user_meta($user_id, '_sb_contact', true);
        $profile_arr['phone'] = $contact_number;
        $profile_arr['profile_img'] = adforestAPI_user_dp($user_id, 'adforest-user-profile');
        /* all active ads */

        $ads_total_text = __("Active", "adforest-rest-api");
        $profile_arr['ads_total'] = adforestAPI_countPostsHere('publish', '_adforest_ad_status_', 'active', $user_id) . ' ' . $ads_total_text;
        $ads_inactive_text = __("Inactive", "adforest-rest-api");
        $profile_arr['ads_inactive'] = adforestAPI_countPostsHere('pending', '', '', $user_id) . ' ' . $ads_inactive_text;
        $ads_sold_text = __("Sold", "adforest-rest-api");
        $profile_arr['ads_sold'] = adforestAPI_countPostsHere('draft', '_adforest_ad_status_', 'sold', $user_id) . ' ' . $ads_sold_text;
        $ads_expired_text = __("Expired", "adforest-rest-api");
        $profile_arr['ads_expired'] = adforestAPI_countPostsHere('draft', '_adforest_ad_status_', 'expired', $user_id) . ' ' . $ads_expired_text;

        $ads_featured_text = __("Featured Ads", "adforest-rest-api");
        $profile_arr['ads_featured'] = adforestAPI_countPostsHere('publish', '_adforest_is_feature', '1', $user_id) . ' ' . $ads_featured_text;
        $profile_arr['expire_ads'] = get_user_meta($user_id, '_sb_expire_ads', true);
        $profile_arr['simple_ads'] = get_user_meta($user_id, '_sb_simple_ads', true);
        $profile_arr['featured_ads'] = get_user_meta($user_id, '_sb_featured_ads', true);
        $profile_arr['package_type'] = get_user_meta($user_id, '_sb_pkg_type', true);
        $profile_arr['last_login'] = adforestAPI_getLastLogin($user_id, true);
        $profile_arr['edit_text'] = __("Edit Profile", "adforest-rest-api");

        $badge_text = esc_attr(get_the_author_meta('_sb_badge_text', $user_id));

        if (isset($adforestAPI['sb_new_user_email_verification']) && $adforestAPI['sb_new_user_email_verification']) {
            $token = get_user_meta($user_id, 'sb_email_verification_token', true);
            if ($token && $token != "") {
                $badge_text = __('Not Verified', "adforest-rest-api");
            }
        }

        $badge_text = ( $badge_text ) ? $badge_text : __('Verified', "adforest-rest-api");

        $badge_color = '#8ac249';
        $sb_badge_type = esc_attr(get_the_author_meta('_sb_badge_type', $user_id));
        if ($sb_badge_type == 'label-success')
            $badge_color = '#8ac249';
        else if ($sb_badge_type == 'label-warning')
            $badge_color = '#fe9700';
        else if ($sb_badge_type == 'label-info')
            $badge_color = '#02a8f3';
        else if ($sb_badge_type == 'label-danger')
            $badge_color = '#f34235';

        //$badge_text = 
        $profile_arr['verify_buton']['text'] = $badge_text;
        $profile_arr['verify_buton']['color'] = $badge_color;
        $profile_arr['rate_bar']['number'] = adforestAPI_user_ratting_info($user_id, 'stars');
        $profile_arr['rate_bar']['text'] = adforestAPI_user_ratting_info($user_id, 'count');

        $sb_userType = get_user_meta($user->ID, '_sb_user_type', true);
        $sb_userType = ($sb_userType) ? $sb_userType : __('Individual', "adforest-rest-api");
        $profile_arr['userType_buton']['text'] = $sb_userType;
        $profile_arr['userType_buton']['color'] = '#8ac249';

        $social_profiles = adforestAPI_social_profiles();
        $profile_arr['is_show_social'] = false;
        
        
    
        if (isset($social_profiles) && count($social_profiles) > 0) {
            $profile_arr['is_show_social'] = true;
            foreach ($social_profiles as $key => $val) {
                $is_disable = 'false';
                if ($key == 'linkedin') {
                    $is_disable = isset($adforestAPI['sb_disable_linkedin_edit']) && $adforestAPI['sb_disable_linkedin_edit'] ? 'true' : 'false';
                }
                $keyName = '';
                $keyName = "_sb_profile_" . $key;
                $keyVal = get_user_meta($user_id, $keyName, true);
                $keyVal = ( $keyVal ) ? $keyVal : '';
                $profile_arr['social_icons'][] = array("key" => $val, "value" => $keyVal, "field_name" => $keyName, "disable" => $is_disable);
            }
        }
        return $profile_arr;
    }

}








if (!function_exists('adforestAPI_user_ratting_info')) {

    function adforestAPI_user_ratting_info($user_id = '', $type = 'stars') {
        $stars = get_user_meta($user_id, "_adforest_rating_avg", true);
        $info["stars"] = ( $stars == "" ) ? "0" : $stars;
        $starsCount = get_user_meta($user_id, "_adforest_rating_count", true);
        $info["count"] = ( $starsCount != "" ) ? $starsCount : "0";
        return $info["$type"];
    }

}

if (!function_exists('adforestAPI_countPostsHere')) {

    function adforestAPI_countPostsHere($status = 'publish', $meta_key = '', $meta_val = '', $postAuthor = '') {
        if ($meta_key != "") {
            $args = array("author" => $postAuthor, 'post_type' => 'ad_post', 'post_status' => $status, 'meta_key' => $meta_key, 'meta_value' => $meta_val);
            $args = apply_filters('adforestAPI_site_location_args', $args, 'ads');
            $query = new WP_Query($args);
        } else {

            $args = array("author" => $postAuthor, 'post_type' => 'ad_post', 'post_status' => $status);
            $args = apply_filters('adforestAPI_site_location_args', $args, 'ads');
            $query = new WP_Query($args);
        }

        wp_reset_postdata();
        return $query->found_posts;
    }

}

add_action('rest_api_init', 'adforestAPI_user_public_profile_hook', 0);

function adforestAPI_user_public_profile_hook() {

    /* Routs */
    register_rest_route(
            'adforest/v1', '/profile/public/', array('methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_user_public_profile',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/profile/public/', array('methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_user_public_profile',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
}

if (!function_exists('adforestAPI_user_public_profile')) {

    function adforestAPI_user_public_profile($request) {

        $json_data = $request->get_json_params();
        $user_id = (isset($json_data['user_id'])) ? $json_data['user_id'] : '';

        if ($user_id == "") {
            $user = wp_get_current_user();
            $user_id = $user->data->ID;
        }

        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';

        $adsData = adforestApi_userAds($userID->ID, '', '', $paged);
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'myads';
        $arr['text']['editable'] = 0;
        $arr['text']['show_dropdown'] = 0;
        $arr['profile'] = adforestAPI_basic_profile_data($user_id);
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}

add_action('rest_api_init', 'adforestAPI_user_ratting_hook', 0);

function adforestAPI_user_ratting_hook() {
    /* Routs */
    register_rest_route(
            'adforest/v1', '/profile/ratting/', array('methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_user_ratting_list',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
    register_rest_route(
            'adforest/v1', '/profile/ratting_get/', array('methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_user_ratting_list',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
}

if (!function_exists('adforestAPI_user_ratting_list')) {

    function adforestAPI_user_ratting_list($request = '') {
        $json_data = $request->get_json_params();
        $author_id = (isset($json_data['author_id'])) ? $json_data['author_id'] : '';
        if ($author_id == "") {
            $author = wp_get_current_user();
            $author_id = $author->data->ID;
        }
        return adforestAPI_user_ratting_list1($author_id);
    }

}

if (!function_exists('adforestAPI_user_ratting_list1')) {

    function adforestAPI_user_ratting_list1($author_id = '', $return_arr = true) {
        $rating_user = wp_get_current_user();
        $rating_user_id = $rating_user->data->ID;
        $ratings = adforestAPI_get_all_ratings($author_id);
        $rateArr = array();
        $rate = array();
        $message = '';
        $rdata = array();
        if (count($ratings) > 0) {
            foreach ($ratings as $rating) {
                $data = explode('_separator_', $rating->meta_value);
                $rated = trim((int) $data[0]);
                $comments = trim($data[1]);
                $date = $data[2];
                $reply = ( isset($data[3]) ) ? $data[3] : '';
                $reply_date = ( isset($data[4]) ) ? $data[4] : '';
                $_arr = explode('_user_', $rating->meta_key);
                $rator = $_arr[1];

                $user = get_user_by('ID', $rator);
                if ($user) {
                    $img = adforestAPI_user_dp($user->ID);
                    $can_reply = ( $reply == "" && $rating_user_id == $author_id ) ? true : false;
                    $has_reply = ( $reply == "" ) ? false : true;

                    $rate['reply_id'] = $rator;
                    $rate['name'] = $user->display_name;
                    $rate['img'] = $img;
                    $rate['stars'] = (int) ( $rated != "" ) ? $rated : 0;
                    $rate['date'] = date(get_option('date_format'), strtotime($date));
                    $rate["can_reply"] = $can_reply;
                    $rate["has_reply"] = $has_reply;
                    $rate["reply_txt"] = __('Reply', 'adforest-rest-api');
                    $rate["comments"] = $comments;

                    $rate2 = array();
                    if ($reply != "") {
                        $userR = get_user_by('ID', $author_id);
                        $img2 = adforestAPI_user_dp($author_id);
                        $rate2['name'] = $userR->display_name;
                        $rate2['img'] = $img2;
                        $rate2['stars'] = 0;
                        $rate2['date'] = date(get_option('date_format'), strtotime($reply_date));
                        $rate2["can_reply"] = false;
                        $rate2["has_reply"] = true;
                        $rate2["reply_txt"] = __('Reply', 'adforest-rest-api');
                        $rate2["comments"] = trim($reply);
                    }
                    $rate["reply"] = $rate2;
                    $rateArr[] = $rate;
                }

                if (count($rateArr) == 0) {
                    $message = ( $author_id != $rating_user_id ) ? __('Be the first one to rate this user.', 'adforest-rest-api') : __('Currently no rating available..', 'adforest-rest-api');
                }
            }
        } else {
            $message = ( $author_id != $rating_user_id ) ? __('Be the first one to rate this user.', 'adforest-rest-api') : __('Currently no rating available..', 'adforest-rest-api');
        }
        $can_rate = ($author_id == $rating_user_id) ? false : true;
        /* User Ratting Form Info */
        $rdata['page_title'] = __('User Rating', 'adforest-rest-api');
        $rdata['rattings'] = $rateArr;
        $rdata['can_rate'] = $can_rate;
        $rdata['form']['title'] = __('Rate Here', 'adforest-rest-api');
        $rdata['form']['select_text'] = __('Rating', 'adforest-rest-api');
        $rdata['form']['select_value'] = array(1, 2, 3, 4, 5);
        $rdata['form']['textarea_text'] = __('Comments', 'adforest-rest-api');
        $rdata['form']['textarea_value'] = '';
        $rdata['form']['tagline'] = __('You can not edit it later.', 'adforest-rest-api');
        $rdata['form']['btn'] = __('Submit Your Rating', 'adforest-rest-api');

        if ($return_arr == true) {
            $response = array('success' => true, 'data' => $rdata, 'message' => $message, "ratings " => $ratings);
            return $response;
        } else {
            return $rateArr;
        }
    }

}

add_action('rest_api_init', 'adforestAPI_post_ratting_hook', 0);

function adforestAPI_post_ratting_hook() {

    register_rest_route(
            'adforest/v1', '/profile/ratting/', array('methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_post_user_ratting',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
}

if (!function_exists('adforestAPI_post_user_ratting')) {

    function adforestAPI_post_user_ratting($request) {
        $json_data = $request->get_json_params();
        $ratting = (isset($json_data['ratting'])) ? (int) $json_data['ratting'] : '';
        $comments = (isset($json_data['comments'])) ? trim($json_data['comments']) : '';
        $author = (isset($json_data['author_id'])) ? (int) $json_data['author_id'] : '';
        $is_reply = (isset($json_data['is_reply']) && $json_data['is_reply'] == true) ? true : false;
        $authorData = wp_get_current_user();
        $rator = $authorData->data->ID;
        $cUser = $authorData->data->ID;

        if ($author == $rator)
            return array('success' => false, 'data' => '', 'message' => __("You can't rate yourself.", "adforest-rest-api"));

        if ($is_reply == true) {
            $rdata = array();
            $rator = $author;
            $got_ratting = $rator;

            $ratting = get_user_meta($cUser, "_user_" . $rator, true);
            $data_arr = explode('_separator_', $ratting);
            if (count($data_arr) > 3) {
                return array('success' => false, 'data' => '', 'message' => __("You already replied to this user.", "adforest-rest-api"));
            } else {
                $ratting = $ratting . "_separator_" . $comments . "_separator_" . date('Y-m-d');
                update_user_meta($cUser, '_user_' . $rator, $ratting);
                $rdata['rattings'] = adforestAPI_user_ratting_list1($cUser, false);
                return array('success' => true, 'data' => $rdata, 'message' => __("You're reply has been posted.", "adforest-rest-api"));
            }
        } else {
            if (get_user_meta($author, "_user_" . $rator, true) == "") {
                $rdata = array();
                update_user_meta($author, "_user_" . $rator, $ratting . "_separator_" . $comments . "_separator_" . date('Y-m-d'));
                $ratings = adforestAPI_get_all_ratings($author);
                $all_rattings = 0;
                $got = 0;
                if (count($ratings) > 0) {
                    foreach ($ratings as $rating) {
                        $data = explode('_separator_', $rating->meta_value);
                        $got = $got + $data[0];
                        $all_rattings++;
                    }
                    $avg = $got / $all_rattings;
                } else {
                    $avg = $ratting;
                }

                update_user_meta($author, "_adforest_rating_avg", $avg);
                $total = get_user_meta($author, "_adforest_rating_count", true);
                if ($total == "") {
                    $total = 0;
                }
                $total = $total + 1;
                update_user_meta($author, "_adforest_rating_count", $total);

                // Send email if enabled
                global $adforestAPI;
                if (isset($adforestAPI['email_to_user_on_rating']) && $adforestAPI['email_to_user_on_rating']) {
                    adforest_send_email_new_rating($rator, $author, $ratting, $comments);
                }
                $rdata['rattings'] = adforestAPI_user_ratting_list1($author, false);
                return array('success' => true, 'data' => $rdata, 'message' => __("You've rated this user.", "adforest-rest-api"));
            } else {
                return array('success' => false, 'data' => '', 'message' => __("You already rated this user.", "adforest-rest-api"));
            }
        }
    }

}

/* API custom endpoints for WP-REST API */
add_action('rest_api_init', 'adforestAPI_profile_nearby', 0);

function adforestAPI_profile_nearby() {
    register_rest_route(
            'adforest/v1', '/profile/nearby/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_profile_nearby_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route(
            'adforest/v1', '/profile/nearby/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_nearby_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_nearby_get')) {

    function adforestAPI_profile_nearby_get($request) {
        $data = array();
        $user_id = get_current_user_id();
        $success = false;
        if ($user_id != "") {

            if (isset($request)) {
                $json_data = $request->get_json_params();
                $latitude = (isset($json_data['nearby_latitude'])) ? $json_data['nearby_latitude'] : '';
                $longitude = (isset($json_data['nearby_longitude'])) ? $json_data['nearby_longitude'] : '';
                $distance = (isset($json_data['nearby_distance'])) ? $json_data['nearby_distance'] : '20';
                if ($latitude != "" && $longitude != "") {
                    $data_array = array("latitude" => $latitude, "longitude" => $longitude, "distance" => $distance);
                    update_user_meta($user_id, '_sb_user_nearby_data', $data_array);
                    $success = true;
                } else {
                    update_user_meta($user_id, '_sb_user_nearby_data', '');
                    $success = false;
                }
            }

            $data = adforestAPI_determine_minMax_latLong();
        }

        $message = ( $success ) ? __("Nearby option turned on", "adforest-rest-api") : __("Nearby option turned off", "adforest-rest-api");

        return array('success' => $success, 'data' => $data, 'message' => $message);
    }

}
/* NearByAdsStarts */
add_action('rest_api_init', 'adforestAPI_nearby_ads_hook', 0);

function adforestAPI_nearby_ads_hook() {
    /* Routs */
    register_rest_route(
            'adforest/v1', '/ad/nearby/', array('methods' => WP_REST_Server::EDITABLE, 'callback' => 'adforestAPI_nearby_ads_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
    ));
}

/* Active Ads */
if (!function_exists('adforestAPI_nearby_ads_get')) {

    function adforestAPI_nearby_ads_get($request) {
        $json_data = $request->get_json_params();
        $paged = (isset($json_data['page_number']) ) ? $json_data['page_number'] : '1';

        $userID = wp_get_current_user();
        $adsData = adforestApi_userAds('', '', '', $paged, 'publish', 'near_me');
        $arr['ads'] = $adsData['ads'];
        $arr['pagination'] = $adsData['pagination'];

        $arr['page_title'] = __('Near By ads', 'adforest-rest-api');
        $arr['text'] = adforestAPI_user_ad_strings();
        $arr['text']['ad_type'] = 'nearby';
        $arr['text']['editable'] = '1';
        $arr['text']['show_dropdown'] = '1';
        $arr['profile'] = adforestAPI_basic_profile_data();
        $message = (count($arr['ads']) == 0 ) ? __("No ad found", "adforest-rest-api") : "";
        $response = array('success' => true, 'data' => $arr, 'message' => $message);
        return $response;
    }

}
/* NearByAdsENds */

add_action('rest_api_init', 'adforestAPI_profile_package_details_hook', 0);

function adforestAPI_profile_package_details_hook() {

    register_rest_route(
            'adforest/v1', '/profile/purchases/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_profile_package_details',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_package_details')) {

    function adforestAPI_profile_package_details() {

        $user_id = get_current_user_id();
        $args = array('customer_id' => $user_id,);
        $order_hostory = array();
        $order_hostory[] = array(
            "order_number" => __('Order #', 'adforest-rest-api'),
            "order_name" => __('Package(s)', 'adforest-rest-api'),
            "order_status" => __('Status', 'adforest-rest-api'),
            "order_date" => __('Date', 'adforest-rest-api'),
            "order_total" => __('Order total', 'adforest-rest-api'),
        );

        $orders = wc_get_orders($args);
        $message = '';
        if (count($orders) > 0) {
            foreach ($orders as $order) {

                $order_id = $order->get_id();
                $items = $order->get_items();
                $product_name = array();

                foreach ($items as $item) {
                    $product_name[] = $item->get_name();
                }
                $product_names = implode(",", $product_name);
                $order_hostory[] = array(
                    "order_number" => $order_id,
                    "order_name" => $product_names,
                    "order_status" => wc_get_order_status_name($order->get_status()),
                    "order_date" => date_i18n(get_option('date_format'), strtotime($order->get_date_created())),
                    "order_total" => $order->get_total(),
                );
            }
        } else {
            $message = __('No Order Found', 'adforest-rest-api');
        }
        $data['page_title'] = __('Packages History', 'adforest-rest-api');
        $data['order_hostory'] = $order_hostory;
        return array('success' => true, 'data' => $data, 'message' => $message);
    }

}
/* -----
  Ad rating And Comments Starts
  ----- */
add_action('rest_api_init', 'adforestAPI_profile_gdpr_delete_user_hook', 0);

function adforestAPI_profile_gdpr_delete_user_hook() {

    register_rest_route('adforest/v1', '/profile/delete/user_account/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_gdpr_delete_user',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_gdpr_delete_user')) {

    function adforestAPI_profile_gdpr_delete_user($request) {
        global $adforestAPI; /* For Redux */
        $json_data = $request->get_json_params();
        $user_id = (isset($json_data['user_id']) && $json_data['user_id'] != "" ) ? $json_data['user_id'] : '';
        $current_user = get_current_user_id();
        $success = false;
        $message = __("Something went wrong.", "adforest-rest-api");
        $if_user_exists = adforestAPI_user_id_exists($user_id);
        if ($current_user == $user_id && $if_user_exists) {
            if (current_user_can('administrator')) {
                $success = false;
                $message = __("Admin can not delete his account from here.", "adforest-rest-api");
            } else {
                adforestAPI_delete_userComments($user_id);
                $user_delete = wp_delete_user($user_id);
                if ($user_delete) {
                    $success = true;
                    $message = __("You account has been delete successfully.", "adforest-rest-api");
                }
            }
        }
        return array('success' => $success, 'data' => '', 'message' => $message);
    }

}
if (!function_exists('adforestAPI_user_id_exists')) {

    function adforestAPI_user_id_exists($user) {
        global $wpdb;
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user));
        if ($count == 1) {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('adforestAPI_delete_userComments')) {

    function adforestAPI_delete_userComments($user_id) {
        $user = get_user_by('id', $user_id);
        $comments = get_comments('author_email=' . $user->user_email);
        if ($comments && count($comments) > 0) {
            foreach ($comments as $comment) :
                @wp_delete_comment($comment->comment_ID, true);
            endforeach;
        }

        $comments = get_comments('user_id=' . $user_id);
        if ($comments && count($comments) > 0) {
            foreach ($comments as $comment) :
                @wp_delete_comment($comment->comment_ID, true);
            endforeach;
        }
    }

}