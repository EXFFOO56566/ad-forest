<?php
/* POSTS API HERE */
add_action('init', 'adforestAPI_post_type_rest_support', 25);

function adforestAPI_post_type_rest_support() {
    global $wp_post_types;
    /* be sure to set this to the name of your post type! */
    $post_type_name = 'post';
    if (isset($wp_post_types[$post_type_name])) {
        $wp_post_types[$post_type_name]->show_in_rest = true;
        $wp_post_types[$post_type_name]->rest_base = $post_type_name;
        $wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
    }
}

add_action('rest_api_init', 'adforestAPI_hook_for_getting_posts', 0);

function adforestAPI_hook_for_getting_posts() {
    register_rest_route(
            'adforest/v1', '/posts/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_get_posts_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route(
            'adforest/v1', '/posts/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_get_posts_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_ad_posts_get')) {

    function adforestAPI_get_posts_get($request) {
        $json_data = $request->get_json_params();
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $posts_per_page = get_option('posts_per_page');
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'order' => 'DESC',
            'orderby' => 'date'
        );
        $message = '';
        $args = apply_filters('adforestAPI_site_location_args', $args, 'ads');
        $posts = new WP_Query($args);
        $data = array();
        $arr = array();
        $post_data = array();
        if ($posts->have_posts()) {

            while ($posts->have_posts()) {
                $posts->the_post();
                $post_id = get_the_ID();
                $arr['post_id'] = $post_id;
                $arr['title'] = get_the_title();
                $arr['date'] = get_the_date("", $post_id);

                $list = array();
                $term_lists = wp_get_post_terms($post_id, 'category', array('fields' => 'all'));
                foreach ($term_lists as $term_list)
                    $list[] = array('id' => $term_list->term_id, 'name' => $term_list->name);
                $arr['cats'] = $list;
                $image = get_the_post_thumbnail_url($post_id, 'medium');
                if (!$image)
                    $image = '';

                $arr['has_image'] = ( $image ) ? true : false;
                $arr['image'] = $image;
                $comments = wp_count_comments($post_id);
                $arr['comments'] = $comments->approved;
                $arr['read_more'] = __("Read More", "adforest-rest-api");
                $post_data[] = $arr;
            }
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            $message = __("no posts found", "adforest-rest-api");
        }

        $data['post'] = $post_data;

        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $posts->max_num_pages ) ? true : false;

        $data['pagination'] = array("max_num_pages" => (int) $posts->max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) ($posts->found_posts), "has_next_page" => $has_next_page);

        $extra['page_title'] = __("Blog", "adforest-rest-api");
        $extra['comment_title'] = __("Comments", "adforest-rest-api");
        $extra['load_more'] = __("Load More", "adforest-rest-api");
        $extra['load_more'] = __("Load More", "adforest-rest-api");
        $extra['comment_form']['title'] = __("Post your comments here", "adforest-rest-api");
        $extra['comment_form']['textarea'] = __("Your comment here", "adforest-rest-api");
        $extra['comment_form']['btn_submit'] = __("Post Comment", "adforest-rest-api");
        $extra['comment_form']['btn_cancel'] = __("Cancel Comment", "adforest-rest-api");
        return $response = array('success' => true, 'data' => $data, 'message' => $message, 'extra' => $extra);
    }

}
/* Post details Start here */
add_action('rest_api_init', 'adforestAPI_hook_for_getting_post', 0);

function adforestAPI_hook_for_getting_post() {
    register_rest_route(
            'adforest/v1', '/posts/detail/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_get_post_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_get_post_get')) {

    function adforestAPI_get_post_get($request) {
        global $adforestAPI;
        $arr = array();
        
        $app_settings_rtl = isset($adforestAPI['app_settings_rtl']) && $adforestAPI['app_settings_rtl'] ? 'rtl' : 'ltr';
        $app_settings_rtl = apply_filters('AdforestAPI_app_direction', $app_settings_rtl);
        $json_data = $request->get_json_params();
        $post_id = (isset($json_data['post_id']) && $json_data['post_id'] != "" ) ? $json_data['post_id'] : '';
        $post = get_post($post_id);
        $post_author_id = $post->post_author;
        $post_id = $post->ID;
        $arr['post_id'] = $post_id;
        $arr['author_name'] = get_the_author_meta('display_name', $post_author_id);
        $arr['title'] = $post->post_title;
        $arr['date'] = get_the_date("", $post_id);
        $finalDesc = adforestAPI_youtube_url_to_iframe(do_shortcode($post->post_content));
        $wrapHTML = '<span style=" line-height:20px;" dir="'.$app_settings_rtl.'">' . wpautop($finalDesc) . '</span>';

        $arr['desc'] = ($wrapHTML);
        $arr['dir'] = ($app_settings_rtl);
        
        $list = array();
        $term_lists = wp_get_post_terms($post_id, 'category', array('fields' => 'all'));
        if (isset($term_lists) && count($term_lists) > 0)
            foreach ($term_lists as $term_list)
                $list[] = array('id' => $term_list->term_id, 'name' => $term_list->name);
        $arr['cats'] = $list;

        $tags = array();
        $tags_lists = wp_get_post_terms($post_id, 'post_tag', array('fields' => 'all'));
        if (isset($tags_lists) && count($tags_lists) > 0)
            foreach ($tags_lists as $tags_list)
                $tags[] = array('id' => $tags_list->term_id, 'name' => $tags_list->name);
        $arr['tags'] = $tags;


        $image = get_the_post_thumbnail_url($post_id, 'medium');
        if (!$image)
            $image = '';

        $arr['has_image'] = ( $image ) ? true : false;
        $arr['image'] = $image;
        $arr['comment_status'] = $post->comment_status;
        $arr['comment_count'] = $post->comment_count;

        $arr['has_comment'] = ($post->comment_count > 0 ) ? true : false;
        $comment_mesage = '';
        if ($post->comment_status == 'closed') {
            $comment_mesage = __("Comment are closed", "adforest-rest-api");
        } else {
            $comment_mesage = __("No Comment Found", "adforest-rest-api");
        }
        $arr['comment_mesage'] = $comment_mesage;

        $arr['comments'] = adforestAPI_get_post_comments($post_id, $post->comment_count);
        $data['post'] = $arr;
        $extra['page_title'] = __("Blog Details", "adforest-rest-api");
        $extra['comment_title'] = __("Comments", "adforest-rest-api");
        $extra['load_more'] = __("Load More", "adforest-rest-api");
        $extra['load_more'] = __("Load More", "adforest-rest-api");
        $extra['comment_form']['title'] = __("Post your comments here", "adforest-rest-api");
        $extra['comment_form']['textarea'] = __("Your comment here", "adforest-rest-api");
        $extra['comment_form']['btn_submit'] = __("Post Comment", "adforest-rest-api");
        $extra['comment_form']['btn_cancel'] = __("Cancel Comment", "adforest-rest-api");

        return $response = array('success' => true, 'data' => $data, 'message' => '', 'extra' => $extra);
    }

}

if (!function_exists('adforestAPI_get_post_comments')) {

    function adforestAPI_get_post_comments($post_id = '', $comments_count = '') {
        $paged = 1;
        if ($comments_count == "") {
            $comments_count = wp_count_comments($post_id);
            $total_posts = $comments_count->approved;
        } else {
            $total_posts = $comments_count;
        }
        $parent_comments = adforestAPI_parent_comment_counter($post_id);
        $posts_per_page = get_option('posts_per_page');
        $max_num_pages = ceil($parent_comments / $posts_per_page);
        $get_offset = ($paged - 1);
        $offset = $get_offset * $posts_per_page;
        $args = array(
            'number' => $posts_per_page,
            'order' => 'DESC',
            'orderby' => 'comment_ID',
            'status' => 'approve',
            'parent' => 0,
            'post_id' => $post_id,
            'offset' => $offset,
        );
        $comments = get_comments($args);
        $arr = array();
        $carray = array();

        $comments_open = comments_open($post_id);

        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                $arr['blog_id'] = $post_id;
                $arr['img'] = adforestAPI_user_dp($comment->user_id);
                $arr['comment_id'] = $comment->comment_ID;
                $arr['comment_author'] = $comment->comment_author;
                $arr['comment_content'] = $comment->comment_content;
                $arr['comment_date'] = adforestAPI_timeago($comment->comment_date);
                $arr['comment_parent'] = $comment->comment_parent;
                $arr['comment_author_id'] = $comment->user_id;
                $arr['reply_btn_text'] = __("Reply", "adforest-rest-api");

                $replies = adforestAPI_get_comment_replys($comment->comment_ID, $post_id);
                $has_childs = (isset($replies) && count($replies) > 0 ) ? true : false;
                $arr['can_reply'] = true; //( $comments_open ) ? true : false;
                $arr['has_childs'] = $has_childs;
                $arr['reply'] = $replies;

                $carray[] = $arr;
            }
        } else {
            $carray[] = __('No Comment Found', 'adforest-rest-api');
        }

        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $data['comments'] = $carray;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) $total_posts, "has_next_page" => $has_next_page);

        return $data;
    }

}

if (!function_exists('adforestAPI_get_comment_replys')) {

    function adforestAPI_get_comment_replys($comment_id = '', $post_id = '') {
        $args = array('order' => 'DESC', 'orderby' => 'comment_ID', 'status' => 'approve', 'parent' => $comment_id, 'post_id' => $post_id,);
        $rcomments = get_comments($args);
        $rarr = array();
        $rply = array();
        $comments_open = comments_open($post_id);
        if (count($rcomments) > 0) {
            foreach ($rcomments as $rcomment) {
                $rarr['blog_id'] = $post_id;
                $rarr['img'] = adforestAPI_user_dp($rcomment->user_id);
                $rarr['comment_id'] = $rcomment->comment_ID;
                $rarr['comment_author'] = $rcomment->comment_author;
                $rarr['comment_content'] = $rcomment->comment_content;
                $rarr['comment_date'] = adforestAPI_timeago($rcomment->comment_date);
                $rarr['comment_parent'] = $rcomment->comment_parent;
                $rarr['comment_author_id'] = $rcomment->user_id;
                $rarr['reply_btn_text'] = __("Reply", "adforest-rest-api");

                $rarr['can_reply'] = false;
                $rarr['has_childs'] = false;
                $rarr['reply'] = '';

                $rply[] = $rarr;
            }
        }
        return $rply;
    }

}

add_action('rest_api_init', 'adforestAPI_hook_for_getting_comments', 0);

function adforestAPI_hook_for_getting_comments() {
    register_rest_route(
            'adforest/v1', '/posts/comments/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_get_post_comments_api',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route(
            'adforest/v1', '/posts/comments/get/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_get_post_comments_api',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route(
            'adforest/v1', '/posts/comments/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_get_post_comments_submit_api',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_get_post_comments_submit_api')) {

    function adforestAPI_get_post_comments_submit_api($request) {
        $user = wp_get_current_user();
        $user_id = @$user->data->ID;
        $display_name = @$user->data->display_name;
        $user_email = @$user->data->user_email;
        $json_data = $request->get_json_params();
        $message = (isset($json_data['message'])) ? $json_data['message'] : '';
        $post_id = (isset($json_data['post_id'])) ? $json_data['post_id'] : '';
        $comment_id = (isset($json_data['comment_id'])) ? $json_data['comment_id'] : 0;

        $commentdata = array(
            'comment_post_ID' => $post_id,
            'comment_author' => $display_name,
            'comment_author_email' => $user_email,
            'comment_author_url' => '',
            'comment_content' => $message,
            'comment_type' => 'comments',
            'comment_parent' => $comment_id,
            'user_id' => $user_id,
        );

        /* Insert new comment and get the comment ID */
        add_filter('duplicate_comment_id', '__return_false');
        $comment_id = wp_new_comment($commentdata);
        $arr = array();
        if ($comment_id) {
            $arr['comments'] = adforestAPI_get_post_comments($post_id);
            $success = true;

            $status = wp_get_comment_status($comment_id);
            if ($status == "approved") {
                $message = __("Comment Posted Successfully.", "adforest-rest-api");
            } else {
                $message = __("Comment sent for approval.", "adforest-rest-api");
            }
        } else {
            $arr['comments'] = array();
            $success = false;
            $message = __("Some Error Ocuured While Posting Ad", "adforest-rest-api");
        }
        $extra['message'] = __("You are posting too fast. Please slow down.", "adforest-rest-api");
        /* $message = strip_tags($message); */
        return $response = array('success' => $success, 'data' => $arr, 'message' => $message, "extra" => $extra);
    }

}

if (!function_exists('adforestAPI_get_post_comments_api')) {

    function adforestAPI_get_post_comments_api($request) {
        $json_data = $request->get_json_params();
        $post_id = (isset($json_data['post_id'])) ? $json_data['post_id'] : '';
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $posts_per_page = get_option('posts_per_page');
        $comments_count = wp_count_comments($post_id);
        $parent_comments = adforestAPI_parent_comment_counter($post_id);
        $max_num_pages = ceil($parent_comments / $posts_per_page);
        $get_offset = ($paged - 1);
        $offset = $get_offset * $posts_per_page;
        $args = array(
            'number' => $posts_per_page,
            'order' => 'DESC',
            'orderby' => 'comment_ID',
            'status' => 'approve',
            'parent' => 0,
            'post_id' => $post_id,
            'offset' => $offset
        );

        $comments = get_comments($args);
        $arr = array();
        $carray = array();
        $comments_open = comments_open($post_id);
        if (count($comments) > 0) :
            foreach ($comments as $comment) :
                $arr['blog_id'] = $post_id;
                $arr['img'] = adforestAPI_user_dp($comment->user_id);
                $arr['comment_id'] = $comment->comment_ID;
                $arr['comment_author'] = $comment->comment_author;
                $arr['comment_content'] = $comment->comment_content;
                $arr['comment_date'] = adforestAPI_timeago($comment->comment_date);
                $arr['comment_parent'] = $comment->comment_parent;
                $arr['comment_author_id'] = $comment->user_id;
                $arr['reply_btn_text'] = __("Reply", "adforest-rest-api");
                $replies = adforestAPI_get_comment_replys($comment->comment_ID, $post_id);
                $has_childs = (isset($replies) && count($replies) > 0 ) ? true : false;
                $arr['can_reply'] = ( $comments_open ) ? true : false;
                $arr['has_childs'] = $has_childs;
                $arr['reply'] = $replies;
                $carray[] = $arr;
            endforeach;
        endif;

        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages) ? true : false;
        $data['comments'] = $carray;
        $data['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) $parent_comments, "has_next_page" => $has_next_page);
        return $response = array('success' => true, 'data' => $data, 'message' => '');
    }

}

function adforestAPI_parent_comment_counter($id) {
    global $wpdb;
    $query = "SELECT COUNT(comment_post_id) AS count FROM $wpdb->comments WHERE `comment_approved` = 1 AND `comment_post_ID` = $id AND `comment_parent` = 0";
    $parents = $wpdb->get_row($query);
    return isset($parents->count) ? $parents->count : 0;
}

/*Post details Ends here*/