<?php
/* -----
  Ad rating And Comments Starts
  ----- */
add_action('rest_api_init', 'adforestAPI_adDetails_rating_hook', 0);

function adforestAPI_adDetails_rating_hook() {
    register_rest_route('adforest/v1', '/ad_post/ad_rating/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_adDetails_rating_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route('adforest/v1', '/ad_post/ad_rating/rating_emojies', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_adDetails_rating_emojies',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route('adforest/v1', '/ad_post/ad_rating/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_adDetails_rating1_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route('adforest/v1', '/ad_post/ad_rating/new/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_adDetails_add_rating',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_adDetails_rating_emojies')) {

    function adforestAPI_adDetails_rating_emojies($request) {
        $json_data = $request->get_json_params();

        $reaction_id = (isset($json_data['r_id']) && $json_data['r_id'] != "" ) ? $json_data['r_id'] : '';
        $comment_id = (isset($json_data['c_id']) && $json_data['c_id'] != "" ) ? $json_data['c_id'] : '';

        if (!is_user_logged_in()) {
            return array('success' => false, 'message' => __("You need to login.", 'adforest-rest-api'));
        }




        if ($reaction_id && $comment_id) {
            if (get_user_meta(get_current_user_id(), 'adforest_listing_review_submit_id' . $comment_id, true) == $comment_id) {
                return array('success' => false, 'message' => __("You have already reacted on this review.", 'adforest-rest-api'));
            } else {
                //get comment listing id and current user id
                $get_comment = get_comment($comment_id);
                $listing_id = $get_comment->comment_post_ID;

                update_user_meta(get_current_user_id(), 'adforest_listing_review_submit_id' . $comment_id, $comment_id);

                if ($reaction_id == 1) {
                    if (get_comment_meta($comment_id, 'review_like', true) != "") {
                        $current_count = get_comment_meta($comment_id, 'review_like', true);
                        $update_count = $current_count + 1;
                        update_comment_meta($comment_id, 'review_like', $update_count);
                        return array('success' => true, 'data' => $update_count);
                    } else {
                        $total_count = '1';
                        update_comment_meta($comment_id, 'review_like', $total_count);
                        return array('success' => true, 'data' => $total_count);
                    }
                }
                if ($reaction_id == 2) {
                    if (get_comment_meta($comment_id, 'review_love', true) != "") {
                        $current_count = get_comment_meta($comment_id, 'review_love', true);
                        $update_count = $current_count + 1;
                        update_comment_meta($comment_id, 'review_love', $update_count);
                        return array('success' => true, 'data' => $update_count);
                    } else {
                        $total_count = '1';
                        update_comment_meta($comment_id, 'review_love', $total_count);
                        return array('success' => true, 'data' => $total_count);
                    }
                }
                if ($reaction_id == 3) {
                    if (get_comment_meta($comment_id, 'review_wow', true) != "") {
                        $current_count = get_comment_meta($comment_id, 'review_wow', true);
                        $update_count = $current_count + 1;
                        update_comment_meta($comment_id, 'review_wow', $update_count);
                        return array('success' => true, 'data' => $update_count);
                    } else {
                        $total_count = '1';
                        update_comment_meta($comment_id, 'review_wow', $total_count);
                        return array('success' => true, 'data' => $total_count);
                    }
                }
                if ($reaction_id == 4) {
                    if (get_comment_meta($comment_id, 'review_angry', true) != "") {
                        $current_count = get_comment_meta($comment_id, 'review_angry', true);
                        $update_count = $current_count + 1;
                        update_comment_meta($comment_id, 'review_angry', $update_count);
                        return array('success' => true, 'data' => $update_count);
                    } else {
                        $total_count = '1';
                        update_comment_meta($comment_id, 'review_angry', $total_count);
                        return array('success' => true, 'data' => $total_count);
                    }
                }
            }
        }

        return array('success' => false, 'message' => __("Something went wrong please try again.", 'adforest-rest-api'));
    }

}





if (!function_exists('adforestAPI_adDetails_add_rating')) {

    function adforestAPI_adDetails_add_rating($request) {
        global $adforestAPI; /* For Redux */
        $json_data = $request->get_json_params();
        $sender_id = get_current_user_id();
        $sender = get_userdata($sender_id);
        $json_data = $request->get_json_params();

        $ad_id = (isset($json_data['ad_id']) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
        //$ad_id 	         = (isset( $json_data['rating_comments'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : 0;
        $rating_stars = (isset($json_data['rating']) && $json_data['rating'] != "" ) ? $json_data['rating'] : 1;
        $rating_comments = (isset($json_data['rating_comments']) && $json_data['rating_comments'] != "" ) ? $json_data['rating_comments'] : '';
        $page_number = (isset($json_data['page_number']) && $json_data['page_number'] != "" ) ? $json_data['page_number'] : 1;

        $rply_comment_id = (isset($json_data['comment_id']) && $json_data['comment_id'] != "" ) ? $json_data['comment_id'] : '';
        $is_ratingReply = ( $rply_comment_id == "" ) ? true : false;
        $poster_id = get_post_field('post_author', $ad_id);

        if ($sender_id == 0 || $sender_id == "" || $ad_id == "") {
            return array('success' => false, 'data' => '', 'message' => __("Something went wrong", 'adforest-rest-api'));
        }
        if ($is_ratingReply) {
            if ($sender_id == $poster_id) {
                return array('success' => false, 'data' => '', 'message' => __("Ad author can't post rating", 'adforest-rest-api'));
            }

            if (!$adforestAPI['sb_update_rating'] && get_user_meta($sender_id, 'ad_ratting_' . $sender_id, true) == $ad_id) {
                return array('success' => false, 'data' => '', 'message' => __("You've posted rating already", 'adforest-rest-api'));
            }
            /* Rating update starts starts */
            if (isset($adforestAPI['sb_update_rating']) && $adforestAPI['sb_update_rating']) {

                $args = array('type__in' => array('ad_post_rating'), 'post_id' => $ad_id, 'user_id' => $sender_id, 'number' => 1, 'parent' => 0,);
                $comment_exist = get_comments($args);
                if (count($comment_exist) > 0) {
                    $comment = array();
                    $comment['comment_ID'] = $comment_exist[0]->comment_ID;
                    $comment['comment_content'] = $rating_comments;
                    wp_update_comment($comment);
                    update_comment_meta($comment_exist[0]->comment_ID, 'review_stars', $rating_stars);
                    
                    if (isset($adforestAPI['sb_rating_email_author']) && $adforestAPI['sb_rating_email_author']) {
                        adforestAPI_email_ad_rating($ad_id, $sender_id, $rating_stars, $rating_comments);
                    }
                    return array('success' => true, 'data' => '', 'message' => __("Your rating has been updated", 'adforest-rest-api'));
                }
            }
            
            if (function_exists('adforest_set_date_timezone')) {
                    adforest_set_date_timezone();
                }
            /* Rating update starts ends */

            /* New Rating posts starts */
            $time = current_time('mysql',1);
            $data = array(
                'comment_post_ID' => $ad_id,
                'comment_author' => $sender->display_name,
                'comment_author_email' => $sender->user_email,
                'comment_author_url' => '',
                'comment_content' => $rating_comments,
                'comment_type' => 'ad_post_rating',
                'user_id' => $sender_id,
                'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                'comment_date' => $time,
                'comment_approved' => 1
            );

            $comment_id = wp_insert_comment($data);
            if ($comment_id) {
                update_comment_meta($comment_id, 'review_stars', $rating_stars);
                update_user_meta($sender_id, 'ad_ratting_' . $sender_id, $ad_id);
                if (isset($adforestAPI['sb_rating_email_author']) && $adforestAPI['sb_rating_email_author']) {
                    adforestAPI_email_ad_rating($ad_id, $sender_id, $rating_stars, $rating_comments);
                }
                return array('success' => true, 'data' => '', 'message' => __("Your rating has been posted", 'adforest-rest-api'));
            }
            /* New Rating posts ends */
        } else {
            /* Rating Reply posts starts */
            if ($rply_comment_id == "") {
                return array('success' => false, 'data' => '', 'message' => __("Something went wrong", 'adforest-rest-api'));
            }
            if ($sender_id != $poster_id) {
                return array('success' => false, 'data' => '', 'message' => __("Only ad author can reply rating", 'adforest-rest-api'));
            }

            $args = array('type__in' => array('ad_post_rating'), 'post_id' => $ad_id, 'user_id' => $sender_id, 'number' => 1, 'parent' => $rply_comment_id,);
            $comment_exist = get_comments($args);
            if (count($comment_exist) > 0) {
                $comment = array();
                $comment['comment_ID'] = $comment_exist[0]->comment_ID;
                $comment['comment_content'] = $rating_comments;
                wp_update_comment($comment);

                if (isset($adforest_theme['sb_rating_reply_email']) && $adforest_theme['sb_rating_reply_email']) {
                    $comment_data = get_comment($rply_comment_id);
                    $rating = get_comment_meta($rply_comment_id, 'review_stars', true);
                    adforestAPI_email_ad_rating_reply($ad_id, $comment_data->user_id, $rating_comments, $rating, $comment_data->comment_content);
                }
                $data = adforestAPI_adDetails_rating_get($ad_id, $page_number, false);
                return array('success' => true, 'data' => $data, 'message' => __("Your reply has been updated", 'adforest-rest-api'));
            }

            $time = current_time('mysql',1);
            $data = array(
                'comment_post_ID' => $ad_id,
                'comment_author' => $sender->display_name,
                'comment_author_email' => $sender->user_email,
                'comment_author_url' => '',
                'comment_content' => $rating_comments,
                'comment_type' => 'ad_post_rating',
                'user_id' => $sender_id,
                'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                'comment_date' => $time,
                'comment_parent' => $rply_comment_id,
                'comment_approved' => 1
            );

            $comment_id = wp_insert_comment($data);
            if ($comment_id) {
                update_user_meta($sender_id, 'ad_comment_reply' . $rply_comment_id, $comment_id);
                if (isset($adforestAPI['sb_rating_reply_email']) && $adforestAPI['sb_rating_reply_email']) {
                    $comment_data = get_comment($rply_comment_id);
                    $rating = get_comment_meta($rply_comment_id, 'review_stars', true);
                    adforestAPI_email_ad_rating_reply($ad_id, $comment_data->user_id, $rating_comments, $rating, $comment_data->comment_content);
                }
                $data = adforestAPI_adDetails_rating_get($ad_id, $page_number, false);
                return array('success' => true, 'data' => $data, 'message' => __("Your reply has been posted", 'adforest-rest-api'));
            }
            /* Rating Reply posts ends */
        }
        return array('success' => false, 'data' => '', 'message' => __("Something went please try again.", 'adforest-rest-api'));
    }

}

if (!function_exists('adforestAPI_adDetails_rating1_get')) {

    function adforestAPI_adDetails_rating1_get($request) {
        $json_data = $request->get_json_params();
        $post_id = (isset($json_data['ad_id']) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : 0;
        $page_number = (isset($json_data['page_number']) && $json_data['page_number'] != "" ) ? $json_data['page_number'] : 1;
        $data = adforestAPI_adDetails_rating_get($post_id, $page_number, true);
        return $data;
    }

}

if (!function_exists('adforestAPI_adDetails_rating_get')) {

    function adforestAPI_adDetails_rating_get($post_id = 0, $page_number = 1, $return_arr = false) {
        global $adforestAPI; /* For Redux */
        $user_id = get_current_user_id();
        /* Load Required Data Starts */
        $poster_id = get_post_field('post_author', $post_id);
        /* Load Required Data Ends */
        $limit_number = (isset($adforestAPI['sb_rating_max']) && $adforestAPI['sb_rating_max'] ) ? $adforestAPI['sb_rating_max'] : 10;
        /* Pagination Settings */
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($page_number)) {
            $paged = $page_number;
        } else {
            $paged = 1;
        }
        $page = $paged;
        $limit = $limit_number;
        $offset = ($page * $limit) - $limit;
        $args = array(
            'type__in' => array('ad_post_rating'),
            'number' => $limit,
            'offset' => $offset,
            'parent' => 0, // parent only
            'post_id' => $post_id, // use post_id, not post_ID
        );

        $count_comments = count(get_comments(array('post_id' => $post_id, 'type' => 'ad_post_rating', 'parent' => 0)));
        $comments = get_comments($args);
        $ratings = array();
        $count = 0;
        if (count($comments) > 0) {
            $can_reply = ( get_post_meta($post_id, '_adforest_ad_status_', true) == 'active' && get_current_user_id() == $poster_id ) ? true : false;
            $reply_text = __("Reply", "adforest-rest-api");
            foreach ($comments as $comment) {
                $commenter = get_userdata($comment->user_id);
                if ($commenter) {

                    $got_likes = "";
                    $got_love = "";
                    $got_wow = "";
                    $got_angry = "";
                    $reactions_arr = array();
                    if (get_comment_meta($comment->comment_ID, 'review_like', true) != "" && get_comment_meta($comment->comment_ID, 'review_like', true) > 0) {
                        $got_likes = get_comment_meta($comment->comment_ID, 'review_like', true);
                        $reactions_arr['like'] = $got_likes;
                    } else {
                        $reactions_arr['like'] = $got_likes;
                    }
                    if (get_comment_meta($comment->comment_ID, 'review_love', true) != "" && get_comment_meta($comment->comment_ID, 'review_love', true) > 0) {
                        $got_love = get_comment_meta($comment->comment_ID, 'review_love', true);
                        $reactions_arr['love'] = $got_love;
                    } else {
                        $reactions_arr['love'] = $got_love;
                    }
                    if (get_comment_meta($comment->comment_ID, 'review_wow', true) != "" && get_comment_meta($comment->comment_ID, 'review_wow', true) > 0) {
                        $got_wow = get_comment_meta($comment->comment_ID, 'review_wow', true);
                        $reactions_arr['wow'] = $got_wow;
                    } else {
                        $reactions_arr['wow'] = $got_wow;
                    }
                    if (get_comment_meta($comment->comment_ID, 'review_angry', true) != "" && get_comment_meta($comment->comment_ID, 'review_angry', true) > 0) {
                        $got_angry = get_comment_meta($comment->comment_ID, 'review_angry', true);
                        $reactions_arr['angry'] = $got_angry;
                    } else {
                        $reactions_arr['angry'] = $got_angry;
                    }

                    $ratings[$count]['rating_id'] = $comment->comment_ID;
                    $ratings[$count]['rating_author'] = $comment->user_id;
                    $ratings[$count]['rating_author_name'] = $commenter->display_name;
                    $ratings[$count]['rating_author_image'] = adforestAPI_user_dp($comment->user_id);
                    $ratings[$count]['rating_date'] = get_comment_date(get_option('date_format'), $comment->comment_ID);
                    $ratings[$count]['rating_text'] = esc_html(adforestAPI_convert_uniText($comment->comment_content));
                    $ratings[$count]['rating_stars'] = get_comment_meta($comment->comment_ID, 'review_stars', true);
                    $ratings[$count]['can_reply'] = $can_reply;
                    $ratings[$count]['reply_text'] = $reply_text;
                    $ratings[$count]['current_page'] = $paged;

                    /* Reply Settings Starts */
                    $args_reply = array(
                        'type__in' => array('ad_post_rating'),
                        'number' => 1,
                        'parent' => $comment->comment_ID, // parent only
                        'post_id' => $post_id, // use post_id, not post_ID
                    );
                    $rate_rply = array();
                    $has_reply = false;
                    $replies = get_comments($args_reply);
                    if (count($replies) > 0) {
                        $rcount = 0;
                        $ad_author = get_userdata($poster_id);
                        $has_reply = true;
                        foreach ($replies as $reply) {
                            $rate_rply[$rcount]['rating_id'] = $reply->comment_ID;
                            $rate_rply[$rcount]['rating_author'] = $reply->user_id;
                            $rate_rply[$rcount]['rating_author_name'] = $ad_author->display_name;
                            $rate_rply[$rcount]['rating_author_image'] = adforestAPI_user_dp($reply->user_id);
                            ;
                            $rate_rply[$rcount]['rating_date'] = get_comment_date(get_option('date_format'), $reply->comment_ID);
                            $rate_rply[$rcount]['rating_text'] = esc_html(adforestAPI_convert_uniText($reply->comment_content));
                            $rate_rply[$rcount]['rating_user_stars'] = get_comment_meta($reply->comment_ID, 'review_stars', true);
                            $rate_rply[$rcount]['can_reply'] = false;
                            $rate_rply[$rcount]['reply_text'] = $reply_text;
                            $rate_rply[$rcount]['current_page'] = $paged;
                            $rcount++;
                        }
                    }
                    $ratings[$count]['has_reply'] = $has_reply;
                    $ratings[$count]['reply'] = $rate_rply;
                    $ratings[$count]['ad_reactions'] = $reactions_arr;
                    
                    /* Reply Settings Ends */
                    $count++;
                }
            }
        }

        /* Offers on my ads starts */
        $section_title = (isset($adforestAPI['sb_ad_rating_title']) && $adforestAPI['sb_ad_rating_title'] != "" ) ? $adforestAPI['sb_ad_rating_title'] : __("Ad Rating and Reviews", "adforest-rest-api");

        $email_author = (isset($adforestAPI['sb_rating_email_author']) && $adforestAPI['sb_rating_email_author'] ) ? true : false;
        $sb_rating_reply_email = (isset($adforestAPI['sb_rating_reply_email']) && $adforestAPI['sb_rating_reply_email'] ) ? true : false;
        $data["ad_id"] = $post_id;
        $data["ad_rating_emojies"] = (isset($adforestAPI['adforest_listing_review_enable_emoji']) && $adforestAPI['adforest_listing_review_enable_emoji'] ) ? true : false;
        $data["section_title"] = $section_title;
        /* $data["page_title"]      	= __("Ad Rating and Reviews", "adforest-rest-api"); */
        $data["ratings"] = $ratings;
        $data["rating_show"] = (isset($adforestAPI['sb_ad_rating']) && $adforestAPI['sb_ad_rating'] ) ? true : false;
        ;
        $data["title"] = __("Ad Rating Here", "adforest-rest-api");
        $data["textarea_text"] = __("Rating Comments", "adforest-rest-api");
        $data["textarea_value"] = "";
        $data["tagline"] = __("You can not edit it later.", "adforest-rest-api");
        $data["is_editable"] = (isset($adforestAPI['sb_update_rating']) && $adforestAPI['sb_update_rating'] ) ? true : false;
        $data["can_rate"] = ( get_post_meta($post_id, '_adforest_ad_status_', true) == 'active' ) ? true : false;
        $data["can_rate_msg"] = __("You can't rate this ad", "adforest-rest-api");
        $data["no_rating"] = __("Be the first one to rate this ad", "adforest-rest-api");
        $data["no_rating_message"] = __("No rating found.", "adforest-rest-api");
        $data["btn"] = __("Submit Your Rating", "adforest-rest-api");
        $data["loadmore_btn"] = __("Load More", "adforest-rest-api");
        $data["loadmore_btn_show"] = ( @$limit_number < @$count_comments ) ? true : false;
        $data["rply_dialog"]['text'] = __("Your reply text here", "adforest-rest-api");
        $data["rply_dialog"]['send_btn'] = __("Submit", "adforest-rest-api");
        $data["rply_dialog"]['cancel_btn'] = __("Cancel", "adforest-rest-api");
        $data["pagination"]['has_next_page'] = ($paged <= ceil(count((array) $count_comments) / $limit_number) && count((array) $ratings) > 0) ? true : false;
        $data["pagination"]['next_page'] = $paged + 1;
        $m = "";
        return $response = ($return_arr) ? array('success' => true, 'data' => $data, 'message' => $m) : $data;
    }

}