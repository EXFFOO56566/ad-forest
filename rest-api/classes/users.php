<?php
/* Adds all user meta to the /wp-json/wp/v2/user/[id] endpoint */
function sb_user_meta_update($data, $field_name, $request) {
    if ($data['id']) {
        $user_meta = get_user_meta($data['id']);
    }
    $data = array("email", "profile");
    return $field_name;
}

function sb_user_meta1($data, $field_name, $request) {

    $profile_arr = array();
    if ($data['id']) {

        $userID = $data['id'];
        $profile_arr = array();
        $profile_arr['phone'] = get_user_meta($userID, '_sb_contact', true);
        $profile_arr['profile_img'] = adforestAPI_user_dp($userID);
        $profile_arr['expire_ads'] = get_user_meta($userID, '_sb_expire_ads', true);
        $profile_arr['simple_ads'] = get_user_meta($userID, '_sb_simple_ads', true);
        $profile_arr['featured_ads'] = get_user_meta($userID, '_sb_featured_ads', true);
        $profile_arr['package_type'] = get_user_meta($userID, '_sb_pkg_type', true);
        $profile_arr['last_login'] = adforestAPI_getLastLogin($userID);
        $profile_arr['active'] = adforestApi_userAds($userID, 'active', '', -1);
        $profile_arr['expired'] = adforestApi_userAds($userID, 'expired', '', -1);
        $profile_arr['sold'] = adforestApi_userAds($userID, 'sold', '', -1);
        $profile_arr['featured'] = adforestApi_userAds($userID, 'active', 1, -1);
    }

    return $profile_arr;
}

add_action('rest_api_init', 'adforestAPI_sb_user_meta_update_hook');

function adforestAPI_sb_user_meta_update_hook() {
    register_rest_field('user', 'meta', array(
        'get_callback' => 'sb_user_meta1',
        'update_callback' => 'sb_user_meta_update',
        'schema' => null,
            )
    );
}

add_action('rest_api_init', 'adforestAPI_sb_user_email_hook');

function adforestAPI_sb_user_email_hook() {
    register_rest_field('user', 'info', array(
        'get_callback' => 'sb_user_email',
        'update_callback' => null,
        'schema' => null,
            )
    );
}

function sb_user_email($data, $field_name, $request) {
    return isset($data['email']) && $data['email'] != '' ? $data['email'] : '';
}

/* add_filter( 'rest_prepare_user', 'get_all_users', 10, 3 ); */

function get_all_users($data, $field_name, $request) {

    $user_info = wp_get_current_user();
    $userID = $user_info->ID;
    /* User Profile */
    $profile['profile']['userID'] = $userID;
    $profile['profile']['user_login'] = $user_info->user_login;
    $profile['profile']['user_email'] = $user_info->user_email;
    $profile['profile']['display_name'] = $user_info->display_name;
    $profile['profile']['user_nicename'] = $user_info->user_nicename;
    $profile['profile']['user_registered'] = $user_info->user_registered;
    /* User Meta */
    $profile['meta']['phone'] = get_user_meta($userID, '_sb_contact', true);
    $profile['meta']['profile_img'] = adforestAPI_user_dp($userID);
    $profile['meta']['expire_ads'] = get_user_meta($userID, '_sb_expire_ads', true);
    $profile['meta']['simple_ads'] = get_user_meta($userID, '_sb_simple_ads', true);
    $profile['meta']['featured_ads'] = get_user_meta($userID, '_sb_featured_ads', true);
    $profile['meta']['package_type'] = get_user_meta($userID, '_sb_pkg_type', true);
    $profile['meta']['last_login'] = adforestAPI_getLastLogin($userID);
    $response = array('success' => true, 'data' => $profile);
    return $response;
}

add_action('rest_api_init', 'adforestAPI_profile_block_user_hook', 0);

function adforestAPI_profile_block_user_hook() {
    register_rest_route(
            'adforest/v1', '/user/block/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_profile_block_user_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route(
            'adforest/v1', '/user/block/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_block_user_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route(
            'adforest/v1', '/user/unblock/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_profile_unblock_user_post',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_profile_block_user_get')) {

    function adforestAPI_profile_block_user_get() {
        $get_current_user_id = get_current_user_id();
        $blocked = get_user_meta($get_current_user_id, '_sb_adforest_block_users', true);
        $users = array();
        if (isset($blocked) && $blocked != "" && count((array) $blocked) > 0) {
            $count_row = 0;
            $blocked = array_reverse($blocked);
            foreach ((array) $blocked as $block) {
                $userdata = get_user_by('ID', $block);
                if ($userdata) {
                    $users[$count_row]['id'] = $block;
                    $users[$count_row]['name'] = $userdata->display_name;
                    $users[$count_row]['image'] = adforestAPI_user_dp($block);
                    $users[$count_row]['text'] = __("Unblock", "adforest-rest-api");
                    $users[$count_row]['location'] = get_user_meta($block, '_sb_address', true);
                    $count_row++;
                }
            }
        }
        $data = array();
        $data['page_title'] = __("Blocked Users List", "adforest-rest-api");
        ;
        $data['users'] = $users;
        $message = (count($users) > 0 ) ? '' : __("No user in block list", "adforest-rest-api");
        return array('success' => true, 'data' => $data, 'message' => $message);
    }

}

if (!function_exists('adforestAPI_profile_block_user_post')) {

    function adforestAPI_profile_block_user_post($request) {
        $get_current_user_id = get_current_user_id();
        $json_data = $request->get_json_params();
        $user_id = (isset($json_data['user_id']) && $json_data['user_id'] != "" ) ? $json_data['user_id'] : '';
        /* if( true ) */
        if ($get_current_user_id != $user_id) {
            $blocked_list = get_user_meta($get_current_user_id, '_sb_adforest_block_users', true);
            if (isset($blocked_list) && $blocked_list != "") {
                if (!in_array($user_id, $blocked_list)) {
                    array_push($blocked_list, $user_id);
                }
            } else {
                $blocked_list = array($user_id);
            }
            $blocked = update_user_meta($get_current_user_id, '_sb_adforest_block_users', $blocked_list);
            if ($blocked) {
                $success = true;
                $message = __("User blocked successfully. You can unblock this user from profile.", "adforest-rest-api");
            } else {
                $success = false;
                $message = __("Something went wrong or user already in blok list.", "adforest-rest-api");
            }
        } else {
            $success = false;
            $message = __("You can not block yourself.", "adforest-rest-api");
        }
        return array('success' => $success, 'data' => '', 'message' => $message);
    }

}

if (!function_exists('adforestAPI_profile_unblock_user_post')) {

    function adforestAPI_profile_unblock_user_post($request) {
        $get_current_user_id = get_current_user_id();
        $json_data = $request->get_json_params();
        $user_id = (isset($json_data['user_id']) && $json_data['user_id'] != "" ) ? $json_data['user_id'] : '';
        $blocked_list = get_user_meta($get_current_user_id, '_sb_adforest_block_users', true);
        if (isset($blocked_list) && $blocked_list != "") {
            if (in_array($user_id, $blocked_list)) {
                foreach ($blocked_list as $key => $list) {
                    if ($user_id == $list) {
                        unset($blocked_list[$key]);
                        break;
                    }
                }
            }
        }

        $update_saved = $blocked = update_user_meta($get_current_user_id, '_sb_adforest_block_users', $blocked_list);
        if ($blocked) {
            $success = true;
            $message = __("User unblocked successfully.", "adforest-rest-api");
        } else {
            $success = false;
            $message = __("Something went wrong or user already unbloked.", "adforest-rest-api");
        }
        return array('success' => $success, 'data' => '', 'message' => $message);
    }

}

add_action('rest_api_init', 'adforestAPI_sellers_list_hook', 0);

function adforestAPI_sellers_list_hook() {
    register_rest_route(
            'adforest/v1', '/sellers/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_sellers_list',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route(
            'adforest/v1', '/sellers/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_sellers_list',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_sellers_list')) {

    function adforestAPI_sellers_list($request) {
        global $adforestAPI;
        $json_data = $request->get_json_params();
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $result = count_users();
        $total_users = isset($result['total_users']) ? $result['total_users'] : 15;
        $page = $paged;
        $users_per_page = 10;
        $total_pages = 1;
        $offset = ($paged - 1) * $users_per_page;
        $total_pages = ceil($total_users / $users_per_page);
        $args = array('orderby' => 'display_name', 'number' => $users_per_page, 'offset' => $offset);
        // Create the WP_User_Query object
        $user_query = new WP_User_Query($args);
        $message = '';
        $author_list = array();
        $authors = $user_query->get_results();
        if (!empty($authors)) {
            foreach ($authors as $author) {
                // get all the user's data
                $author_info = get_userdata($author->ID);
                $user_id = $author->ID;
                /* Get Social Icons for a user */
                $social_profiles = adforestAPI_social_profiles();
                $profile_arr = array();
                $profile_arr['is_show_social'] = false;
                if (isset($social_profiles) && count($social_profiles) > 0) {
                    $profile_arr['is_show_social'] = true;
                    foreach ($social_profiles as $key => $val) {
                        $keyName = '';

                        $is_disable = 'false';
                        if ($key == 'linkedin') {
                            $is_disable = isset($adforestAPI['sb_disable_linkedin_edit']) && $adforestAPI['sb_disable_linkedin_edit'] ? 'true' : 'false';
                        }


                        $keyName = "_sb_profile_" . $key;
                        $keyVal = get_user_meta($user_id, $keyName, true);
                        $keyVal = ( $keyVal ) ? $keyVal : '';
                        $profile_arr['social_icons'][] = array("key" => $val, "value" => $keyVal, "field_name" => $keyName, "disable" => $is_disable);
                    }
                }

                $address = get_user_meta($author->ID, '_sb_address', true);
                if (!$address) {
                    $address = __("N/A", "adforest-rest-api");
                }
                $author_list[] = array(
                    "author_id" => $author->ID,
                    "author_name" => $author_info->display_name,
                    "author_img" => adforestAPI_user_dp($author->ID),
                    "author_rating" => adforestAPI_user_ratting_info($author->ID),
                    "author_social" => $profile_arr,
                    "author_address" => $address,
                );
            }
        } else {
            $message = __("No authors found", "adforest-rest-api");
        }
        $data['page_title'] = __("Sellers", "adforest-rest-api");
        $data['authors'] = $author_list;
        $max_num_pages = $total_pages;
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $users_per_page, "current_no_of_ads" => (int) $total_users, "has_next_page" => $has_next_page);
        $data['load_more'] = __("Load More", "adforest-rest-api");
        return array('success' => true, 'data' => $data, 'message' => $message);
    }

}