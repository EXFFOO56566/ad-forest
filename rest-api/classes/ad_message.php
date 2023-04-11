<?php
/* -----
  Ad Messages Inbox
  ----- */
add_action('rest_api_init', 'adforestAPI_messages_inbox_api_hooks_get', 0);
function adforestAPI_messages_inbox_api_hooks_get() {

    register_rest_route('adforest/v1', '/message/inbox/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_messages_inbox_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route('adforest/v1', '/message/inbox/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_inbox_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_has_ads_messages')) {

    function adforestAPI_has_ads_messages($where) {
        $where .= ' AND comment_count > 0 ';
        return $where;
    }

}

if (!function_exists('adforestAPI_messages_inbox_get')) {

    function adforestAPI_messages_inbox_get($request) {

        $verifed_phone_number = adforestAPI_check_if_phoneVerified();
        if ($verifed_phone_number) {
            $message2 = __("Please verify your phone number to send message.", "adforest-rest-api");
            return array('success' => false, 'data' => '', 'message' => $message2);
        }
        $json_data = $request->get_json_params();
        $receiver_id = (isset($json_data['receiver_id']) && $json_data['receiver_id'] != "" ) ? $json_data['receiver_id'] : '';



        //print_r($blocked_user_array);

        global $adforestAPI; /* For Redux */
        global $wpdb;
        $user = wp_get_current_user();
        $user_id = @$user->data->ID;
        /* Offers on my ads starts */

        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $posts_per_page = get_option('posts_per_page');
        $args = array(
            'post_type' => 'ad_post',
            'author' => $user_id,
            'post_status' => 'publish',
            'posts_per_page' => get_option('posts_per_page'),
            'paged' => $paged,
            'order' => 'DESC',
            'orderby' => 'date',
            'adforestAPI_post_has_comments' => array(
                'comment_type' => 'ad_post',
                'comment_status' => '1'
            )
        );

        add_filter('posts_where', 'adforestAPI_has_ads_messages');
        $args = apply_filters('adforestAPI_site_location_args', $args, 'ads');
        $ads = new WP_Query($args);
        $myOfferAds = array();
        if ($ads->have_posts()) {
            while ($ads->have_posts()) {
                $ads->the_post();
                $ad_id = get_the_ID();
                $args = array('number' => '1', 'post_id' => $ad_id, 'post_type' => 'ad_post');
                $comments = get_comments($args);

                //if(count($comments) > 0 ){
                $offerAds['ad_id'] = $ad_id;
                $offerAds['message_ad_title'] = esc_html(adforestAPI_convert_uniText(get_the_title($ad_id)));
                $offerAds['message_ad_img'] = adforestAPI_get_ad_image($ad_id, 1, 'thumb');
                $is_unread_msgs = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id = '" . get_current_user_id() . "' AND meta_value = '0' AND meta_key like '" . $ad_id . "_%' ");
                $offerAds['message_read_status'] = ( $is_unread_msgs > 0 ) ? false : true;

                // print_r(get_post_meta($ad_id));

                /*
                 * received offers blocked
                 */
                $poster_id = get_post_field('post_author', $ad_id);
                $blocked_user_array = get_user_meta($user_id, 'adforest_blocked_users', true);
                $block_status = "false";
                if (in_array($poster_id, $blocked_user_array)) {
                    $block_status = "true";
                }
                $offerAds['is_block'] = $block_status;


                $myOfferAds[] = $offerAds;
                //}
            }
        }
        // Don't filter future queries.
        remove_filter('posts_where', 'adforestAPI_has_ads_messages');
        $data['received_offers']['items'] = $myOfferAds;
        /* Offers on my ads ends */
        $data['title']['main'] = __("Messages", "adforest-rest-api");
        $data['title']['sent'] = __("Sent Offers", "adforest-rest-api");
        $data['title']['receive'] = __("Offers on Ads", "adforest-rest-api");
        $data['title']['blocked'] = __("Blocked Users", "adforest-rest-api");

        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $ads->max_num_pages ) ? true : false;

        $data['pagination'] = array("max_num_pages" => (int) $ads->max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) count($ads->posts), "has_next_page" => $has_next_page);

        return $response = array('success' => true, 'data' => $data, 'message' => '');
    }

}
/* -----
  Ad Messages Main
  ----- */
add_action('rest_api_init', 'adforestAPI_messages_api_hooks_get', 0);

function adforestAPI_messages_api_hooks_get() {
    register_rest_route('adforest/v1', '/message/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_messages_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route('adforest/v1', '/message_post/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_messages_get')) {

    function adforestAPI_messages_get($request) {


        $json_data = $request->get_json_params();
        $receiver_id = (isset($json_data['receiver_id']) && $json_data['receiver_id'] != "" ) ? $json_data['receiver_id'] : '';
        $user = wp_get_current_user();
        $user_id = @$user->data->ID;





        global $adforestAPI; /* For Redux */
        global $wpdb;

        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $posts_per_page = get_option('posts_per_page');
        $start = ($paged - 1) * $posts_per_page;

        $rows = $wpdb->get_results("SELECT comment_ID FROM $wpdb->comments WHERE comment_type = 'ad_post' AND user_id = '$user_id' AND comment_parent = '$user_id' GROUP BY comment_post_ID ORDER BY comment_ID DESC");

        $total_posts = $wpdb->num_rows;
        $max_num_pages = ceil($total_posts / $posts_per_page);
        $max_num_pages = ( $max_num_pages < 1 ) ? 1 : $max_num_pages;

        $rows = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_type = 'ad_post' AND user_id = '$user_id' AND comment_parent = '$user_id' GROUP BY comment_post_ID ORDER BY comment_ID DESC LIMIT $start, $posts_per_page");

        $message = array();
        $sentMessageData = array();
        foreach ($rows as $row) {
            $ad_id = $row->comment_post_ID;
            $message_receiver_id = get_post_field('post_author', $row->comment_post_ID);
            $comment_author = @get_userdata($message_receiver_id);
            $msg_status = get_comment_meta($user_id, $ad_id . "_" . $message_receiver_id, true);
            $msg_status_r = ( (int) $msg_status == 0 && $msg_status != "") ? false : true;
            $message['ad_id'] = $ad_id;
            $message['message_author_name'] = @$comment_author->display_name;
            $message['message_ad_img'] = adforestAPI_get_ad_image($ad_id, 1, 'thumb');
            $message['message_ad_title'] = esc_html(adforestAPI_convert_uniText(get_the_title($ad_id)));
            $message['message_read_status'] = $msg_status_r;

            $message['message_sender_id'] = $user_id;
            $message['message_sender_img'] = adforestAPI_user_dp($user_id);
            $message['message_receiver_id'] = $message_receiver_id;
            $message['message_receiver_img'] = adforestAPI_user_dp($message_receiver_id);
            $message['message_date'] = $row->comment_date;
            /*
             * check user blocked ads sent offer
             */
            $blocked_user_array = get_user_meta($user_id, 'adforest_blocked_users', true);
            $block_status = "false";
            if (is_array($blocked_user_array)  && in_array($message_receiver_id, $blocked_user_array)) {
                $block_status = "true";
            }
            $message['is_block'] = $block_status;
            /*
             * check user blocked ads
             */

            $sentMessageData[] = $message;
        }
        $data['sent_offers']['items'] = $sentMessageData;
        /* Messgae sent offer ends */
        $data['title']['main'] = __("Messages", "adforest-rest-api");
        $data['title']['sent'] = __("Sent Offers", "adforest-rest-api");
        $data['title']['receive'] = __("Offers on Ads", "adforest-rest-api");
        $data['title']['blocked'] = __("Blocked Users", "adforest-rest-api");
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) ($total_posts), "has_next_page" => $has_next_page);

        return $response = array('success' => true, 'data' => $data, 'message' => $message2);
    }
}
/* -----
  Ad Messages Get offers on ads
  ----- */
add_action('rest_api_init', 'adforestAPI_messages_offers_api_hooks_get', 0);

function adforestAPI_messages_offers_api_hooks_get() {
    register_rest_route('adforest/v1', '/message/offers/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_messages_offers_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route('adforest/v1', '/message/offers/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_offers_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_messages_offers_get')) {

    function adforestAPI_messages_offers_get($request) {
        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id']) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        global $wpdb;
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            // This will occur if on front page.
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }
        $posts_per_page = get_option('posts_per_page');
        $start = ($paged - 1) * $posts_per_page;

        $rows = $wpdb->get_results("SELECT comment_author, user_id, comment_date FROM $wpdb->comments WHERE comment_post_ID = '$ad_id'  GROUP BY user_id ORDER BY MAX(comment_date) DESC");

        $total_posts = $wpdb->num_rows;
        $max_num_pages = ceil($total_posts / $posts_per_page);

        $rows = $wpdb->get_results("SELECT comment_author, user_id, comment_date FROM $wpdb->comments WHERE comment_post_ID = '$ad_id'  GROUP BY user_id ORDER BY MAX(comment_date) DESC LIMIT $start, $posts_per_page");

        $message = array();
        $myOfferAds = array();
        $success = false;
        if (count($rows) > 0) {
            $success = true;
            foreach ($rows as $r) {
                if ($user_id == $r->user_id)
                    continue;
                $msg_status = get_comment_meta(get_current_user_id(), $ad_id . "_" . $r->user_id, true);
                $message['ad_id'] = $ad_id;
                $message['message_author_name'] = $r->comment_author;
                $message['message_ad_img'] = adforestAPI_user_dp($r->user_id);
                $message['message_ad_title'] = esc_html(adforestAPI_convert_uniText(get_the_title($ad_id)));
                $message['message_read_status'] = ( $msg_status == 0 || $msg_status == '0' ) ? false : true;
                $message['message_sender_id'] = $r->user_id;
                $message['message_sender_img'] = adforestAPI_user_dp($r->user_id);
                $message['message_receiver_id'] = $user_id;
                $message['message_receiver_img'] = adforestAPI_user_dp($user_id);
                $message['message_date'] = $r->comment_date;
                $myOfferAds[] = $message;
            }
        }
        $data['received_offers']['items'] = $myOfferAds;
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) count((array) $total_posts), "has_next_page" => $has_next_page);
        $extra['page_title'] = esc_html(get_the_title($ad_id));
        $message = ( $success == false ) ? __("No Message Found", "adforest-rest-api") : '';
        return $response = array('success' => $success, 'data' => $data, 'message' => $message, "extra" => $extra);
    }

}

/*
 * Adforest Message User Block
 */

add_action('rest_api_init', 'adforestAPI_messages_userblock_hooks_get', 0);

function adforestAPI_messages_userblock_hooks_get() {

    register_rest_route('adforest/v1', '/message/chat/userblocklist', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_chat_get_block_user_list',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route('adforest/v1', '/message/chat/userblocklist', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_messages_chat_get_block_user_list',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );


    register_rest_route('adforest/v1', '/message/chat/userblock', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_userblock_chat_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route('adforest/v1', '/message/chat/userunblock', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_userunblock_chat_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

function adforestAPI_messages_chat_get_block_user_list() {

    $get_current_user_id = get_current_user_id();
    $blocked_user_array = get_user_meta($get_current_user_id, 'adforest_blocked_users', true);

    if (isset($blocked_user_array) && is_array($blocked_user_array) && sizeof($blocked_user_array) > 0) {
        $data = array();
        foreach ($blocked_user_array as $block_time => $single_user) {
            if (!empty($single_user)) {
                $author_obj = @get_user_by('id', $single_user);
                $user_display_name = ($author_obj) ? $author_obj->display_name : __("Demo", "adforest-rest-api");
                $data[] = array(
                    'user_id' => $single_user,
                    'user_name' => $user_display_name,
                    'user_img' => adforestAPI_user_dp($single_user),
                    'sender_id' => $get_current_user_id,
                    'receiver_id' => $single_user,
                    'block_text' => __('Unblock', 'adforest-rest-api'),
                    'block_time' => __('Blocked on', 'adforest') . ' : ' . date('F j, Y', $block_time),
                );
                $success = true;
            }
        }
        return $response = array('success' => $success, 'data' => $data);
    } else {
        $success = true;
        $data = array();
        $message = __('There are no blocked users.', 'adforest-rest-api');
        return $response = array('success' => $success, 'data' => $data, 'message' => $message);
    }
}

function adforestAPI_messages_userblock_chat_get($request) {

    $json_data = $request->get_json_params();
    $block_from = (isset($json_data['sender_id']) && $json_data['sender_id'] != "" ) ? $json_data['sender_id'] : '';
    $block_to = (isset($json_data['recv_id']) && $json_data['recv_id'] != "" ) ? $json_data['recv_id'] : '';

    $message = __('Oops ! Something went wrong.', 'adforest-rest-api');
    $success = false;


    if ($block_to == 0 || $block_from == 0) {
        $message = __('Oops ! Something went wrong.', 'adforest-rest-api');
        $success = false;
    }
    if ($block_to == $block_from) {
        $message = __('Sorry You cannot Block yourself.', 'adforest-rest-api');
        $success = false;
    }
    $blocked_user_array = get_user_meta($block_from, 'adforest_blocked_users', true);
    $blocked_user_array = isset($blocked_user_array) && !empty($blocked_user_array) ? $blocked_user_array : array();
    if (function_exists('adforest_set_date_timezone')) {
        adforest_set_date_timezone();
    }
    if (isset($blocked_user_array) && is_array($blocked_user_array)) {
        if (!in_array($block_to, $blocked_user_array)) {
            //$time = current_time('mysql');
            $time = current_time('mysql', 1);
            $blocked_user_array[strtotime($time)] = $block_to;
            update_user_meta($block_from, 'adforest_blocked_users', $blocked_user_array);
            $message = __('User Blocked Successfully.', 'adforest-rest-api');
            $success = true;
        } else {
            $message = __('You have already block this user.', 'adforest-rest-api');
            $success = false;
        }
    }

    return $response = array('success' => $success, 'message' => $message, 'btn_text' => __('Unblock User', 'adforest-rest-api'));
}

function adforestAPI_messages_userunblock_chat_get($request) {

    $json_data = $request->get_json_params();
    $block_from = (isset($json_data['sender_id']) && $json_data['sender_id'] != "" ) ? $json_data['sender_id'] : '';
    $block_to = (isset($json_data['recv_id']) && $json_data['recv_id'] != "" ) ? $json_data['recv_id'] : '';

    $message = __('Oops ! Something went wrong.', 'adforest-rest-api');
    $success = false;


    if ($block_to == 0 || $block_from == 0) {
        $message = __('Oops ! Something went wrong.', 'adforest-rest-api');
        $success = false;
    }
    if ($block_to == $block_from) {
        $message = __('Sorry You cannot Block yourself.', 'adforest-rest-api');
        $success = false;
    }

    $blocked_user_array = get_user_meta($block_from, 'adforest_blocked_users', true);
    $blocked_user_array = isset($blocked_user_array) && !empty($blocked_user_array) ? $blocked_user_array : array();
    if (isset($blocked_user_array) && is_array($blocked_user_array)) {
        if (in_array($block_to, $blocked_user_array)) {
            if (($key = array_search($block_to, $blocked_user_array)) !== false) {
                unset($blocked_user_array[$key]);
            }
        }
        update_user_meta($block_from, 'adforest_blocked_users', $blocked_user_array);
        $message = __('User unBlocked Successfully.', 'adforest-rest-api');
        $success = true;
    }

    return $response = array('success' => $success, 'message' => $message, 'btn_text' => __('Block User', 'adforest-rest-api'));
}

/*
 * Adforest Message end User Block
 */




/* -----
  Ad Messages Users Chat
  ----- */
add_action('rest_api_init', 'adforestAPI_messages_chat_api_hooks_get', 0);

function adforestAPI_messages_chat_api_hooks_get() {
    register_rest_route('adforest/v1', '/message/chat/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_messages_chat_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route('adforest/v1', '/message/chat/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_chat_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route('adforest/v1', '/message/chat/post/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_chat_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
        
        
        
        register_rest_route('adforest/v1', '/message/chat_media/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_chat_post_media',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
        
        
}

if (!function_exists('adforestAPI_messages_chat_get')) {

    function adforestAPI_messages_chat_get($request) {
        global $adforestAPI;
        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id']) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
        $sender_id = (isset($json_data['sender_id']) && $json_data['sender_id'] != "" ) ? (int) $json_data['sender_id'] : '';
        $receiver_id = (isset($json_data['receiver_id']) && $json_data['receiver_id'] != "" ) ? (int) $json_data['receiver_id'] : '';
        $type = (isset($json_data['type']) && $json_data['type'] != "" ) ? $json_data['type'] : 'sent';
        $message = (isset($json_data['message']) && $json_data['message'] != "" ) ? $json_data['message'] : '';
        
        $message_sent_android = (isset($json_data['sendMessage']) && $json_data['sendMessage'] != "" ) ? $json_data['sendMessage'] : false;
        
        $message_by_user = $message;
        $data['btn_text'] = __("block User", "adforest-rest-api");

        
        $id_to_update   =   $receiver_id;
        
        if(get_current_user_id() == $receiver_id){
            
            $id_to_update   =  $sender_id;
        }                          
       update_comment_meta(get_current_user_id(), $ad_id . "_" . $id_to_update, 1);
/*        $data['is_block'] = FALSE;

        if (isset($blocked_user_array) && !empty($blocked_user_array) && is_array($blocked_user_array) && in_array($sender_id, $blocked_user_array)) {
            $success = false;

            $message = __("You can't send message to this user.", "adforest-rest-api");

            $data['btn_text'] = ($type == 'sent') ? __("Unblock User", "adforest-rest-api")  :  __("block User", "adforest-rest-api");
            $data['is_block'] = ($type == 'sent') ?  false  : true;
            return $response = array('success' => $success, 'message' => $message, 'data' => $data);
        }

        $blocked_user_array1 = get_user_meta($sender_id, 'adforest_blocked_users', true);
        if (isset($blocked_user_array1) && !empty($blocked_user_array1) && is_array($blocked_user_array1) && in_array($receiver_id, $blocked_user_array1)) {
            $success = false;
            $message = __("Unblock this user to send message.", "adforest-rest-api");
            $data['btn_text'] = ($type == 'sent') ? __("block User", "adforest-rest-api") :__("Unblock User", "adforest-rest-api");
            $data['is_block'] =  ($type == 'sent') ?  true  : false;;
            return $response = array('success' => $success, 'message' => $message, 'data' => $data);
        }

*/

/*        $ad_author = get_post_field('post_author', $ad_id);

        if($ad_author == $sender_id)
        {


        }
*/
        $current_user = get_current_user_id();

        if($type == 'sent'){

            if( $current_user != $sender_id )
            {
                $bloker_user_id2 = $sender_id;
            }
            else
            {
                $bloker_user_id2 = $receiver_id;
            }

        }
        else
        {
            if( $current_user != $sender_id )
                {
                    $bloker_user_id2 = $sender_id;
                }
                else
                {
                    $bloker_user_id2 = $receiver_id;
                }

        }

        //$ad_author = get_post_field('post_author', $ad_id);
        //$bloker_user_id = ($type == 'sent') ?  $sender_id : $receiver_id;
       // $bloker_user_id2 = ($type == 'sent') ?  $receiver_id : $sender_id;
        $data['is_block'] = FALSE;
        $blocked_user_array1 = get_user_meta($current_user, 'adforest_blocked_users', true);
        //print_r($blocked_user_array1 );


        if (isset($blocked_user_array1) && !empty($blocked_user_array1) && is_array($blocked_user_array1) && in_array($bloker_user_id2, $blocked_user_array1)) {
            $success = false;
            $message = __("Unblock this user to send message.", "adforest-rest-api") . ' - '. $patanae;
            $data['btn_text'] = __("Unblock User", "adforest-rest-api");
            $data['is_block'] = TRUE;
            return $response = array('success' => $success, 'message' => $message, 'data' => $data);
        }


/*        if( $current_user != $sender_id )
        {
            $bloker_user_id2 = $sender_id;
        }
        else
        {
            $bloker_user_id2 = $receiver_id;
        }*/

     // echo $sender_id . ' | '. $current_user .  ' | ' . $bloker_user_id2;

        $blocked_user_array2 = get_user_meta($bloker_user_id2, 'adforest_blocked_users', true);

        //print_r($blocked_user_array2);

        if (isset($blocked_user_array2) && !empty($blocked_user_array2) && is_array($blocked_user_array2) && in_array($current_user, $blocked_user_array2)) {
            $success = false;
            $message = __("You can't send message to this user.", "adforest-rest-api");
            $data['btn_text'] = __("block User", "adforest-rest-api");
            $data['is_block'] = FALSE;
            return $response = array('success' => $success, 'message' => $message, 'data' => $data);
        }

        /*$patanae = ($type == 'receive') ?  $receiver_id  : $sender_id;

        //$patanae_user = ($type == 'sent') ?  $receiver_id  : $sender_id;
        
        //$patanae = ($type == 'sent') ?  $receiver_id  : $sender_id;


        $blocked_user_array = get_user_meta($sender_id, 'adforest_blocked_users', true);

        $while_block = false;

        $where  = 1;
        if (isset($blocked_user_array) && !empty($blocked_user_array) && is_array($blocked_user_array) && in_array($patanae, $blocked_user_array)) {

                if($patanae == $sender_id)
                {
                    $while_block = true;
                     $where  = 2;
                }
                else
                {
                    $while_block = true;
                    $where  = 3;                        
                }
        }
        else
        {
            $while_block = false;
             $where  = 4;
               /* if($patanae == $receiver_id)
                {
                     $where  = 4;
                    $while_block = true;
                }   
                else
                {
                     $where  = 5;
                    $while_block = false;
                }  * /       
        }


        $data['is_block'] = FALSE;
       
        if ($while_block) {
            $success = false;
            $message = __("You can't send message to this user.", "adforest-rest-api") . ' | '. $where;
            $data['btn_text'] = __("block User", "adforest-rest-api");
            $data['is_block'] = FALSE;
            return $response = array('success' => $success, 'message' => $message, 'data' => $data);
        }
*/
        $user = wp_get_current_user();
        $user_id = (int) $user->data->ID;
        $authors = array($sender_id, $user_id);
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }
        /* get_option( 'posts_per_page' ); */
        $posts_per_page = 10;
        if($type == 'sent'){
            $queryID2 =  ( $current_user == $sender_id ) ? $sender_id : $receiver_id;
        }
        else{
            $queryID2 = ( $current_user != $sender_id ) ? $sender_id : $receiver_id;
        }
       // echo "$current_user | $type | $sender_id | $receiver_id | $queryID2";
        //return '';

        $message2 = '';
        $verifed_phone_number = adforestAPI_check_if_phoneVerified();
        if ($ad_id != "" && $sender_id != "" && $receiver_id != "" && $message != "") {
            if (function_exists('adforestAPI_add_messages_get')) {
                if ($verifed_phone_number == false) {
                    //echo "$ad_id, $queryID, $receiver_id, $sender_id, $type, $message";
                    $message2 = adforestAPI_add_messages_get($ad_id, $queryID2, $sender_id, $receiver_id, $type, $message);
                }
            }
        }
        if ($type == 'sent') {              
            $authors = array($receiver_id, $user_id);
            //$queryID = ( $current_user == $sender_id ) ? $sender_id : $receiver_id;
            if($message == "")
            {
               // echo 111;
               // $authors = array($receiver_id, $user_id);
                $queryID = ( $current_user == $sender_id ) ? $sender_id : $receiver_id;
            }
            else{
                //echo 2222;
                //$authors = array($sender_id, $user_id);
                
                $queryID = ( $current_user != $receiver_id ) ? $sender_id : $receiver_id;
            }

            //$authors = array($queryID, $user_id);
            $authors = array($receiver_id, $user_id);


        } else {

            if($message == "")
            {
                $queryID = ( $current_user != $sender_id ) ? $sender_id : $receiver_id;
            }
            else{
                $queryID = ( $current_user == $receiver_id ) ? $sender_id : $receiver_id;
            }

           // $authors = array($sender_id, $user_id);
            $authors = array($queryID, $user_id);
        }

        //print_r($authors);

        $cArgs = array('author__in' => $authors, 'post_id' => $ad_id, 'parent' => $queryID, 'orderby' => 'comment_date', 'order' => 'DESC',);

/*  if( $type == "sent" )
        {
            if($queryID == $receiver_id)
            {
                $get_other_user_name = $sender_id;
            }
            else
            {
                $get_other_user_name = $receiver_id;
            }            
        }
        else{
            if($queryID != $receiver_id)
            {
                $get_other_user_name = $receiver_id;
            }
            else
            {
                $get_other_user_name = $sender_id;
            }  
        }

*/



      // print_r($cArgs);
       // echo  " | ".$queryID;
        $commentsData = get_comments($cArgs);
        $total_posts = count($commentsData);
        $max_num_pages = ceil($total_posts / $posts_per_page);
        $args = array(
            'author__in' => $authors,
            'post_id' => $ad_id,
            'parent' => $queryID,
            'orderby' => 'comment_date',
            'order' => 'DESC',
            'paged' => $paged,
         //   'offset' => $start,
            'number' => $posts_per_page,
        );

        $comments = get_comments($args);
        $chat = array();
        $chatHistory = array();
        $success = false;
        $get_other_user_name = ( $type != 'sent' ) ? $receiver_id : $sender_id;
        $author_obj = @get_user_by('id', $get_other_user_name);
        $page_title = ($author_obj) ? $author_obj->display_name : __("Chat Box", "adforest-rest-api");
        $data['page_title'] = $page_title;
        $data['ad_title'] = adforestAPI_convert_uniText(get_the_title($ad_id));
        $data['ad_img'] = adforestAPI_get_ad_image($ad_id, 1, 'thumb');
        $data['ad_date'] = get_the_date("", $ad_id);
        $sender_img = adforestAPI_user_dp($sender_id);
        $receiver_img = adforestAPI_user_dp($receiver_id);
        $data['ad_price'] = adforestAPI_get_price('', $ad_id);
        /* Add Read Status Here Starts */

       // update_comment_meta(get_current_user_id(), $ad_id . "_" . $get_other_user_name, 1);
        
        /* Add Read Status Here Ends */
        if (count($comments) > 0) {
            $success = true;
            foreach ($comments as $comment) {
                if ($type == 'sent') {
                    $messageType = ( $comment->comment_parent != $comment->user_id ) ? 'reply' : 'message';
                } else {
                    $messageType = ( $comment->comment_parent != $comment->user_id ) ? 'message' : 'reply';
                }

                $chat['img'] = ( $comment->comment_parent != $comment->user_id ) ? $receiver_img : $sender_img;
                $chat['id'] = $comment->comment_ID;
                $chat['ad_id'] = $comment->comment_post_ID;
                $chat['text'] = $comment->comment_content;
                $chat['date'] = adforestAPI_timeago($comment->comment_date);
                $chat['type'] = $messageType;
                
                 $chat_images   =  array();  
                 $chat_files    =  array();
                 $file_meta     =     get_comment_meta($comment->comment_ID,'comment_file_meta',true);                   
                 $file_meta      =      $file_meta != ""    ? unserialize($file_meta)    :  array();                            
                 if(!empty($file_meta)){    
                       
                        $count   =   0;                       
                        foreach ($file_meta as $attach_id){                                                
                                  $file_url         =    wp_get_attachment_url($attach_id); 
                                  if($file_url != ""){                                    
                                      if(wp_attachment_is_image($attach_id)){                                    
                                          $chat_images[]   =  $file_url;
                                      }
                                      else{
                                         $chat_files[]   =  $file_url;                                        
                                      }                                                                            
                                  }
                                  $count++;
                                }    
                            }
                $is_image    =   false;  
                $is_file     =   false;              
                if(!empty($chat_images)){
                     $is_image    =   true;  
                }
                if(!empty($chat_files)){
                     $is_file    =   true;  
                }
                $chat['is_file']    =  $is_file;             
                $chat['is_image']    =  $is_image;               
                $chat['images']   = $chat_images;
                $chat['files']   = $chat_files;
                $chatHistory[] = $chat;
            }
        }
        $data['chat'] = $chatHistory;
        $message_setting     =    array();
        
        $allow_media    =    isset($adforestAPI['allow_media_upload_messaging'])  ?   $adforestAPI['allow_media_upload_messaging'] :  false;
       
        if($allow_media){
            $allow_media    =  true;
        }
        else{           
            $allow_media   = false;
        }
        $message_setting['attachment_allow']     =  $allow_media;
        $message_setting['attachment_type']     =  isset($adforestAPI['sb_media_upload_messaging_type'])  ?   $adforestAPI['sb_media_upload_messaging_type'] :  'images';
        
        $image_size_combine    =   isset($adforestAPI['sb_media_image_size'])  ?   $adforestAPI['sb_media_image_size'] :  '';
        $image_size_arr        =   explode('-', $image_size_combine);
        $image_size_bytes      =   isset($image_size_arr[0])   ?  $image_size_arr[0]   : "819200";
        $image_size_kb         =     isset($image_size_arr[1])   ?  $image_size_arr[1]   : "800kb";
        $message_setting['image_size']     =  $image_size_bytes;
        
                                 
        $att_size_combine    =  isset($adforestAPI['sb_media_attachment_size'])  ?   $adforestAPI['sb_media_attachment_size'] :  '';
        $att_size_arr        =   explode('-', $att_size_combine);
        $att_size_bytes      =   isset($att_size_arr[0])   ?  $att_size_arr[0]   : "819200";
        $att_size_kb         =   isset($att_size_arr[1])   ?  $att_size_arr[1]   : "800kb";
        $message_setting['attachment_size']     =  $att_size_bytes;
   
        $attachment_formats    =     isset($adforestAPI['sb_message_attach_formats'])  ?   $adforestAPI['sb_message_attach_formats'] :  '';     
        $attachment_format_string    =    "pdf,txt";
         
         if(!empty($attachment_formats)){
              $attachment_format_string  =   "";
             foreach($attachment_formats as $attachment_format){                             
                 $attachment_format_string   .=  $attachment_format . ", ";
             }            
         }
        
        $message_setting['attachment_format']     =  $attachment_formats;
            
        
        $message_setting['upload_txt']     = esc_html__('What do you like to upload','adforest-rest-api');
        $img_size_txt   = esc_html__("Image size limit is",'adforest-rest-api');
        $message_setting['image_limit_txt']     = $img_size_txt   . " " . $image_size_kb;
        
        $doc_size_txt     =    esc_html__("Documents size limit is",'adforest-rest-api');
        $message_setting['doc_limit_txt']     =  $doc_size_txt . "  " . $att_size_kb;
      
        
        $doc_format_txt     =    esc_html__("Allowed formats are",'adforest-rest-api');
        $message_setting['doc_format_txt']     =  $doc_format_txt . "  " . $attachment_format_string;
      
        $message_setting['upload_image']     =  esc_html__("Upload Images",'adforest-rest-api');
        $message_setting['upload_doc']     =  esc_html__("Upload Document",'adforest-rest-api');
        $data['message_setting']      =   $message_setting; 
        
        
              
        $data['is_typing'] = __("is typing", "adforest-rest-api");
        /* array_reverse */
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) count($commentsData), "has_next_page" => $has_next_page);

        $more_message = ($paged > 1) ? __("No More Chat Found", "adforest-rest-api") : __("No Chat Found", "adforest-rest-api");
        $message = ( $success == false ) ? $more_message : $message2;

        $message = ( $message != "" ) ? $message : "";
        if ($verifed_phone_number) {
            if ($message_by_user != "") {
                $message = __("Please verify your phone number to send message.", "adforest-rest-api");
                return $response = array('success' => false, 'data' => $data, 'message' => $message);
            }
        }

        return $response = array('success' => $success, 'data' => $data, 'message' => $message);
    }

}








if (!function_exists('adforestAPI_messages_chat_post_media')) {

    function adforestAPI_messages_chat_post_media($request) {
       
       global $adforestAPI;        
        $json_data = $request->get_json_params();
        $ad_id = (isset($_POST['ad_id']) && $_POST['ad_id'] != "" ) ? $_POST['ad_id'] : '';
        $sender_id = (isset($_POST['sender_id']) && $_POST['sender_id'] != "" ) ? (int) $_POST['sender_id'] : '';
        $receiver_id = (isset($_POST['receiver_id']) && $_POST['receiver_id'] != "" ) ? (int) $_POST['receiver_id'] : '';
        $type = (isset($_POST['type']) && $_POST['type'] != "" ) ? $_POST['type'] : 'sent';
        $message = (isset($_POST['message']) && $_POST['message'] != "" ) ? $_POST['message'] : '';
        $message_by_user = $message;
        $data['btn_text'] = __("block User", "adforest-rest-api");    
        $current_user = get_current_user_id();
        if($type == 'sent'){
            if( $current_user != $sender_id )
            {
                $bloker_user_id2 = $sender_id;
            }
            else
            {
                $bloker_user_id2 = $receiver_id;
            }
        }
        else
        {
            if( $current_user != $sender_id )
                {
                    $bloker_user_id2 = $sender_id;
                }
                else
                {
                    $bloker_user_id2 = $receiver_id;
                }
        }
        
      
        $data['is_block'] = FALSE;
        $blocked_user_array1 = get_user_meta($current_user, 'adforest_blocked_users', true);
       
        if (isset($blocked_user_array1) && !empty($blocked_user_array1) && is_array($blocked_user_array1) && in_array($bloker_user_id2, $blocked_user_array1)) {
            $success = false;
            $message = __("Unblock this user to send message.", "adforest-rest-api") . ' - '. $patanae;
            $data['btn_text'] = __("Unblock User", "adforest-rest-api");
            $data['is_block'] = TRUE;
            return $response = array('success' => $success, 'message' => $message, 'data' => $data);
        }

        $blocked_user_array2 = get_user_meta($bloker_user_id2, 'adforest_blocked_users', true);

        if (isset($blocked_user_array2) && !empty($blocked_user_array2) && is_array($blocked_user_array2) && in_array($current_user, $blocked_user_array2)) {
            $success = false;
            $message = __("You can't send message to this user.", "adforest-rest-api");
            $data['btn_text'] = __("block User", "adforest-rest-api");
            $data['is_block'] = FALSE;
            return $response = array('success' => $success, 'message' => $message, 'data' => $data);
        }

        $user = wp_get_current_user();
        $user_id = (int) $user->data->ID;
        $authors = array($sender_id, $user_id);
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

     
        $posts_per_page = 10;

        if($type == 'sent'){
            $queryID2 =  ( $current_user == $sender_id ) ? $sender_id : $receiver_id;
        }
        else{
            $queryID2 = ( $current_user != $sender_id ) ? $sender_id : $receiver_id;
        }

      
          $message2 = '';
          $verifed_phone_number = adforestAPI_check_if_phoneVerified();
          
          

          $attachment_ids    =   array();
          $attachments_files  =   array();
               
         /* if request from andorid upload images in different way */ 
          if (isset($_FILES)  && !empty($_FILES) && ADFOREST_API_REQUEST_FROM != 'ios') {
           $attachments_files   =   array();
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $files = $_FILES["chat_media"];

            $attachment_ids = array();
            $attachment_idss = '';
            $ul_con = '';
            $file = array();
         
            $is_attachment_allowed    =    isset($adforestAPI['allow_media_upload_messaging'])  ?   $adforestAPI['allow_media_upload_messaging']   : false ;          
            if(!$is_attachment_allowed){
                 $msg = esc_html__("Media upload not allowed", 'adforest-rest-api');
                 return $response = array('success' => false, 'data' => "", 'message' => $msg);
                    
            }
            
            $condition_img = isset($adforestAPI['sb_media_attachment_limit']) ? $adforestAPI['sb_media_attachment_limit'] : 1;
              
           if(!empty($files)){
             foreach ($files['name'] as $key => $value) {
             if ($files['name'][$key]) {
                $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                );
                $_FILES = array("portfolio_upload" => $file);
                
                $upload_overrides  = array('test_form',false);
                        
                        
                 foreach ($_FILES as $file => $array) {
                    // Check uploaded size
                  
                 $attach_id = media_handle_upload($file, -1);
                 
                  if ($attach_id && !isset($attach_id['error'])) {
                    /*                     * ***** Assign image to ad *********** */
                   
                    $attachment_ids[] = $attach_id;
                }         
                 
                    }  
             } 
           }
          } 
                 }
                 
                 
                 
         if (isset($_FILES)  && !empty($_FILES) && ADFOREST_API_REQUEST_FROM == 'ios') {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $files = $_FILES;

            $attachment_ids = array();
            $attachment_idss = '';
            $ul_con = '';
            $file = array();
         
            $is_attachment_allowed    =    isset($adforestAPI['allow_media_upload_messaging'])  ?   $adforestAPI['allow_media_upload_messaging']   : false ;          
            if(!$is_attachment_allowed){
                 $msg = esc_html__("Media upload not allowed", 'adforest-rest-api');
                 return $response = array('success' => false, 'data' => "", 'message' => $msg);
                    
            }
            
            $condition_img = isset($adforestAPI['sb_media_attachment_limit']) ? $adforestAPI['sb_media_attachment_limit'] : 1;
               
             foreach ($_FILES as $key => $val) {
                $uploadedfile = $_FILES["$key"];
                 $upload_overrides = array('test_form' => false);             
                 $movefile =  wp_handle_upload($uploadedfile, $upload_overrides);                
                   if ($movefile && !isset($movefile['error'])) {
                    /*                     * ***** Assign image to ad *********** */
                    $filename = $movefile['url'];
                    $absolute_file = $movefile['file'];
                    $parent_post_id = $ad_id;
                    $filetype = wp_check_filetype(basename($filename), null);
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                          
                    );
                    // Insert the attachment.
                    $attach_id = wp_insert_attachment($attachment, $absolute_file);           
                    $attachment_ids[] = $attach_id;
                
             }
          }  
         } 
          
          
     
        if ($ad_id != "" && $sender_id != "" && $receiver_id != "" ) {
            if (function_exists('adforestAPI_add_messages_get')) {
                if ($verifed_phone_number == false) {                  
                    $message2 = adforestAPI_add_messages_get($ad_id, $queryID2, $sender_id, $receiver_id, $type, $message ,$attachment_ids);
                    
                    }
            }
        }


        if ($type == 'sent') {
            
            $authors = array($receiver_id, $user_id);
          
            if($message == "")
            {
                $queryID = ( $current_user == $sender_id ) ? $sender_id : $receiver_id;
            }
            else{
                $queryID = ( $current_user != $receiver_id ) ? $sender_id : $receiver_id;
            }
            $authors = array($receiver_id, $user_id);

        } else {

            if($message == "")
            {
                $queryID = ( $current_user != $sender_id ) ? $sender_id : $receiver_id;
            }
            else{
                $queryID = ( $current_user == $receiver_id ) ? $sender_id : $receiver_id;
            }

           // $authors = array($sender_id, $user_id);
            $authors = array($queryID, $user_id);
        }

        //print_r($authors);

        $cArgs = array('author__in' => $authors, 'post_id' => $ad_id, 'parent' => $queryID, 'orderby' => 'comment_date', 'order' => 'DESC',);

/*  if( $type == "sent" )
        {
            if($queryID == $receiver_id)
            {
                $get_other_user_name = $sender_id;
            }
            else
            {
                $get_other_user_name = $receiver_id;
            }            
        }
        else{
            if($queryID != $receiver_id)
            {
                $get_other_user_name = $receiver_id;
            }
            else
            {
                $get_other_user_name = $sender_id;
            }  
        }

*/



      // print_r($cArgs);
       // echo  " | ".$queryID;
        $commentsData = get_comments($cArgs);
        $total_posts = count($commentsData);
        $max_num_pages = ceil($total_posts / $posts_per_page);
        $args = array(
            'author__in' => $authors,
            'post_id' => $ad_id,
            'parent' => $queryID,
            'orderby' => 'comment_date',
            'order' => 'DESC',
            'paged' => $paged,
            'offset' =>  0,
            'number' => $posts_per_page,
        );


        $comments = get_comments($args);
        $chat = array();
        $chatHistory = array();
        $success = false;

        $get_other_user_name = ( $type != 'sent' ) ? $receiver_id : $sender_id;
        $author_obj = @get_user_by('id', $get_other_user_name);
        $page_title = ($author_obj) ? $author_obj->display_name : __("Chat Box", "adforest-rest-api");
        $data['page_title'] = $page_title;
        $data['ad_title'] = get_the_title($ad_id);
        $data['ad_img'] = adforestAPI_get_ad_image($ad_id, 1, 'thumb');
        $data['ad_date'] = get_the_date("", $ad_id);
        $sender_img = adforestAPI_user_dp($sender_id);
        $receiver_img = adforestAPI_user_dp($receiver_id);
        $data['ad_price'] = adforestAPI_get_price('', $ad_id);
        /* Add Read Status Here Starts */

        //update_comment_meta(get_current_user_id(), $ad_id . "_" . $get_other_user_name, 1);
        
        /* Add Read Status Here Ends */
        if (count($comments) > 0) {
            $success = true;
            foreach ($comments as $comment) {
                
                if ($type == 'sent') {
                    $messageType = ( $comment->comment_parent != $comment->user_id ) ? 'reply' : 'message';
                } else {
                    $messageType = ( $comment->comment_parent != $comment->user_id ) ? 'message' : 'reply';
                }
                $chat['img'] = ( $comment->comment_parent != $comment->user_id ) ? $receiver_img : $sender_img;
                $chat['id'] = $comment->comment_ID;
                $chat['ad_id'] = $comment->comment_post_ID;
                $chat['text'] = $comment->comment_content;
                $chat['date'] = adforestAPI_timeago($comment->comment_date);
                $chat['type'] = $messageType;
                 $chat_images   =  array();  
                 $chat_files    =  array();
                 $file_meta     =     get_comment_meta($comment->comment_ID,'comment_file_meta',true);                   
                 $file_meta      =      $file_meta != ""    ? unserialize($file_meta)    :  array();                            
                 if(!empty($file_meta)){    
                        $files_html   .= '<div>';
                        $count   =   0;                       
                        foreach ($file_meta as $attach_id){                                                
                                  $file_url         =    wp_get_attachment_url($attach_id); 
                                  if($file_url != ""){                                    
                                      if(wp_attachment_is_image($attach_id)){
                                          
                                          $chat_images[]   =  $file_url;
                                      }
                                      else{
                                         $chat_files[]   =  $file_url;                                        
                                      }                                                                            
                                  }
                                  $count++;
                                }    
                            }
                            
                $is_image    =   false;  
                $is_file    =   false;
                
                if(!empty($chat_images)){
                     $is_image    =   true;  
                    
                }
                if(!empty($chat_files)){
                     $is_file    =   true;  
                    
                }
                $chat['is_file']    =  $is_file;             
                $chat['is_image']    =  $is_image;               
                $chat['images']   = $chat_images;
                $chat['files']   = $chat_files;
                $chatHistory[] = $chat;
            }
        }
        $data['chat'] = $chatHistory;
        $data['is_typing'] = __("is typing", "adforest-rest-api");
        /* array_reverse */
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) count($commentsData), "has_next_page" => $has_next_page);

        $more_message = ($paged > 1) ? __("No More Chat Found", "adforest-rest-api") : __("No Chat Found", "adforest-rest-api");
        $message = ( $success == false ) ? $more_message : $message2;

        $message = ( $message != "" ) ? $message : "";
        if ($verifed_phone_number) {
            if ($message_by_user != "") {
                $message = __("Please verify your phone number to send message.", "adforest-rest-api");
                return $response = array('success' => false, 'data' => $data, 'message' => $message);
            }
        }

        return $response = array('success' => $success, 'data' => $data, 'message' => $message);
    }

}































add_action('rest_api_init', 'adforestAPI_messages_chat_api_hooks_popup', 0);

function adforestAPI_messages_chat_api_hooks_popup() {
    register_rest_route('adforest/v1', '/message/popup/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_messages_chat_submit_popup',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route('adforest/v1', '/message/popup/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_chat_submit_popup',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_messages_chat_submit_popup')) {

    function adforestAPI_messages_chat_submit_popup($request) {
        $verifed_phone_number = adforestAPI_check_if_phoneVerified();
        if ($verifed_phone_number) {
            $message2 = __("Please verify your phone number to send message.", "adforest-rest-api");
            return array('success' => false, 'data' => '', 'message' => $message2);
        }

        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id']) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
        $message = (isset($json_data['message']) && $json_data['message'] != "" ) ? $json_data['message'] : '';
        $user = wp_get_current_user();
        $sender_id = $user->data->ID;
        $receiver_id = get_post_field('post_author', $ad_id);
        $queryID = $sender_id;
        $message2 = __("Something went wrong", "adforest-rest-api");
        $success = false;
        if ($ad_id != "" && $sender_id != "" && $receiver_id != "" && $message != "") {
            if (function_exists('adforestAPI_add_messages_get'))
                $message2 = adforestAPI_add_messages_get($ad_id, $queryID, $sender_id, $receiver_id, 'sent', $message);
            $success = true;
        }
        return $response = array('success' => $success, 'data' => '', 'message' => $message2);
    }

}

if (!function_exists('adforestAPI_add_messages_get')) {

    function adforestAPI_add_messages_get($ad_id = '', $queryID = '', $sender_id = '', $receiver_id = '', $type = 'sent', $message = '' , $attachment_ids =  array()) {
        $user = wp_get_current_user();
        $user_id = (int) $user->data->ID;
        $user_email = $user->data->user_email;
        $display_name = $user->data->display_name;
        if (function_exists('adforest_set_date_timezone')) {
            adforest_set_date_timezone();
        }
        //$time = current_time('mysql');
        $time = current_time('mysql', 1);
        $data = array(
            'comment_post_ID' => $ad_id,
            'comment_author' => $display_name,
            'comment_author_email' => $user_email,
            'comment_author_url' => '',
            'comment_content' => $message,
            'comment_type' => 'ad_post',
            'comment_parent' => $queryID,
            'user_id' => $user_id,
            'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
            'comment_date' => $time,
            'comment_approved' => 1,
        );

        $comment_id = wp_insert_comment($data);
        if ($comment_id) {


        if( $type == "sent" )
        {
            if($queryID == $receiver_id)
            {
                $typeData = $sender_id;
            }
            else
            {
                $typeData = $receiver_id;
            }            
        }
        else{
            if($queryID != $receiver_id)
            {
                $typeData = $sender_id;
            }
            else
            {
                $typeData = $receiver_id;
            }  
        }

            //$typeData = ( $type != "sent" ) ? $receiver_id : $sender_id;

            update_comment_meta($typeData, $ad_id . "_" . $user_id, 0);
            /* Send Email When Message On Ad */
            adforestAPI_get_notify_on_ad_message($ad_id, $typeData, $message, $display_name);



            adforestAPI_messages_sent_func($type, $receiver_id, $sender_id, $user_id, $comment_id, $ad_id, $message, $time);



            $messageString = __("Message sent successfully .", 'adforest-rest-api');
            
            
            if(!empty($attachment_ids)){
                     
                      update_comment_meta($comment_id, 'comment_file_meta', serialize($attachment_ids));
                 } 
            
            
            
        } else {
            $messageString = __("Message not sent, please try again later.", 'adforest-rest-api');
        }
        return $messageString;
    }
}

if (!function_exists('adforestAPI_messages_sent_func')) {
    function adforestAPI_messages_sent_func($type, $receiver_id, $sender_id, $user_id, $comment_id, $ad_id, $message, $time) {
        global $adforestAPI;
        if (isset($adforestAPI['app_settings_message_firebase']) && $adforestAPI['app_settings_message_firebase'] == true) {
            $chat = array();
            /*$fbuserid = ( $type != "sent" ) ? $sender_id : $receiver_id;*/
            $queryID = ( $type == 'sent' ) ? $user_id : $sender_id;
        

          /*  if( $type == "sent" )
            {
                if($queryID == $receiver_id)
                {
                    $fbuserid = $sender_id;
                }
                else
                {
                    $fbuserid = $receiver_id;
                }            
            }


            else{
                if($queryID != $receiver_id)
                {
                    $fbuserid = $receiver_id;
                }
                else
                {
                    $fbuserid = $sender_id;
                }  
            }
        */
            if(get_current_user_id()   ==  $sender_id ){

                $fbuserid = $receiver_id;
            }
            else{
            $fbuserid = $sender_id;
            }
            $firebase_meta_key = ( ADFOREST_API_REQUEST_FROM == 'ios' ) ? '_sb_user_firebase_id_ios' : '_sb_user_firebase_id';
            /*$f_reg_id = get_user_meta($fbuserid, $firebase_meta_key, true);*/
             $fregidios = get_user_meta($fbuserid, '_sb_user_firebase_id_ios', true);
             $fregidandroid = get_user_meta($fbuserid, '_sb_user_firebase_id', true);
            if ($fregidios != "" || $fregidandroid != "") {
                $fbuserid_message_type = ( $type == "sent" ) ? "receive" : "sent";
                $messager_img = ( $type == "sent" ) ? adforestAPI_user_dp($sender_id) : adforestAPI_user_dp($receiver_id);
                if ($type == 'sent') {
                    $messageType = ( $queryID != $user_id ) ? 'message' : 'reply';
                } else {
                    $messageType = ( $queryID != $user_id ) ? 'reply' : 'message';
                }
                $chat['img'] = $messager_img;
                $chat['id'] = $comment_id;
                $chat['ad_id'] = $ad_id;
                $chat['text'] = $message;
                $chat['date'] = adforestAPI_timeago($time);
                $chat['type'] = $messageType;

                $message_data = array
                    (
                    "sound" => 'default',
                    "content_available" => true,
                    "priority" => 'high',
                    'topic' => 'chat',
                    'message' => $message,
                    'title' => get_the_title($ad_id),
                    'adId' => $ad_id,
                    'senderId' => $sender_id,
                    'recieverId' => $receiver_id,
                    'type' => $fbuserid_message_type,
                    'chat' => $chat,
                    'notification' => $chat,
                );

                /* Added new support on 6 sep 2018 */
                if ($fregidios != "") {
                    adforestAPI_firebase_notify_func($fregidios, $message_data);
                }
                if ($fregidandroid != "") {

                    adforestAPI_firebase_notify_func($fregidandroid, $message_data);
                }
            }
        }
    }

}

/* -----
  Ad Messages Users Chat
  ----- */
add_action('rest_api_init', 'adforestAPI_messages_sent_api_hooks_get', 0);

function adforestAPI_messages_sent_api_hooks_get() {
    register_rest_route('adforest/v1', '/message/sent/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_messages_sent_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_messages_sent_get')) {

    function adforestAPI_messages_sent_get($request) {

        $json_data = $request->get_json_params();
        $ad_id = (isset($json_data['ad_id']) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
        $sender_id = (isset($json_data['sender_id']) && $json_data['ad_id'] != "" ) ? (int) $json_data['sender_id'] : '';
        $receiver_id = (isset($json_data['receiver_id']) && $json_data['receiver_id'] != "" ) ? (int) $json_data['receiver_id'] : '';
        $message = (isset($json_data['message']) && $json_data['message'] != "" ) ? (int) $json_data['message'] : '';

        $user = wp_get_current_user();
        $user_id = (int) $user->data->ID;
        $authors = array($sender_id, $user_id);

        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $posts_per_page = 10;
        $start = ($paged - 1) * $posts_per_page;
        $cArgs = array('author__in' => $authors, 'post_id' => $ad_id, 'parent' => $user_id, 'orderby' => 'comment_date', 'order' => 'ASC',);
        $commentsData = get_comments($cArgs);
        $total_posts = count($commentsData);
        $max_num_pages = ceil($total_posts / $posts_per_page);

        $args = array(
            'author__in' => $authors,
            'post_id' => $ad_id,
            'parent' => $user_id,
            'orderby' => 'comment_date',
            'order' => 'ASC',
            'paged' => $paged,
            'offset' => $start,
            'number' => $posts_per_page,
        );
        $comments = get_comments($args);
        $chat = array();
        $chatHistory = array();
        $success = false;

        if (count($comments) > 0) {
            $success = true;
            foreach ($comments as $comment) {
                $messageType = ( $comment->comment_parent != $comment->user_id ) ? 'reply' : 'message';
                $chat['id'] = $comment->comment_ID;
                $chat['ad_id'] = $comment->comment_post_ID;
                $chat['text'] = $comment->comment_content;
                $chat['date'] = $comment->comment_date;
                $chat['type'] = $messageType;
                $chatHistory[] = $chat;
            }
        }

        $data['chat'] = $chatHistory;
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) count($commentsData), "has_next_page" => $has_next_page);

        $message = ( $success == false ) ? __("No Chat Found", "adforest-rest-api") : '';
        return $response = array('success' => $success, 'data' => $data, 'message' => $message);
    }

}

if (!function_exists('adforestAPI_messages_get')) {

    function adforestAPI_count_ad_messages($ad_id = '', $user_id = '') {
        global $wpdb;
        $total = 0;
        if ($ad_id != '' && $user_id != '') {
            $total = $wpdb->get_var("SELECT COUNT(DISTINCT(comment_author)) as total FROM $wpdb->comments WHERE comment_post_ID = '" . $ad_id . "' AND user_id != '" . $user_id . "'");
        }
        return $total;
    }

}