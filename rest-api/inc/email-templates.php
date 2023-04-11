<?php
/* AdForest Ad Post Email Template */
if (!function_exists('adforestAPI_get_notify_on_ad_post')) {

    function adforestAPI_get_notify_on_ad_post($pid) {
        global $adforestAPI;
        if (isset($adforestAPI['sb_send_email_on_ad_post']) && $adforestAPI['sb_send_email_on_ad_post']) {
            $to = $adforestAPI['ad_post_email_value'];
            $subject = __('New Ad', 'adforest-rest-api') . '-' . get_bloginfo('name');
            $body = '<html><body><p>' . __('Got new ad', 'adforest-rest-api') . ' <a href="' . get_edit_post_link($pid) . '">' . get_the_title($pid) . '</a></p></body></html>';
            $from = get_bloginfo('name');
            if (isset($adforestAPI['sb_msg_from_on_new_ad']) && $adforestAPI['sb_msg_from_on_new_ad'] != "") {
                $from = $adforestAPI['sb_msg_from_on_new_ad'];
            }

            $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
            if (isset($adforestAPI['sb_msg_on_new_ad']) && $adforestAPI['sb_msg_on_new_ad'] != "") {

                $author_id = get_post_field('post_author', $pid);
                $user_info = get_userdata($author_id);

                $subject_keywords = array('%site_name%', '%ad_owner%', '%ad_title%');
                $subject_replaces = array(get_bloginfo('name'), $user_info->display_name, get_the_title($pid));

                $subject = str_replace($subject_keywords, $subject_replaces, $adforestAPI['sb_msg_subject_on_new_ad']);

                $msg_keywords = array('%site_name%', '%ad_owner%', '%ad_title%', '%ad_link%');
                $msg_replaces = array(get_bloginfo('name'), $user_info->display_name, get_the_title($pid), get_the_permalink($pid));

                $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_msg_on_new_ad']);
            }
            wp_mail($to, $subject, $body, $headers);
        }
    }

}


/* AdForest Ad Post Email Template */
if (!function_exists('adforestAPI_get_notify_on_ad_approval')) {

    function adforestAPI_get_notify_on_ad_approval($pid) {
        global $adforestAPI;
        $from = get_bloginfo('name');
        if (isset($adforestAPI['sb_active_ad_email_from']) && $adforestAPI['sb_active_ad_email_from'] != "") {
            $from = $adforestAPI['sb_active_ad_email_from'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($adforestAPI['sb_active_ad_email_message']) && $adforestAPI['sb_active_ad_email_message'] != "") {

            $author_id = get_post_field('post_author', $pid);
            $user_info = get_userdata($author_id);

            $subject = $adforestAPI['sb_active_ad_email_subject'];

            $msg_keywords = array('%site_name%', '%user_name%', '%ad_title%', '%ad_link%');
            $msg_replaces = array(get_bloginfo('name'), $user_info->display_name, get_the_title($pid), get_the_permalink($pid));

            $to = $user_info->user_email;
            $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_active_ad_email_message']);
            wp_mail($to, $subject, $body, $headers);
        }
    }

}


/* AdForest Message on ad */
if (!function_exists('adforestAPI_get_notify_on_ad_message')) {

    function adforestAPI_get_notify_on_ad_message($pid = '', $msg_receiver_id = '', $ad_message = '', $name = '') {
        global $adforestAPI;
        if (isset($adforestAPI['sb_send_email_on_message']) && $adforestAPI['sb_send_email_on_message']) {
            $author_obj = get_user_by('id', $msg_receiver_id);
            $to = @$author_obj->user_email;
            $subject = __('New Message', 'adforest-rest-api');
            $title = get_the_title($pid);
            $body = '<html><body><p>' . __('Got new message on ad', 'adforest-rest-api') . ' ' . $title . '</p><p>' . $ad_message . '</p></body></html>';
            $from = get_bloginfo('name');
            if (isset($adforestAPI['sb_message_from_on_new_ad']) && $adforestAPI['sb_message_from_on_new_ad'] != "") {
                $from = $adforestAPI['sb_message_from_on_new_ad'];
            }
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
            if (isset($adforestAPI['sb_message_on_new_ad']) && $adforestAPI['sb_message_on_new_ad'] != "") {
                $subject_keywords = array('%site_name%', '%ad_title%');
                $subject_replaces = array(get_bloginfo('name'), get_the_title($pid));
                $subject = str_replace($subject_keywords, $subject_replaces, $adforestAPI['sb_message_subject_on_new_ad']);
                $msg_keywords = array('%site_name%', '%ad_title%', '%ad_link%', '%message%', '%sender_name%');
                $msg_replaces = array(get_bloginfo('name'), get_the_title($pid), get_the_permalink($pid), $ad_message, $name);
                $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_message_on_new_ad']);
            }
            wp_mail($to, $subject, $body, $headers);
        }
    }

}


/* AdForest Message on ad */
if (!function_exists('adforestAPI_sb_report_ad')) {

    function adforestAPI_sb_report_ad($ad_id = '', $option = '', $comments = '', $author_id = '') {
        global $adforestAPI;

        $to = $adforestAPI['report_email'];
        $subject = __('Ad Reported', 'adforest-rest-api');
        $body = '<html><body><p>' . __('Users reported this ad, please check it. ', 'adforest-rest-api') . '<a href="' . get_the_permalink($ad_id) . '">' . get_the_title($ad_id) . '</a></p></body></html>';

        $from = get_bloginfo('name');
        if (isset($adforestAPI['sb_report_ad_from']) && $adforestAPI['sb_report_ad_from'] != "")
            $from = $adforestAPI['sb_report_ad_from'];

        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($adforestAPI['sb_report_ad_message']) && $adforestAPI['sb_report_ad_message'] != "") {
            $subject_keywords = array('%site_name%', '%ad_title%');
            $subject_replaces = array(get_bloginfo('name'), get_the_title($ad_id));
            $subject = str_replace($subject_keywords, $subject_replaces, $adforestAPI['sb_report_ad_subject']);
            $author_id = get_post_field('post_author', $ad_id);
            $user_info = get_userdata($author_id);
            $msg_keywords = array('%site_name%', '%ad_title%', '%ad_link%', '%ad_owner%', '%ad_report_option%');
            $msg_replaces = array(get_bloginfo('name'), get_the_title($ad_id), get_the_permalink($ad_id), $user_info->display_name, $option);
            $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_report_ad_message']);
        }
        wp_mail($to, $subject, $body, $headers);
    }

}


/* AdForest Message on ad */
if (!function_exists('adforestAPI_email_on_new_user')) {

    function adforestAPI_email_on_new_user($user_id, $social = '', $api_admin_email = true) {
        global $adforestAPI;

        if (isset($adforestAPI['sb_new_user_email_to_admin']) && $adforestAPI['sb_new_user_email_to_admin'] && $api_admin_email == true) {
            if (isset($adforestAPI['sb_new_user_admin_message']) && $adforestAPI['sb_new_user_admin_message'] != "" && isset($adforestAPI['sb_new_user_admin_message_from']) && $adforestAPI['sb_new_user_admin_message_from'] != "") {
                $to = get_option('admin_email');
                $subject = $adforestAPI['sb_new_user_admin_message_subject'];
                $from = $adforestAPI['sb_new_user_admin_message_from'];
                $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");

                // User info
                $user_info = get_userdata($user_id);


                $msg_keywords = array('%site_name%', '%display_name%', '%email%');
                $msg_replaces = array(get_bloginfo('name'), $user_info->display_name, $user_info->user_email);


                $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_new_user_admin_message']);
                              
                wp_mail($to, $subject, $body, $headers);              

            }
        }

        if (isset($adforestAPI['sb_new_user_email_to_user']) && $adforestAPI['sb_new_user_email_to_user']) {
            if (isset($adforestAPI['sb_new_user_message']) && $adforestAPI['sb_new_user_message'] != "" && isset($adforestAPI['sb_new_user_message_from']) && $adforestAPI['sb_new_user_message_from'] != "") {
                // User info
                $user_info = get_userdata($user_id);

                $to = $user_info->user_email;
                $subject = $adforestAPI['sb_new_user_message_subject'];
                $from = $adforestAPI['sb_new_user_message_from'];
                $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");

                $user_name = $user_info->user_email;
                

                $token = adforest_randomString(6);
                $verification_link = $token;
                if (isset($adforestAPI['sb_new_user_email_verification']) && $adforestAPI['sb_new_user_email_verification']) {
                    update_user_meta($user_id, 'sb_email_verification_token', $token);
                }
                
                 if ($social != ''){
                    $user_name .= "(Password: $social )";
                    $verification_link    =  "";
                }

                $msg_keywords = array('%site_name%', '%user_name%', '%display_name%', '%verification_link%');
                $msg_replaces = array(get_bloginfo('name'), $user_name, $user_info->display_name, $verification_link);

                $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_new_user_message']);
                wp_mail($to, $subject, $body, $headers);
            }
        }
    }

}

/* Send Email On Forgot pass */
if (!function_exists('adforestAPI_forgot_pass_email_link')) {

    function adforestAPI_forgot_pass_email_link($email = '') {
        global $adforestAPI;
        $from = get_bloginfo('name');
        if (isset($adforestAPI['sb_forgot_password_from']) && $adforestAPI['sb_forgot_password_from'] != "") {
            $from = $adforestAPI['sb_forgot_password_from'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($adforestAPI['sb_forgot_password_message']) && $adforestAPI['sb_forgot_password_message'] != "") {

            $subject_keywords = array('%site_name%');
            $subject_replaces = array(get_bloginfo('name'));

            $subject = str_replace($subject_keywords, $subject_replaces, $adforestAPI['sb_forgot_password_subject']);

            $token = adforest_randomString(50);

            $user = get_user_by('email', $email);
            $msg_keywords = array('%site_name%', '%user%', '%reset_link%');
            $reset_link = trailingslashit(get_home_url()) . '?token=' . $token . '-sb-uid-' . $user->ID;
            $msg_replaces = array(get_bloginfo('name'), $user->display_name, $reset_link);

            $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_forgot_password_message']);

            $to = $email;
            $mail = wp_mail($to, $subject, $body, $headers);
            if ($mail) {
                update_user_meta($user->ID, 'sb_password_forget_token', $token);
                $success = true;
                $message = __('Email sent', 'adforest-rest-api');
            } else {
                $success = false;
                $message = __('Email server not responding', 'adforest-rest-api');
            }
        }

        $response = array('success' => $success, 'data' => '', 'message' => $message);
        return $response;
    }

}


/* Send Email On Forgot pass */
if (!function_exists('adforestAPI_forgot_pass_email_text')) {

    function adforestAPI_forgot_pass_email_text($email = '') {
        global $adforestAPI;
        $params = array();

        if (email_exists($email) == true) {

            // lets generate our new password
            $random_password = wp_generate_password(12, false);
            $to = $email;
            $subject = __('Your new password', 'adforest-rest-api');

            $body = __('Your new password is: ', 'adforest-rest-api') . $random_password;
            $from = get_bloginfo('name');
            if (isset($adforestAPI['sb_forgot_password_from']) && $adforestAPI['sb_forgot_password_from'] != "") {
                $from = $adforestAPI['sb_forgot_password_from'];
            }
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
            if (isset($adforestAPI['sb_forgot_password_message']) && $adforestAPI['sb_forgot_password_message'] != "") {
                $subject_keywords = array('%site_name%');
                $subject_replaces = array(get_bloginfo('name'));

                $subject = str_replace($subject_keywords, $subject_replaces, $adforestAPI['sb_forgot_password_subject']);

                $user = get_user_by('email', $email);
                $msg_keywords = array('%site_name%', '%user%', '%reset_link%');
                $msg_replaces = array(get_bloginfo('name'), $user->display_name, $random_password);

                $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_forgot_password_message']);
            }
            $mail = wp_mail($to, $subject, $body, $headers);
            if ($mail) {
                // Get user data by field and data, other field are ID, slug, slug and login
                $update_user = wp_update_user(array(
                    'ID' => $user->ID,
                    'user_pass' => $random_password
                        )
                );
                $success = true;
                $message = __('Email sent', 'adforest-rest-api');
            } else {
                $success = false;
                $message = __('Email server not responding', 'adforest-rest-api');
            }
        } else {
            $success = false;
            $message = __('Email is not resgistered with us.', 'adforest-rest-api');
        }

        $response = array('success' => $success, 'data' => '', 'message' => $message);
        return $response;
    }

}



/* Send Email On Forgot pass */
if (!function_exists('adforestAPI_send_email_new_bid')) {

    function adforestAPI_send_email_new_bid($sender_id, $receiver_id, $bid = '', $comments = '', $aid='') {
        global $adforestAPI;
        $receiver_info = get_userdata($receiver_id);
        $to = $receiver_info->user_email;
        $from = '';
        if (isset($adforestAPI['sb_new_bid_from']) && $adforestAPI['sb_new_bid_from'] != "") {
            $from = $adforestAPI['sb_new_bid_from'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($adforestAPI['sb_new_bid_message']) && $adforestAPI['sb_new_bid_message'] != "") {
            $subject_keywords = array('%site_name%');
            $subject_replaces = array(get_bloginfo('name'));
            $subject = str_replace($subject_keywords, $subject_replaces, $adforestAPI['sb_new_bid_subject']);
            // Bidder info
            $sender_info = get_userdata($sender_id);

            $msg_keywords = array('%site_name%', '%receiver%', '%bidder%', '%bid%', '%comments%', '%bid_link%');
            $msg_replaces = array(get_bloginfo('name'), $receiver_info->display_name, $sender_info->display_name, $bid, $comments, get_the_permalink($aid) . '#tab2default');

            $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['sb_new_bid_message']);
            wp_mail($to, $subject, $body, $headers);
        }
    }

}


/* Email on Ad rating */
if (!function_exists('adforestAPI_email_ad_rating')) {

    function adforestAPI_email_ad_rating($pid, $sender_id, $rating, $comments) {
        global $adforestAPI;
        $from = get_bloginfo('name');
        if (isset($adforestAPI['ad_rating_email_from']) && $adforestAPI['ad_rating_email_from'] != "") {
            $from = $adforestAPI['ad_rating_email_from'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($adforestAPI['ad_rating_email_message']) && $adforestAPI['ad_rating_email_message'] != "") {

            $author_id = get_post_field('post_author', $pid);
            $user_info = get_userdata($author_id);

            $subject = $adforestAPI['ad_rating_email_subject'];

            $msg_keywords = array('%site_name%', '%ad_title%', '%ad_link%', '%rating%', '%rating_comments%', '%author_name%');
            $msg_replaces = array(get_bloginfo('name'), get_the_title($pid), get_the_permalink($pid) . '#ad-rating', $rating, $comments, $user_info->display_name);

            $to = $user_info->user_email;
            $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['ad_rating_email_message']);
            $mail = wp_mail($to, $subject, $body, $headers);
            if ($mail) {
                return 'sent';
            } else {
                return 'not sent';
            }
        }
    }

}

/* Email on Ad rating reply */
if (!function_exists('adforestAPI_email_ad_rating_reply')) {

    function adforestAPI_email_ad_rating_reply($pid, $receiver_id, $reply, $rating, $rating_comments) {
        global $adforestAPI;
        $from = get_bloginfo('name');
        if (isset($adforestAPI['ad_rating_reply_email_from']) && $adforestAPI['ad_rating_reply_email_from'] != "") {
            $from = $adforestAPI['ad_rating_reply_email_from'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($adforestAPI['ad_rating_reply_email_message']) && $adforestAPI['ad_rating_reply_email_message'] != "") {

            $author_id = get_post_field('post_author', $pid);
            $user_info = get_userdata($author_id);

            $subject = $adforestAPI['ad_rating_reply_email_subject'];

            $msg_keywords = array('%site_name%', '%ad_title%', '%ad_link%', '%rating%', '%rating_comments%', '%author_name%', '%author_reply%');
            $msg_replaces = array(get_bloginfo('name'), get_the_title($pid), get_the_permalink($pid) . '#ad-rating', $rating, $rating_comments, $user_info->display_name, $reply);

            $receiver_info = get_userdata($receiver_id);
            $to = $receiver_info->user_email;
            $body = str_replace($msg_keywords, $msg_replaces, $adforestAPI['ad_rating_reply_email_message']);
            wp_mail($to, $subject, $body, $headers);
        }
    }

}

// package Expiry Notification


add_action('adforestAPI_package_expiry_notification', 'adforestAPI_package_expiry_notification_callback', 10, 2);

function adforestAPI_package_expiry_notification_callback($before_days = 0, $user_id = 0) {
    global $adforestAPI;
    $sb_pkg_name = get_user_meta($user_id, '_sb_pkg_type', true);
    $user_info = get_userdata($user_id);
    $to = $user_info->user_email;
    $subject = __('New Messages', 'redux-framework');
    $body = '<html><body><p>' . __('Got new message on ads', 'redux-framework') . ' ' . get_the_title($rej_ad_id) . '</p><p>' . $ad_reject_reason . '</p></body></html>';
    $from = get_bloginfo('name');
    if (isset($adforestAPI['sb_package_expiry_from']) && $adforestAPI['sb_package_expiry_from'] != "") {
        $from = $adforestAPI['sb_package_expiry_from'];
    }
    $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
    $subject_keywords = array('%site_name%');
    $subject_replaces = array(get_bloginfo('name'));
    $subject = str_replace($subject_keywords, $subject_replaces, $adforest_theme['sb_package_expiray_subject']);
    $msg_keywords = array('%package_subcriber%', '%site_name%', '%package_name%', '%no_of_days%');
    $msg_replaces = array($user_info->display_name, get_bloginfo('name'), $sb_pkg_name, $before_days);
    $body = str_replace($msg_keywords, $msg_replaces, $adforest_theme['sb_package_expiry_msg']);
    $body = stripcslashes($body);
    wp_mail($to, $subject, $body, $headers);
}

add_action('adforestAPI_send_email_bid_winner', 'adforestAPI_send_email_bid_winner_callback', 10, 1);

function adforestAPI_send_email_bid_winner_callback($ad_id = 0) {

    global $adforestAPI;

    if ($ad_id == 0)
        return;

    $adforest_bid_flag = get_post_meta($ad_id, 'adforest_app_bid_winner_mail_flg', true);
    $adforest_bid_flag = $adforest_bid_flag == '' ? '1' : $adforest_bid_flag;

    if ($adforest_bid_flag == '0')
        return;


    $bids_res = adforest_get_all_biddings_array($ad_id);
    $total_bids = count($bids_res);
    $max = 0;
    if ($total_bids > 0) {
        $max = max($bids_res);
    }
    $count = 1;
    if ($total_bids > 0) {
        foreach ($bids_res as $key => $val) {
            $bid_winner_neme = 'demo';
            if ($val == $max) {
                $data = explode('_', $key);
                $bid_winner_id = $data[0];
                $user_info = get_userdata($bid_winner_id);
                $bid_winner_neme = $user_info->display_name;
                $to = $user_info->user_email;
                $from = '';
                if (isset($adforestAPI['sb_new_bid_winner_from']) && $adforestAPI['sb_new_bid_winner_from'] != "") {
                    $from = $adforestAPI['sb_new_bid_winner_from'];
                }
                $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
                if (isset($adforestAPI['sb_email_to_bid_winner']) && $adforestAPI['sb_email_to_bid_winner']) {
                    if (isset($adforestAPI['sb_new_bid_winner_message']) && $adforestAPI['sb_new_bid_winner_message'] != "") {
                        $subject_keywords = array('%site_name%');
                        $subject_replaces = array(get_bloginfo('name'));
                        $subject = str_replace($subject_keywords, $subject_replaces, $adforest_theme['sb_new_bid_winner_subject']);
                        $msg_keywords = array('%site_name%', '%bid_winner_name%', '%bid_link%');
                        $msg_replaces = array(get_bloginfo('name'), $bid_winner_neme, get_the_permalink($ad_id) . '#tab2default');
                        $body = str_replace($msg_keywords, $msg_replaces, $adforest_theme['sb_new_bid_winner_message']);
                        wp_mail($to, $subject, $body, $headers);
                        update_post_meta($ad_id, 'adforest_app_bid_winner_mail_flg', '0');
                    }
                }
            }
            break;
        }
    }
}
