<?php

add_action('plugins_loaded', 'adforestAPI_add_whizchat_filter');
function adforestAPI_add_whizchat_filter() {
    /* filter return a chat list of a user */
    add_filter('whizzChatAPI_load_chat_list', 'whizzChatAPI_load_chat_list_data', 10, 1);
    /* filter return a complete chat box against a chat id */
    add_filter('whizzChatAPI_load_chat_chatbox', 'whizzChatAPI_load_chat_chatbox_fun', 10, 2);
    /* filter return the messages of a single chat box */
    add_filter('whizzAPI_filter_chat_box_content', 'whizzChatAPI_filter_chat_box_content_fun', 10, 2);
    /* get type text messages */
    add_filter('whizzChatAPI_list_chat_messages_text', 'whizzChat_list_chat_messages_text_fun', 10, 3);
}
add_action('rest_api_init', 'adforestAPI_whizchat_send_message', 0);
function adforestAPI_whizchat_send_message() {
    /* get chat list  */
    register_rest_route('adforest/v1', '/whizchat/whizchatApi_get_chat_list/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_whizchatApi_get_chat_list_fun',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    /* open single chat */
    register_rest_route('adforest/v1', '/whizchat/whizchatApi_get_chat_box/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_whizchatApi_get_chat_box_fun',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    /* send message or start a new chat */
    register_rest_route('adforest/v1', '/whizchat/whizchatApi_send_message/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_whizchatApi_send_message',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}
function adforestAPI_whizchatApi_get_chat_list_fun($request) {
    $chat_lists = whizzChat_chat_list();
    $chat_list_data = apply_filters('whizzChatAPI_load_chat_list', $chat_lists);
    $data = array();
    $data['chat_list'] = $chat_list_data;

    if (!empty($chat_list_data)) {
        $response = array('success' => true, 'data' => $data, 'message' => "");
    } else {
        $response = array('success' => false, 'data' => $data, 'message' => esc_html__('No chat found', 'adforest-rest-api'));
    }
    return $response;
}

/* get single chat box */
if (!function_exists('adforestAPI_whizchatApi_get_chat_box_fun')) {

    function adforestAPI_whizchatApi_get_chat_box_fun($request) {
        global $whizzChat_options;
        $json_data = $request->get_json_params();
        $chat_id = isset($json_data['chat_id']) ? $json_data['chat_id'] : "";
        $dat = array();
        $image_data = whizzChat_upload_info("image");
        $file_data = whizzChat_upload_info("file");
        $attachment_format_string = "pdf,txt";
        if (isset($file_data['format']) && !empty($file_data['format'])) {
            $attachment_format_string = "";
            foreach ($file_data['format'] as $attachment_format) {
                $attachment_format_string .= $attachment_format . ", ";
            }
        }
        $message_setting = array();
        $message_setting['image_format'] = isset($image_data['format']) ? $image_data['format'] : array();
        $message_setting['image_size'] = isset($image_data['size']) ? $image_data['size'] : "200000";
        $message_setting['image_allow'] = isset($image_data['is_allow']) ? $image_data['is_allow'] . "" : "1";
        $message_setting['location_allow'] = isset($whizzChat_options['whizzChat-allow-location']) ? $whizzChat_options['whizzChat-allow-location'] . "" : "0";
        $message_setting['file_format'] = isset($file_data['format']) ? $file_data['format'] : array();
        $message_setting['file_size'] = isset($file_data['size']) ? $file_data['size'] : "200000";
        $message_setting['file_allow'] = isset($file_data['is_allow']) ? $file_data['is_allow'] . "" : "1";
        $img_size_txt = esc_html__("Image size limit is", 'adforest-rest-api');
        $message_setting['image_limit_txt'] = $img_size_txt . " " . $image_data['size'];
        $doc_size_txt = esc_html__("Documents size limit is", 'adforest-rest-api');
        $message_setting['doc_limit_txt'] = $doc_size_txt . "  " . $file_data['size'];
        $doc_format_txt = esc_html__("Allowed formats are", 'adforest-rest-api');
        $message_setting['doc_format_txt'] = $doc_format_txt . "  " . $attachment_format_string;
        $message_setting['upload_image'] = esc_html__("Upload Images", 'adforest-rest-api');
        $message_setting['upload_doc'] = esc_html__("Upload Document", 'adforest-rest-api');
        $message_setting['upload_loc'] = esc_html__("Send Location", 'adforest-rest-api');
        if ($chat_id != "") {
            global $wpdb;
            global $whizz_tbl_sessions;
            $user_id = get_current_user_id();
            if ($user_id) {
                $query = "SELECT * FROM $whizz_tbl_sessions WHERE `id` = %s AND (`rel` = %s OR `session` = %s) LIMIT 1";
                $chats = $wpdb->get_results($wpdb->prepare($query, $chat_id, $user_id, $user_id));
                $value = array();
                if (isset($chats) && count($chats) > 0) {
                    foreach ($chats as $chat) {
                        if (isset($chat->id)) {
                            $value[] = array(
                                "id" => $chat->id,
                                "name" => $chat->name,
                                "email" => $chat->email,
                                "chat_status" => $chat->status,
                                "post_id" => $chat->chat_box_id,
                                "post_author_id" => $chat->rel,
                                "session_id" => $chat->session,
                                "start_time" => $chat->timestamp,
                                "last_active_time" => $chat->last_active_timestamp,
                                "chat_box_status" => $chat->chat_box_status,
                                "receiver_open" => $chat->chatbox_receiver_open,
                                "sender_open" => $chat->chatbox_sender_open,
                                "message_for" => $chat->message_for,
                                "message_count" => $chat->message_count
                            );
                            $current_session_id = $session_id = whizzChat::session_id();
                            $user_type = ($current_session_id == $chat->session) ? 'sender' : 'receiver';
                            $type_args = array(
                                "chat_id" => $chat->id,
                                "user_type" => $user_type,
                                "last_chat_index" => 1,
                            );
                        }
                    }
                }
                $box = apply_filters('whizzChatAPI_load_chat_chatbox', $value, false);
                $response = array('success' => true, 'data' => $box, 'message' => esc_html__('', 'adforest-rest-api'), 'extra' => $message_setting);
                return $response;
            }
        } else {
            $response = array('success' => false, 'data' => array(), 'message' => esc_html__('No chat Found', 'adforest-rest-api'), 'extra' => $message_setting);
            return $response;
        }
    }
}
/**   Get chat list data   * */
if (!function_exists('whizzChatAPI_load_chat_list_data')) {
    function whizzChatAPI_load_chat_list_data($chat_lists) {
        $session_id = whizzChat::session_id();
        $chat_list_data = array();
        foreach ($chat_lists as $chat_list) {
            if (isset($chat_list) && $chat_list['id'] > 0) {
                $chat_id = ($chat_list["id"]);
                $message_count = isset($chat_list['message_count']) ? $chat_list['message_count'] : "";
                $message_for = isset($chat_list['message_for']) ? $chat_list['message_for'] : "";
                $alert_msg_class = ($session_id == $message_for && $message_count > 0) ? "chatlist-message-alert" : "";
                $size = array(150, 150);
                $url = get_the_post_thumbnail_url($chat_list["post_id"], $size);
                if ($url == "") {
                    $url = plugin_dir_url('/') . 'whizz-chat/assets/images/no-list-img.jpg';
                }
                $post_title = get_the_title($chat_list["post_id"]);
                $author_id = get_post_field('post_author', $chat_list['post_id']);
                if ($user_id_ses == $chat_list['session_id']) {
                    $display_name = get_the_author_meta('display_name', $author_id);
                } else {
                    $display_name = $chat_list['name'];
                }
                if ($user_id_ses != $chat_list['post_author_id']) {

                    $display_name = get_the_author_meta('display_name', $chat_list['post_author_id']);
                }
                $last_active_time = whizzChat::whizzChat_time_ago($chat_list["last_active_time"]);
                $chat_list_data[] = array(
                    "post_title" => $post_title,
                    "receiver_name" => $display_name,
                    "image_url" => $url,
                    "chat_id" => $chat_id,
                    "new_message" => $alert_msg_class,
                    "message_count" => $message_count,
                    "message_for" => $message_for,
                    "last_active_time" => $last_active_time
                );
            }
        }
        return $chat_list_data;
    }
}
/* get chat box */
function whizzChatAPI_load_chat_chatbox_fun($chat_lists = array(), $content_html = false) {
    global $whizzChat_options;
    $whizzChat_options = get_option('whizz-chat-options');
    $chat_box_data = array();
    $session_id = whizzChat::session_id();
    $current_user = get_current_user_id();
    if (count($chat_lists) > 0) {
        foreach ($chat_lists as $chat_list) {
            $post_id = $chat_list["post_id"];
            $chat_id = ($chat_list["id"]);
            $author_id = get_post_field('post_author', $post_id);
            $author_id = apply_filters('whizz_chat_author_rel_id', $author_id);
            $messages = array();
            if ($chat_list['session_id'] != "") {
                $blocked_status = whizzChat_is_user_blocked($chat_id, true);
                $messages = apply_filters('whizzAPI_filter_chat_box_content', $chat_list, $blocked_status);
            }
            $real_com_id = $chat_list['session_id'];
            if ($session_id == $author_id) {
                $real_com_id = $chat_list['session_id'];
            } else {
                $real_com_id = $author_id;
            }
            $live_room_data = md5($_SERVER['HTTP_HOST']) . '_whizchat' . $chat_id . '';
            if ($author_id != $current_user) {
                $display_name = get_the_author_meta('display_name', $author_id);
            } else {
                $display_name = $chat_list['name'];
            }
            $chat_box_data = array(
                "id" => $chat_id,
                "post-id" => $post_id,
                "live_room_data" => $live_room_data,
                "author-id" => $author_id,
                "communication_id" => $real_com_id,
                "chat-id" => $chat_id,
                "user_name" => $display_name,
                "post_title" => get_the_title($post_id),
                "sender_id" => get_current_user_id() . "",
                "blocked_status" => $blocked_status,
                "chat" => $messages,
            );
        }
    }
    return $chat_box_data;
}
/*  single chat box messages */
function whizzChatAPI_filter_chat_box_content_fun($user_data, $box_content = array()) {
    global $whizzChat_options;
    $whizzChat_options = get_option('whizz-chat-options');
    $chat_html = $seen_at = '';
    $last_message = array();
    if ($user_data['id'] > 0) {
        $current_user = get_current_user_id();
        $messages = apply_filters('whizzChatAPI_list_chat_messages', $user_data, true);
        return $messages;
          
    }
}
/* start chat or send messges */
if (!function_exists('adforestAPI_whizchatApi_send_message')) {
    function adforestAPI_whizchatApi_send_message($request) {
        global $wpdb;
        global $whizz_tbl_sessions;
        global $whizz_tblname_chat_message;
        global $whizz_tblname_chat_ratings;
        global $whizz_tblname_offline_msgs;
        //$get_parms = $request->get_json_params();
        //$files_parms = $request->get_file_params();
        $json_data = $request->get_json_params();
        $get_parms = $request->get_params();
        $files_parms = $request->get_file_params();
        $session_id = get_current_user_id() . "";
        $msg = (isset($get_parms['msg'])) ? $get_parms['msg'] : '';
        $session = (isset($get_parms['session'])) ? $get_parms['session'] : '';
        $post_id = (isset($get_parms['post_id'])) ? $get_parms['post_id'] : '';
        $author_id = get_post_field('post_author', $post_id);
        $comm_id = (isset($get_parms['comm_id'])) ? $get_parms['comm_id'] : '';
        $chat_id = (isset($get_parms['chat_id'])) ? $get_parms['chat_id'] : '';
        $message_ids = (isset($get_parms['message_ids'])) ? $get_parms['message_ids'] : '';
        $current_user = get_current_user_id();
        $sender_name = get_the_author_meta('display_name', $current_user);
        $sender_email = get_the_author_meta('email', $current_user);
        $query = "SELECT * FROM $whizz_tbl_sessions WHERE `id` = %s LIMIT 1";
        $results = $wpdb->get_results($wpdb->prepare($query, $chat_id));
        $success = true;
        if (isset($results) && count($results) == 0) {
            $server_token = $rest_token = get_option("whizz_api_secret_token");
            $data_array = array();
            $name = $data_array['name'] = $sender_name;
            $email = $data_array['email'] = $sender_email;
            $data_array['url'] = $url;
            $data_array['session'] = $current_user;
            $data_array['cid'] = "";
            $data_array['server_token'] = $server_token;
            $data_array['chat_box_id'] = $post_id;
            $data_array['sender_id'] = whizzChat::user_data('id');
            $data_array['comm_id'] = $author_id;
            $cid = apply_filters('whizzChat_register_user_and_session', $data_array);
            $query = "SELECT * FROM $whizz_tbl_sessions WHERE `id` = %s LIMIT 1";
            $results = $wpdb->get_results($wpdb->prepare($query, $cid));
            $cid = $results[0]->id;
            $email = $results[0]->email;
            $rel = $results[0]->rel;
            $session = $results[0]->session;
            $is_rel = $current_user;
            $message_for = $results[0]->message_for;
            $message_count = $results[0]->message_count;
        } else {
            $cid = $results[0]->id;
            $name = whizzChat::user_data('name');
            $email = $results[0]->email;
            $rel = $results[0]->rel;
            $session = $results[0]->session;
            $is_rel = ($current_user) ? $current_user : $session_id;
            $message_for = $results[0]->message_for;
            $message_count = $results[0]->message_count;
        }
        $chat_id = (isset($results[0]->id)) ? $results[0]->id : '';
        $rel_id = (isset($results[0]->rel)) ? $results[0]->rel : '';
        $sessionId = (isset($results[0]->session)) ? $results[0]->session : '';
        $sender_id = ($sessionId != $session_id) ? $sessionId : $rel_id;
        $type_args = array(
            "chat_id" => $cid, /* chat  session id */
            "receiver_id" => ( $session_id != $session ) ? $session : $rel_id, /* other user id who will receive message */
            "chat_box_id" => $post_id, /* post id */
            "message_for" => $message_for, /* old user id in message_for column */
            "message_count" => $message_count /* message count from message_count column */,
            "current_user" => $session_id, /// current user session id /
            "is_update" => true,
        );
        $msgType = isset($get_parms['message_type']) ? $get_parms['message_type'] : 'text';
        $status = 1;
        do_action('whizzChat_new_message_and_count', $type_args, $status);
        $message_type = $msgType;
        $attachments = '';
        if ($cid) {
            $extra_data = array();
            $extra_data['success'] = true;
            $extra_data['message_type'] = $msgType;
            $extra_data['attachments'] = '';
            if (isset($files_parms) && $msgType != "text" && $msgType != "map") {
                if ($get_parms['message_type'] == 'voice') {
                    $extra_data = apply_filters("whizzChat_send_sound_message_filter", $request, $cid);
                } else {
                    $extra_data = apply_filters("whizzChatAPI_send_message_attachment", $request, $cid);
                }
            }
            if ($extra_data['success'] == true) {
                $message_type = isset($extra_data['message_type']) ? $extra_data['message_type'] : $msgType;
                $attachments = isset($extra_data['attachments']) ? $extra_data['attachments'] : '';
                $is_go = true;
                if ($message_type == 'text' && $msg == "") {
                    $is_go = false;
                } else if ($message_type == 'map') {
                    if (isset($get_parms['latitude']) && isset($get_parms['longitude'])) {

                        $array = ["latitude" => $get_parms['latitude'], "longitude" => $get_parms['longitude']];

                        $attachments = json_encode($array);
                    }
                }
                $blocked_status = whizzChat_is_user_blocked($cid, true);
                if ($blocked_status['is_blocked'] != true && $is_go == true) {
                    $author_id = apply_filters('whizz_chat_author_rel_id', $author_id); // in admin only case 
                    $id = $wpdb->insert($whizz_tblname_chat_message, array('session_id' => $cid, 'timestamp' => current_time('mysql'), 'fromname' => $name, 'message' => $msg, 'status' => 0, 'rel' => $is_rel, 'author_id' => $author_id, 'post_id' => $post_id, 'message_type' => $message_type, 'attachments' => $attachments), array('%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
                    );
                    $message = esc_html__("Message sent.", "whizz-chat");
                    $success = true;
                } else {
                    $message = esc_html__("User has blocked you. You can not send the message until user unblock you.", "whizz-chat");
                    $success = false;
                }
            } else {
                $message = isset($extra_data['message']) ? $extra_data['message'] : "";
                $success = false;
            }
            $response = array('success' => $success, 'data' => array(), 'message' => $message);
            return $response;
        }
    }

}

if (!function_exists('whizzChat_list_chat_messages_text_fun')) {

    function whizzChat_list_chat_messages_text_fun() {
        
    }

}
/* upload images or file in case of api */
add_filter('whizzChatAPI_send_message_attachment', 'whizzChatAPI_send_message_attachment_func', 10, 3);
if (!function_exists('whizzChatAPI_send_message_attachment_func')) {
    function whizzChatAPI_send_message_attachment_func($request, $cid) {
        $message = "";
        $json_data = $request->get_json_params();
        $get_parms = $request->get_params();
        $files_parms = $request->get_file_params();
        $attachment_ids = array();
        $message = '';
        $success = false;
        $message_type = $get_parms['message_type'];
        $attachment_ids = array();
        if (isset($_FILES) && !empty($_FILES) && ADFOREST_API_REQUEST_FROM != 'ios') {
            $attachments_files = array();
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $files = $_FILES["chat_media"];
            $upload_type = (isset($get_parms['upload_type'])) ? $get_parms['upload_type'] : '';
            $upload_data = whizzChat_upload_info($upload_type);
            if (!empty($files)) {
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
                        $upload_overrides = array('test_form', false);
                        foreach ($_FILES as $file => $array) {
                            // Check uploaded size
                            $attach_id = media_handle_upload($file, -1);
                            if ($attach_id && !isset($attach_id['error'])) {
                                /*                                 * ***** Assign image to ad *********** */
                                $attachment_ids[] = $attach_id;
                            }
                        }
                    }
                }
            }
            $success = true;
        }

        if (isset($_FILES) && count($_FILES) > 0 && ADFOREST_API_REQUEST_FROM == 'ios') {
            global $wpdb;
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $session = (isset($get_parms['session'])) ? $get_parms['session'] : '';
            $chat_id = (isset($get_parms['chat_id'])) ? $get_parms['chat_id'] : 0;
            $post_id = (isset($get_parms['post_id'])) ? $get_parms['post_id'] : '';
            $upload_type = (isset($get_parms['upload_type'])) ? $get_parms['upload_type'] : '';
            $upload_data = whizzChat_upload_info($upload_type);
            foreach ($_FILES as $key => $val) {
                $uploadedfile = $_FILES["$key"];
                $uploaded_file_type = $uploadedfile['type'];
                $uploaded_file_size = $uploadedfile['size'];
                $uploaded_file_name = $uploadedfile['name'];
                $uploaded_file_size = $uploaded_file_size / 1000;
                if (strpos($uploaded_file_type, 'image') !== false && $upload_type == 'image') {
                    $message_type = 'image';
                }
                if ($upload_type == 'file') {
                    $message_type = 'file';
                }
                $fileName = end(explode(".", $uploaded_file_name));
                if ($uploaded_file_size > $upload_data['size']) {
                    $success = false;
                    $message = esc_html__("Max allowed size is", 'whizz-chat') . " " . $upload_data['size'];
                } else if (!in_array($fileName, $upload_data['format'])) {
                    $success = false;
                    $message = esc_html__("Invalid format uploaded.", 'whizz-chat');
                }
                $upload_overrides = array('test_form' => false);
                $request_from = adforestAPI_getSpecific_headerVal('Adforest-Request-From');
                if ($request_from == 'ios') {
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                    if ($movefile && !isset($movefile['error'])) {
                        /*                         * ***** Assign image to ad ********** */
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
                            'post_status' => 'inherit'
                        );
                        // Insert the attachment.
                        $attach_id = wp_insert_attachment($attachment, $absolute_file, $post_id);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $absolute_file);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $attachment_ids[] = $attach_id;
                    }
                }
                $success = true;
            }
        }
        $attachment_ids = json_encode($attachment_ids);
        return array("success" => $success, "attachments" => $attachment_ids, "message_type" => $message_type, "message" => $message);
    }

}

add_filter('whizzChatAPI_list_chat_messages', 'whizzChatAPI_list_chat_messages_func', 10, 2);
if (!function_exists('whizzChatAPI_list_chat_messages_func')) {
    function whizzChatAPI_list_chat_messages_func($data_array = array(), $from_app = false) {
        global $wpdb;
        global $whizz_tblname_chat_message;
        $session_id = $data_array['id'];
        $ad_id = $data_array['post_id'];
        $current_user = get_current_user_id();
        $load_messages = '';
        $limit = 6;
        if ($from_app) {
            $load_messages = "";
            $limit = "500";
        }
        $query = "SELECT * FROM $whizz_tblname_chat_message WHERE ( session_id = '" . $session_id . "' ) AND post_id = '" . $ad_id . "' " . $load_messages . " ORDER BY ID DESC LIMIT $limit";
        $chats = $wpdb->get_results($query);
        $chat_messages = array();
        $chats = array_reverse($chats, true);
        if (isset($chats) && count($chats) > 0) {
            foreach ($chats as $key => $value) {
                $is_reply = '';
                if ($current_user > 0) {
                    if ($value->rel != $current_user) {
                        $is_reply = 'message-partner';
                    }
                } else {
                    if ($value->rel != whizzChat::session_id()) {
                        $is_reply = 'message-partner';
                    }
                }

                if ($is_reply == '') {
                    $is_reply = 'message-sender-box';
                }
                $images_url = array();
                $all_attachemnts = $value->attachments;
                $file_urls = array();
                $latitude = "";
                $longitude = "";
                if ($value->message_type == 'image') {
                    if (isset($all_attachemnts)) {
                        $attachments = json_decode($all_attachemnts, true);
                        $img_count = 0;
                        if (isset($attachments) && count($attachments) > 0) {
                            foreach ($attachments as $attachment) {
                                $image = whizzChat_get_image_size_links($attachment);
                                if (isset($image['full']) && $image['full'] != "") {
                                    $images_url[] = $image['full'];
                                }
                            }
                        }
                    }
                } else if ($value->message_type == 'file') {
                    if (isset($all_attachemnts)) {
                        $attachments = json_decode($all_attachemnts, true);
                        foreach ($attachments as $attachment) {
                            $file_urls[] = wp_get_attachment_url($attachment);
                        }
                    }
                } else if ($value->message_type == 'map') {

                    $latlongs = ( json_decode($all_attachemnts, true));
                    if (isset($latlongs['latitude']) && isset($latlongs['longitude'])) {
                        $latitude = $latlongs['latitude'] . "";
                        $longitude = $latlongs['longitude'] . "";
                    }
                }
                $chat_messages[] = array(
                    "chat_message_id" => $value->id,
                    "chat_sender_id" => $value->session_id,
                    "chat_sender_name" => $value->fromname,
                    "msg" => $value->message,
                    "chat_post_id" => $value->post_id,
                    "chat_post_author" => $value->author_id,
                    "is_reply" => $is_reply,
                    "rel" => $value->rel,
                    "message_type" => $value->message_type,
                    "attachments" => $value->attachments,
                    "seen_at" => $value->seen_at,
                    "time_chat" => whizzChat::whizzchat_time_ago($value->timestamp),
                    "image_url" => $images_url,
                    "file_url" => $file_urls,
                    "longitude" => $longitude,
                    "latitude" => $latitude
                );
            }
        }
        return $chat_messages;
    }
}