<?php /** * Modify url base from wp-json to 'api' */
/* Ads Loop Starts */

if (!function_exists('adforestAPI_add_new_image_size'))
{
    function adforestAPI_add_new_image_size() {
        add_theme_support('post-thumbnails', array('post'));
        add_image_size('adforest-andriod-profile', 450, 450, true);
        add_image_size('adforest-single-post', 760, 410, true);
        add_image_size('adforest-category', 400, 300, true);
        add_image_size('adforest-single-small', 80, 80, true);
        add_image_size('adforest-ad-thumb', 120, 63, true);
        add_image_size('adforest-ad-related', 313, 234, true);
        add_image_size('adforest-user-profile', 300, 300, true);
        //add_image_size( 'adforest-app-thumb', 230, 230, true );
        add_image_size('adforest-app-thumb', 400, 250, true);
        add_image_size('adforest-app-full', 700, 400, true);
        add_image_size('adforest-ads-medium', 169, 127, true);
        add_image_size('adforest-location-large', 370, 560, true);
        add_image_size('adforest-location-wide', 750, 270, true);
        add_image_size('adforest-location-grid', 360, 252, true);
        add_image_size('adforest-ad-small', 94, 102, true);
        add_image_size('adforest-ad-small-2', 180, 170, true);
        add_image_size('adforest-shop-book', 90, 147, true);


    }
}

add_filter('pre_option_thumbnail_crop', '__return_zero');
if (!function_exists('adforestAPI_convert_uniText')) {

    function adforestAPI_convert_uniText($string = '') {
        $string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $string);
        return html_entity_decode($string, ENT_COMPAT, 'UTF-8');
    }

}
if (!function_exists('adforestAPI_getReduxValue')) {

    function adforestAPI_getReduxValue($param1 = '', $param2 = '', $vaidate = false) {
        global $adforestAPI;
        $data = '';
        if ($param1 != "") {
            $data = $adforestAPI["$param1"];
        }
        if ($param1 != "" && $param2 != "") {
            $data = $adforestAPI["$param1"]["$param2"];
        }
        if ($vaidate == true) {
            $data = (isset($data) && $data != "") ? 1 : 0;
        }
        return $data;
    }

}

if (!function_exists('adforestAPI_appLogo')) {

    function adforestAPI_appLogo() {
        global $adforestAPI;
        $defaultLogo = ADFOREST_API_PLUGIN_URL . "images/logo.png";
        $app_logo = (isset($adforestAPI['app_logo'])) ? $adforestAPI['app_logo']['url'] : $defaultLogo;
        return $app_logo;
    }

}

if (!function_exists('adforestAPI_get_authors_notIn_list')) {

    function adforestAPI_get_authors_notIn_list($user_id = '') {
        global $adforestAPI;
        $allow_block = (isset($adforestAPI['sb_user_allow_block']) && $adforestAPI['sb_user_allow_block']) ? true : false;
        $author_not_in = array();
        if ($allow_block) {
            $get_current_user_id = ($user_id != "") ? $user_id : get_current_user_id();
            if ($get_current_user_id) {
                $blocked = get_user_meta($get_current_user_id, '_sb_adforest_block_users', true);
                if (isset($blocked) && count((array) $blocked) > 0) {
                    $author_not_in = $blocked;
                }
            }
        }
        return $author_not_in;
    }

}

if (!function_exists('adforestAPI_adsLoop')) {

    function adforestAPI_adsLoop($args, $userid = '', $is_profile = false, $is_fav = false, $is_pagination = false) {
        $adsArr = array();

        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $ad_id = get_the_ID();
                $postAuthor = get_the_author_meta('ID');
                if ($is_fav == false) {
                    if ($userid != "" && $postAuthor != $userid)
                        continue;
                }
                /* Get Categories */
                $cats = adforestAPI_get_ad_terms($ad_id, 'ad_cats');
                $cats_name = adforestAPI_get_ad_terms_names($ad_id, 'ad_cats');
                /* Get Image */
                $thumb_img = '';
                $thumb_img = adforestAPI_get_ad_image($ad_id, 1, 'thumb');
                /* Strip tags and limit ad description */
                $words = wp_trim_words(strip_tags(get_the_content()), 12, '...');
                $location = adforestAPI_get_adAddress($ad_id);
                $price = get_post_meta($ad_id, "_adforest_ad_price", true);
                $ad_count = get_post_meta($ad_id, "sb_post_views_count", true);
                $priceFinal = adforestAPI_get_price($price, $ad_id);
                $ad_status = adforestAPI_adStatus($ad_id);

                $adsArr[] = array(
                    "ad_author_id" => $postAuthor,
                    "ad_id" => $ad_id,
                    "ad_date" => get_the_date("", $ad_id),
                    "ad_title" => adforestAPI_convert_uniText(get_the_title()),
                    "ad_desc" => $words,
                    "ad_status" => $ad_status,
                    "ad_cats_name" => $cats_name,
                    "ad_cats" => $cats,
                    "ad_images" => $thumb_img,
                    "ad_location" => $location,
                    "ad_price" => $priceFinal,
                    "ad_views" => $ad_count,
                    "ad_video" => adforestAPI_get_adVideo($ad_id),
                    "ad_timer" => adforestAPI_get_adTimer($ad_id),
                    "ad_saved" => array(
                        "is_saved" => 0,
                        "text" => __("Save Ad", "adforest-rest-api")
                    ),
                );
            }
            wp_reset_postdata();
        }
        if ($is_pagination == false) {
            return $adsArr;
        } else {
            return array(
                "ads" => $adsArr,
                "found_posts" => $the_query->found_posts,
                "max_num_pages" => $the_query->max_num_pages
            );
        }
    }

}

if (!function_exists('adforestAPI_get_adTimer')) {

    function adforestAPI_get_adTimer($ad_id = '') {
        global $adforestAPI;

        $ad_bidding_time = get_post_meta($ad_id, '_adforest_ad_bidding_date', true);
        $myData = strtotime($ad_bidding_time);
        $current_data0 = get_gmt_from_date('UTC', $format = 'Y-m-d H:i:s');
        $current_data = strtotime($current_data0);
        $differenceInSeconds = $myData - $current_data;
        $is_show = false;

        if (isset($adforestAPI['bidding_timer']) && $adforestAPI['bidding_timer']) {
            $is_show = true;
        }
        if (isset($adforestAPI['sb_enable_comments_offer']) && $adforestAPI['sb_enable_comments_offer'] == false) {
            $is_show = false;
        }

        $timer['timer_strings'] = array(
            "days" => __("D", "adforest-rest-api"),
            "hurs" => __("H", "adforest-rest-api"),
            "mins" => __("M", "adforest-rest-api"),
            "secs" => __("S", "adforest-rest-api")
        );

        if ($myData <= $current_data || $ad_bidding_time == "") {
            $is_show = false;
            $timer['is_show'] = $is_show;
            $timer['timer'] = ''; /* Mili-seconds */
            $timer['timer_time'] = '';
            $timer['timer_server_time'] = $current_data0;
        } else {
            $timer['is_show'] = $is_show;
            $timer['timer'] = convert_seconds($differenceInSeconds); /* Mili-seconds */
            $timer['timer_time'] = $ad_bidding_time;
            $timer['timer_server_time'] = $current_data0;
        }
        return $timer;
    }

}

function convert_seconds($seconds) {
    $dt1 = new DateTime("@0");
    $dt2 = new DateTime("@$seconds");
    $final_date = $dt1->diff($dt2)->format("%a,%h,%i,%s");
    return explode(",", $final_date);
}

/* Ads Loop Ends */
/* Ads Statuses Starts */
if (!function_exists('adforestAPI_adStatus')) {

    function adforestAPI_adStatus($ad_id = '') {

        $isFretured = get_post_meta($ad_id, "_adforest_is_feature", true);
        $ad_status = get_post_meta($ad_id, "_adforest_ad_status_", true);
        $feature_text = ($isFretured == 1) ? __("Featured", "adforest-rest-api") : '';
        $status_text = '';
        if ($ad_status == 'active') {
            $status_text = __("Active", "adforest-rest-api");
        } else if ($ad_status == 'expired') {
            $status_text = __("Expired", "adforest-rest-api");
        } else if ($ad_status == 'sold') {
            $status_text = __("Sold", "adforest-rest-api");
        }
        $ad_status = array(
            "status" => $ad_status,
            "status_text" => $status_text,
            "featured_type" => $isFretured,
            "featured_type_text" => $feature_text,
        );
        return $ad_status;
    }

}

/* Related Ads Starts */
if (!function_exists('adforestApi_related_ads')) {

    function adforestApi_related_ads($ad_id = '', $limit = 5) {
        $cats = wp_get_post_terms($ad_id, 'ad_cats');
        $categories = array();
        foreach ($cats as $cat)
            $categories[] = $cat->term_id;

        $loc_args = '';
        $loc_args = apply_filters('adforestAPI_site_location_args', $loc_args, 'search');

        $args = array(
            'post_type' => 'ad_post',
            'posts_per_page' => $limit,
            'order' => 'DESC',
            'orderby' => 'date',
            'post_status' => 'publish',
            'post__not_in' => array(
                $ad_id
            ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'ad_cats',
                    'field' => 'id',
                    'terms' => $categories,
                    'operator' => 'IN'
                ),
                $loc_args,
            )
        );
        $args = apply_filters('AdforestAPI_wpml_show_all_posts', $args);
        return adforestAPI_adsLoop($args);
    }
}
/* Related Ads Ends */

/* Category Specific Ads Starts */
if (!function_exists('adforestApi_catSpecific_ads')) {

    function adforestApi_catSpecific_ads($cat_id = '', $limit = 5, $check_login = true) {
        $is_active = array(
            'key' => '_adforest_ad_status_',
            'value' => 'active',
            'compare' => '=',
        );
        $author_not_in = adforestAPI_get_authors_notIn_list();
        $categories = array($cat_id);

        $loc_args = '';
        $loc_args = apply_filters('adforestAPI_site_location_args', $loc_args, 'search');

        if ($check_login) {
            $args = array(
                'post_type' => 'ad_post',
                'post_status' => 'publish',
                'posts_per_page' => $limit,
                'order' => 'DESC',
                'orderby' => 'date',
                'author__not_in' => $author_not_in,
                /* 'post__not_in'	=> array( $ad_id ), */
                'meta_query' => array($is_active),
                'tax_query' => array(
                    array(
                        'taxonomy' => 'ad_cats',
                        'field' => 'id',
                        'terms' => $categories,
                        'operator' => 'IN'
                    ),
                    $loc_args
                )
            );
        } else {
            $args = array(
                'post_type' => 'ad_post',
                'posts_per_page' => $limit,
                'post_status' => 'publish',
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_query' => array($is_active),
                'tax_query' => array(
                    array(
                        'taxonomy' => 'ad_cats',
                        'field' => 'id',
                        'terms' => $categories,
                        'operator' => 'IN'
                    ),
                    $loc_args,
                )
            );
        }
        $args = apply_filters('AdforestAPI_wpml_show_all_posts', $args);
        return adforestAPI_adsLoop($args);
    }

}
/* Category Specific Ads Ends */
if (!function_exists('adforestAPI_get_ad_terms')) {

    function adforestAPI_get_ad_terms($post_id = '', $term_type = 'ad_cats', $only_parent = '', $name = '') {
        $ad_trms = wp_get_object_terms($post_id, $term_type);
        $termsArr = array();
        if (count($ad_trms)) {
            foreach ($ad_trms as $ad_trm) {
                if (isset($ad_trm->term_id) && $ad_trm->term_id != "") {
                    $termsArr[] = array(
                        "id" => $ad_trm->term_id,
                        "name" => htmlspecialchars_decode($ad_trm->name, ENT_NOQUOTES),
                        "slug" => $ad_trm->slug,
                        "count" => $ad_trm->count,
                        "taxonomy" => $ad_trm->taxonomy,
                    );
                }
            }
        }
        return ($name == "") ? $termsArr : array(
            $name,
            $termsArr
        );
    }

}

if (!function_exists('adforestAPI_get_ad_terms_names')) {

    function adforestAPI_get_ad_terms_names($post_id = '', $term_type = 'ad_cats', $only_parent = '', $name = '', $separator = '>') {

        $terms = wp_get_post_terms($post_id, $term_type, array('orderby' => 'id', 'order' => 'DESC'));
        $deepestTerm = false;
        $maxDepth = - 1;
        $c = 0;
        $catNames = array();
        if (count($terms) > 0) {
            foreach ($terms as $term) {
                $ancestors = get_ancestors($term->term_id, $term_type);
                $termDepth = count($ancestors);
                $deepestTerm[$c] = $term->name;
                $maxDepth = $termDepth;
                $c++;
            }
            $terms = (isset($deepestTerm) && count($deepestTerm) > 0 && $term_type != 'ad_tags') ? array_reverse($deepestTerm) : $deepestTerm;

            if (count($terms) > 0) {
                foreach ($terms as $tr) {
                    $trName = htmlspecialchars_decode($tr, ENT_NOQUOTES);
                    $catNames[] = $trName;
                }
            }
        }

        $catNames = @implode(" $separator ", $catNames);
        return ($name == "") ? $catNames : array(
            $name,
            $catNames
        );
    }

}

if (!function_exists('adforestAPI_terms_seprates_by')) {

    function adforestAPI_terms_seprates_by($post_id, $taxonomy = 'ad_cats', $separator = '') {
        $terms = wp_get_post_terms($post_id, $taxonomy, array('orderby' => 'id', 'order' => 'DESC'));
        $deepestTerm = false;
        $maxDepth = - 1;
        $c = 0;
        foreach ($terms as $term) {
            $ancestors = get_ancestors($term->term_id, $taxonomy);
            $termDepth = count($ancestors);
            $deepestTerm[$c] = $term->name;
            $maxDepth = $termDepth;
            $c++;
        }
        $terms = array_reverse($deepestTerm);

        $string = '';
        if (count($terms) > 0) {
            foreach ($terms as $tr) {
                $trName = htmlspecialchars_decode($tr, ENT_NOQUOTES);
                $string .= $trName . $separator;
            }
        }
        $string = rtrim($string, $separator);
        return $string;
    }

}

if (!function_exists('adforestAPI_listing_adSize')) {

    function adforestAPI_listing_adSize($thumbnail = '', $thumbFor = '') {
        if ($thumbnail != "") {
            return $thumbnail;
        }
        global $adforestAPI;
        if ($thumbFor == 'listing') {
            $images_sizes = (isset($adforestAPI['ads_images_sizes']) && $adforestAPI['ads_images_sizes'] != "") ? $adforestAPI['ads_images_sizes'] : 'default';

            if ($images_sizes == 'default') {
                $thumbnail = 'adforest-app-thumb';
            } else if ($images_sizes == 'size2') {
                $thumbnail = 'adforest-category';
            } else if ($images_sizes == 'size3') {
                $thumbnail = 'adforest-ad-related';
            } else if ($images_sizes == 'size4') {
                $thumbnail = 'adforest-app-thumb';
            } else if ($images_sizes == 'size5') {
                $thumbnail = 'adforest-app-full';
            }
            return $thumbnail;
        }
        if ($thumbFor == 'ad_detail') {
            $images_sizes = (isset($adforestAPI['ads_images_sizes_adDetils']) && $adforestAPI['ads_images_sizes_adDetils'] != "") ? $adforestAPI['ads_images_sizes_adDetils'] : 'default';

            if ($images_sizes == 'default') {
                $thumbnail = 'adforest-app-full';
            } else if ($images_sizes == 'size2') {
                $thumbnail = 'adforest-single-post';
            }
            return $thumbnail;
        }
    }

}

if (!function_exists('adforestAPI_get_ad_image')) {

    function adforestAPI_get_ad_image($post_id = '', $numOf = '', $size = 'both', $show_default = true) {

        $media = get_attached_media('image', $post_id);
        $img_arr = array();
        if (count($media) > 0) {
            $re_order = get_post_meta($post_id, '_sb_photo_arrangement_', true);
            if ($re_order != "") {
                $media = explode(",", $re_order);
            }
            $c = 1;
            $getThumbnailListing = adforestAPI_listing_adSize('', 'listing');
            $getThumbnailAdDetail = adforestAPI_listing_adSize('', 'ad_detail');
            foreach ($media as $m) {
                $mid = (isset($m->ID)) ? $m->ID : $m;
                $img = wp_get_attachment_image_src($mid, $getThumbnailListing);
                $full_img = wp_get_attachment_image_src($mid, $getThumbnailAdDetail);
                if (isset($img[0]) && isset($full_img[0])) {
                    if ($size == 'full') {
                        $img_arr[] = array('full' => $full_img[0], "img_id" => $mid);
                    } else if ($size == 'thumb') {

                        $img_arr[] = array('thumb' => $img[0], "img_id" => $mid);
                    } else {
                        $img_arr[] = array('full' => $full_img[0], 'thumb' => $img[0], "img_id" => $mid);
                    }
                } else {
                    global $adforestAPI;
                    $default_img = ADFOREST_API_PLUGIN_URL . "images/default-img.png";
                    $default_img = (isset($adforestAPI['default_related_image'])) ? $adforestAPI['default_related_image']['url'] : $default_img;
                    $full_img = $default_img;
                    $thumb_img = $default_img;
                    $img_arr[] = array('full' => $full_img, 'thumb' => $thumb_img, "img_id" => $mid);
                }
                if ($numOf == $c)
                    break;

                $c++;
            }
        }
        else {
            /* Need to add images from backend *********** */
            global $adforestAPI;
            $default_img = ADFOREST_API_PLUGIN_URL . "images/default-img.png";
            $default_img = (isset($adforestAPI['default_related_image'])) ? $adforestAPI['default_related_image']['url'] : $default_img;

            $full_img = $default_img;
            $thumb_img = $default_img;

            if ($show_default == true) {

                if ($size == 'full') {
                    $img_arr[] = array('full' => $full_img);
                } else if ($size == 'thumb') {
                    $img_arr[] = array('thumb' => $thumb_img);
                } else {
                    $img_arr[] = array('full' => $full_img, 'thumb' => $thumb_img);
                }
            } else {
                /* $img_arr[] 	= array('full' => $full_img, 'thumb' => $thumb_img); */
            }
        }
        return $img_arr;
    }

}

if (!function_exists('adforestAPI_get_ad_image_slider')) {

    function adforestAPI_get_ad_image_slider($post_id = '') {
        $media = get_attached_media('image', $post_id);
        $img_arr = array();
        if (count($media) > 0) {
            $re_order = get_post_meta($post_id, '_sb_photo_arrangement_', true);
            if ($re_order != "") {
                $media = explode(",", $re_order);
            }
            foreach ($media as $m) {
                $mid = (isset($m->ID)) ? $m->ID : $m;
                $full_img = wp_get_attachment_image_src($mid, 'full');
                if (isset($full_img[0])) {
                    $img_arr[] = $full_img[0];
                }
            }
        }
        return $img_arr;
    }

}

if (!function_exists('adforestAPI_get_price')) {

    function adforestAPI_get_price($price = '', $ad_id = '', $ad_currency_id = '') {
        $price_type = $ad_currency = $price_typeValue = '';
        if ($ad_id != "") {
            $price_type = get_post_meta($ad_id, '_adforest_ad_price_type', true);
            $price = get_post_meta($ad_id, '_adforest_ad_price', true);
            $ad_currency = get_post_meta($ad_id, '_adforest_ad_currency', true);
            $price_typeValue = ($price_type) ? adforestAPI_adPrice_typesValue($price_type) : '';
            if ($price == "" && $price_type == "on_call") {
                $priceData = __("Price On Call", 'adforest-rest-api');
                return array("price" => $priceData, "price_type" => $price_typeValue);
            }
            if ($price == "" && $price_type == "free") {
                $priceData = __("Free", 'adforest-rest-api');
                return array("price" => $priceData, "price_type" => $price_typeValue);
            }

            if ($price == "" || $price_type == "no_price") {
                $priceData = '';
                return array("price" => $priceData, "price_type" => $price_typeValue);
            }
        }

        if ($ad_currency_id != "") {
            $ad_currency = get_post_meta($ad_currency_id, '_adforest_ad_currency', true);
        }
        /* Get Direction */
        $position = 'left';
        if (adforestAPI_getReduxValue('sb_price_direction', '', true)) {
            $position = adforestAPI_getReduxValue('sb_price_direction', '', false);
        }
        /* Get Symbol */
        $symbol = adforestAPI_getReduxValue('sb_currency', '', false);

        if ($ad_currency != "") {
            $symbol = $ad_currency;
        }
        /* Get And Set Price Formate */
        $thousands_sep = ",";
        if (adforestAPI_getReduxValue('sb_price_separator', '', true)) {
            $thousands_sep = adforestAPI_getReduxValue('sb_price_separator', '', false);
        }
        $decimals = 0;
        if (adforestAPI_getReduxValue('sb_price_decimals', '', true)) {
            $decimals = adforestAPI_getReduxValue('sb_price_decimals', '', false);
        }
        $decimals_separator = ".";
        if (adforestAPI_getReduxValue('sb_price_decimals_separator', '', true)) {
            $decimals_separator = adforestAPI_getReduxValue('sb_price_decimals_separator', '', false);
        }
        $price = @number_format((float)$price, $decimals, $decimals_separator, $thousands_sep);
        $price = (isset($price) && $price != "") ? $price : 0;
        /* Get And Set Price Formate  Ends */
        $pos = ($position != 'left') ? $price . ' ' . $symbol : $symbol . ' ' . $price;

        return array("price" => $pos, "price_type" => $price_typeValue);
    }

}

if (!function_exists('adforestAPI_get_adPrice_currencyPos')) {

    function adforestAPI_get_adPrice_currencyPos($price = '', $symbol = '$') {
        $price = trim($price);
        if ($price == "")
            return '';
        /* Get Direction */
        $position = 'left';
        if (adforestAPI_getReduxValue('sb_price_direction', '', true)) {
            $position = adforestAPI_getReduxValue('sb_price_direction', '', false);
        }

        /* Get And Set Price Formate */
        $thousands_sep = ",";
        if (adforestAPI_getReduxValue('sb_price_separator', '', true)) {
            $thousands_sep = adforestAPI_getReduxValue('sb_price_separator', '', false);
        }
        $decimals = 0;
        if (adforestAPI_getReduxValue('sb_price_decimals', '', true)) {
            $decimals = adforestAPI_getReduxValue('sb_price_decimals', '', false);
        }
        $decimals_separator = ".";
        if (adforestAPI_getReduxValue('sb_price_decimals_separator', '', true)) {
            $decimals_separator = adforestAPI_getReduxValue('sb_price_decimals_separator', '', false);
        }
        $price = @number_format($price, $decimals, $decimals_separator, $thousands_sep);
        $price = (isset($price) && $price != "") ? $price : 0;
        /* Get And Set Price Formate  Ends */

        $price = adforestAPI_convert_uniText($price);
        $symbol = adforestAPI_convert_uniText($symbol);

        return ($position != 'left') ? $price . ' ' . $symbol : $symbol . ' ' . $price;
    }

}

if (!function_exists('adforestAPI_get_adPrice')) {

    function adforestAPI_get_adPrice($ad_id = '', $symbol = '$') {
        $arr = array();
        $ad_price = get_post_meta($ad_id, '_adforest_ad_price', true);
        $price_type = get_post_meta($ad_id, '_adforest_ad_price_type', true);
        $price_typeValue = ($price_type) ? adforestAPI_adPrice_typesValue($price_type) : '';
        /* $symbol = "$"; */
        $symbol = adforestAPI_getReduxValue('sb_currency', '', false);
        $ad_currency = get_post_meta($ad_id, '_adforest_ad_currency', true);
        if ($ad_currency != "") {
            $symbol = $ad_currency;
        }
        $arr = array("price" => $ad_price, "type" => $price_typeValue, "symbol" => $symbol);
        return $arr;
    }

}

if (!function_exists('adforestAPI_get_adAddress')) {

    function adforestAPI_get_adAddress($ad_id = '') {
        $location_arr = array();
        $poster_location = get_post_meta($ad_id, '_adforest_ad_location', true);
        $map_lat = get_post_meta($ad_id, '_adforest_ad_map_lat', true);
        $map_long = get_post_meta($ad_id, '_adforest_ad_map_long', true);

        $map_lat = (is_float($map_lat) || is_numeric($map_lat)) ? $map_lat : '';
        $map_long = (is_float($map_long) || is_numeric($map_long)) ? $map_long : '';

        global $adforestAPI;
        if (isset($adforestAPI['allow_lat_lon']) && $adforestAPI['allow_lat_lon'] == false) {
            $map_lat = $map_long = '';
        }

        $location_arr = array("title" => __("Location", "adforest-rest-api"), "address" => $poster_location, "lat" => $map_lat, "long" => $map_long);
        return $location_arr;
    }

}

if (!function_exists('adforestAPI_get_adVideo')) {

    function adforestAPI_get_adVideo($ad_id = '') {
        $vid_arr = array();
        $ad_video = get_post_meta($ad_id, '_adforest_ad_yvideo', true);
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $ad_video, $match);
        $vID = (isset($match[1]) && $match[1] != "") ? $match[1] : '';
        $vid_arr = array("video_url" => $ad_video, "video_id" => $vID);
        return $vid_arr;
    }

}

/* Custom Fields Starts */
if (!function_exists('adforestAPI_get_customFields')) {

    function adforestAPI_get_customFields($ad_id = '') {
        $result = adforestAPI_categoryForm_data($ad_id);
        //Static Iteams starts
        $type = sb_custom_form_data($result, '_sb_default_cat_ad_type_show');
        $price = sb_custom_form_data($result, '_sb_default_cat_price_show');
        $priceType = sb_custom_form_data($result, '_sb_default_cat_price_type_show');
        $condition = sb_custom_form_data($result, '_sb_default_cat_condition_show');
        $warranty = sb_custom_form_data($result, '_sb_default_cat_warranty_show');
        $dynamicData = array();
        $ad_cats = adforestAPI_get_ad_terms_names($ad_id, 'ad_cats', '', '', $separator = ',');
        //adforestAPI_terms_seprates_by($ad_id , 'ad_cats',  ', ');
        $dynamicData[] = array("key" => __("Category", "adforest-rest-api"), "value" => $ad_cats, "type" => '');
        $dynamicData[] = array("key" => __("Date", "adforest-rest-api"), "value" => get_the_date("", $ad_id), "type" => '');

        if ($type == 1 && $result != "") {
            $custom_val = get_post_meta($ad_id, '_adforest_ad_type', true);
            if ($custom_val != "") {
                $dynamicData[] = array("key" => __("Type", "adforest-rest-api"), "value" => ($custom_val), "type" => '');
            }
        } else {
            $custom_val = get_post_meta($ad_id, '_adforest_ad_type', true);
            if ($custom_val != "") {
                $dynamicData[] = array("key" => __("Type", "adforest-rest-api"), "value" => ($custom_val), "type" => '');
            }
        }

        if ($price == 1 && $result != "") {
            $showType = ($priceType == 1) ? true : false;
            $priceValue = adforestAPI_get_price('', $ad_id);

            if (isset($priceValue['price']) && $priceValue['price'] != "") {
                $price_type = (isset($priceValue['price_type']) && $priceValue['price_type'] != "") ? " (" . $priceValue['price_type'] . ")" : '';
                $finalPrice = $priceValue['price'] . $price_type;
                $dynamicData[] = array("key" => __("Price", "adforest-rest-api"), "value" => $finalPrice, "type" => '');
            }
        } else {
            $priceValue = adforestAPI_get_price('', $ad_id);
            if (isset($priceValue['price']) && $priceValue['price'] != "") {
                $price_type = (isset($priceValue['price_type']) && $priceValue['price_type'] != "") ? " (" . $priceValue['price_type'] . ")" : '';
                $finalPrice = $priceValue['price'] . $price_type;
                $dynamicData[] = array("key" => __("Price", "adforest-rest-api"), "value" => $finalPrice, "type" => '');
            }
        }

        if ($condition == 1 && $result != "") {
            $custom_val = get_post_meta($ad_id, '_adforest_ad_condition', true);
            if ($custom_val != "") {
                $dynamicData[] = array("key" => __("Condition", "adforest-rest-api"), "value" => ($custom_val), "type" => '');
            }
        } else {
            $custom_val = get_post_meta($ad_id, '_adforest_ad_condition', true);
            if ($custom_val != "") {
                $dynamicData[] = array("key" => __("Condition", "adforest-rest-api"), "value" => ($custom_val), "type" => '');
            }
        }

        if ($warranty == 1 && $result != "") {
            $custom_val = get_post_meta($ad_id, '_adforest_ad_warranty', true);
            if ($custom_val != "") {
                $dynamicData[] = array("key" => __("Warranty", "adforest-rest-api"), "value" => ($custom_val), "type" => '');
            }
        } else {
            $custom_val = get_post_meta($ad_id, '_adforest_ad_warranty', true);
            if ($custom_val != "") {
                $dynamicData[] = array("key" => __("Warranty", "adforest-rest-api"), "value" => ($custom_val), "type" => '');
            }
        }

        $is_show_location = wp_count_terms('ad_country');
        if (isset($is_show_location) && $is_show_location > 0) {
            /* Some Location Code Goes Here */
            //$ad_country = adforestAPI_get_ad_terms_names($ad_id, 'ad_country', '', '', $separator = ',');
            //adforestAPI_terms_seprates_by($ad_id , 'ad_cats',  ', ');
            //$dynamicData[] =  array("key" => __("Location", "adforest-rest-api"), "value" => $ad_country, "type" => '');
        }

        /* Static Iteams ends - Dynamic Cats */
        $formData = sb_dynamic_form_data($result);
        if (count($formData) > 0) {

            foreach ($formData as $data) {
                if ($data['titles'] != "") {
                    $values = get_post_meta($ad_id, "_adforest_tpl_field_" . $data['slugs'], true);
                    $value = json_decode($values);
                    $value = (is_array($value)) ? implode($value, ", ") : $values;
                    $titles = ($data['titles']);
                    $status = ($data['status']);
                    if ($value != "" && $status == 1) {
                        $type = 'textfield';
                        if ($data['types'] == '1') {
                            $type = 'textfield';
                        }
                        if ($data['types'] == '2') {
                            $type = 'select';
                        }
                        if ($data['types'] == '3') {
                            $type = 'checkbox';
                             $value = ltrim($value, ",");
                        }
                        if ($data['types'] == '4') {
                            $type = 'textfield_date';
                            $value = date_i18n(get_option('date_format'), strtotime($value));
                        }
                        if ($data['types'] == '5') {
                            $type = 'textfield_url';
                            $value = esc_url($value);
                        }
                        if ($data['types'] == '6') {
                            $type = 'textfield';
                        }
                        if ($data['types'] == '7') {
                            $type = 'color_field';
                            $value = esc_url($value);
                        }
                        if ($data['types'] == '8') {
                            $type = 'radio';
                        }
                        if ($data['types'] == '9') {
                            $type = 'checkbox';
                             $value = ltrim($value, ",");
                        }

                        $dynamicData[] = array("key" => esc_html($titles), "value" => esc_html($value), "type" => $type);
                    }
                }
            }
        }

        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = '$ad_id' AND meta_key LIKE '_sb_extra_%'");
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $caption = explode('_', $row->meta_key);
                $name = ucfirst($caption[3]);
                if ($row->meta_value == "")
                    continue;
                $dynamicData[] = array("key" => esc_html($name), "value" => esc_html($row->meta_value), "type" => '');
            }
        }
        return ($dynamicData);
    }

}

if (!function_exists('adforest_randomString')) {

    function adforest_randomString($length = 50) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

}

// Get user profile PIC
if (!function_exists('adforestAPI_user_dp')) {

    function adforestAPI_user_dp($user_id, $size = 'adforest-andriod-profile') {
        $user_pic = ADFOREST_API_PLUGIN_URL . "images/user.jpg";
        if (adforestAPI_getReduxValue('sb_user_dp', 'url', true)) {
            $user_pic = adforestAPI_getReduxValue('sb_user_dp', 'url', false);
        }
        
        if (get_user_meta($user_id, '_sb_user_linkedin_pic', true) != "") {
            $user_pic = get_user_meta($user_id, '_sb_user_linkedin_pic', true);
            return $user_pic;
        }

        $image_link = array();
        if (get_user_meta($user_id, '_sb_user_pic', true) != "") {
            $attach_id = get_user_meta($user_id, '_sb_user_pic', true);
            if ($attach_id) {
                $image_link = wp_get_attachment_image_src($attach_id, $size);
            }
        }
        return (isset($image_link) && count($image_link) > 0 && $image_link != "") ? $image_link[0] : $user_pic;
    }

}

if (!function_exists('adforestAPI_check_username')) {

    function adforestAPI_check_username($username = '') {
        if (username_exists($username)) {
            $random = rand();
            $username = $username . '-' . $random;
            adforestAPI_check_username($username);
        }
        return $username;
    }

}

/* User ads starts */
if (!function_exists('adforestApi_userAds')) {

    function adforestApi_userAds($userid = '', $status = '', $adType = '', $limit = 1, $adStatus = 'publish', $other = '') {
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($limit)) {
            $paged = $limit;
        } else {
            $paged = 1;
        }

        $adType = ($adType != "") ? array('key' => '_adforest_is_feature', 'value' => $adType, 'compare' => '=') : array();
        $status = ($status != "") ? array('key' => '_adforest_ad_status_', 'value' => $status, 'compare' => '=') : array();

        $lat_lng_meta_query = array();
        if ($other == 'near_me') {
            $lats_longs = adforestAPI_determine_minMax_latLong();
            $get_dstance = ( isset($lats_longs['distance']) && $lats_longs['distance'] > 0 ) ? $lats_longs['distance'] : 0;
            if (isset($lats_longs) && count($lats_longs) > 0 && $get_dstance > 0) {

                $lat_lng_meta_query[] = array(
                    'key' => '_adforest_ad_map_lat',
                    'value' => array($lats_longs['lat']['min'], $lats_longs['lat']['max']),
                    'compare' => 'BETWEEN',
                    'type' => 'DECIMAL',
                );

                $lat_lng_meta_query[] = array(
                    'key' => '_adforest_ad_map_long',
                    'value' => array($lats_longs['long']['min'], $lats_longs['long']['max']),
                    'compare' => 'BETWEEN',
                    'type' => 'DECIMAL',
                );
                add_filter('get_meta_sql', 'adforestAPI_cast_decimal_precision_filter');
            }
        }

        $posts_per_page = get_option('posts_per_page');
        if ($userid != "") {
            $args = array(
                'author' => $userid,
                'post_type' => 'ad_post',
                'post_status' => $adStatus,
                'posts_per_page' => $posts_per_page,
                'paged' => $paged,
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_query' => array($status, $adType, $lat_lng_meta_query),
            );
        } else {
            $args = array(
                'post_type' => 'ad_post',
                'post_status' => $adStatus,
                'posts_per_page' => $posts_per_page,
                'paged' => $paged,
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_query' => array($status, $adType, $lat_lng_meta_query),
            );
        }

        if ($other == 'visited') {
            $args['meta_key'] = 'sb_post_views_count';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        }

        $location_flag = TRUE;
        if (($other == 'visited') || ($other == 'expire_sold') || ($other == 'profile_featured') || ($other == 'profile_inactive') || ($other == 'profile_rejected') || ($other == 'profile_fav')) {
            $location_flag = FALSE;
        }

        if ($location_flag) {
            $args = apply_filters('adforestAPI_site_location_args', $args, 'ads');
        }

        $ad_data = adforestAPI_adsLoop($args, $userid, true, false, true);
        $found_posts = $ad_data['found_posts'];
        $max_num_pages = $ad_data['max_num_pages'];

        $nextPaged = $paged + 1;
        $has_next_page = ($nextPaged <= (int) $max_num_pages) ? true : false;
        $adData = array();
        $adData['ads'] = $ad_data['ads'];
        $adData['pagination'] = array(
            "max_num_pages" => (int) $max_num_pages,
            "current_page" => (int) $paged,
            "next_page" => (int) $nextPaged,
            "increment" => (int) $posts_per_page,
            "current_no_of_ads" => (int) $found_posts,
            "has_next_page" => $has_next_page
        );
        return $adData;
    }

}
/* User ads starts */

/* User ads starts */
if (!function_exists('adforestApi_userAds_fav')) {

    function adforestApi_userAds_fav($userid = '', $status = '', $adType = '', $limit = 1, $adStatus = 'publish') {

        $adType = ($adType != "") ? array('key' => '_adforest_is_feature', 'value' => $adType, 'compare' => '=') : array();
        $status = ($status != "") ? array('key' => '_adforest_ad_status_', 'value' => $status, 'compare' => '=') : array();

        global $wpdb;
        $rows = $wpdb->get_results("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = '$userid' AND meta_key LIKE '_sb_fav_id_%'");
        $pids = array();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $pids[] = (int) $row->meta_value;
            }
        } else {
            $pids = array(0);
        }

        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($limit)) {
            $paged = $limit;
        } else {
            $paged = 1;
        }

        $posts_per_page = get_option('posts_per_page');
        $args = array(
            'post__in' => $pids,
            'post_type' => 'ad_post',
            'post_status' => $adStatus,
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'order' => 'DESC',
            'orderby' => 'date',
        );
        $args = apply_filters('adforestAPI_site_location_args', $args, 'ads');
        $ad_data = adforestAPI_adsLoop($args, $userid, true, true, true);
        $found_posts = $ad_data['found_posts'];
        $max_num_pages = $ad_data['max_num_pages'];
        $nextPaged = $paged + 1;
        $has_next_page = ($nextPaged <= (int) $max_num_pages) ? true : false;
        $adData = array();
        $adData['ads'] = $ad_data['ads'];
        $adData['pagination'] = array(
            "max_num_pages" => (int) $max_num_pages,
            "current_page" => (int) $paged,
            "next_page" => (int) $nextPaged,
            "increment" => (int) $posts_per_page,
            "current_no_of_ads" => (int) $found_posts,
            "has_next_page" => $has_next_page
        );
        return $adData;
    }

}
/* User ads starts */

/* featured ad slider starts */
if (!function_exists('adforestApi_featuredAds_slider')) {

    function adforestApi_featuredAds_slider($userid = '', $status = '', $adType = '', $limit = - 1, $term_id = '', $adStatus = 'publish', $other = '') {
        $adType = ($adType != "") ? array('key' => '_adforest_is_feature', 'value' => $adType, 'compare' => '=') : array();
        $status = ($status != "") ? array('key' => '_adforest_ad_status_', 'value' => $status, 'compare' => '=') : array();

        $lat_lng_meta_query = array();
        if ($other == 'nearby') {
            $lats_longs = adforestAPI_determine_minMax_latLong();
            if (isset($lats_longs) && count($lats_longs) > 0) {

                $lat_lng_meta_query[] = array(
                    'key' => '_adforest_ad_map_lat',
                    'value' => array($lats_longs['lat']['min'], $lats_longs['lat']['max']),
                    'compare' => 'BETWEEN',
                    'type' => 'DECIMAL',
                );

                $lat_lng_meta_query[] = array(
                    'key' => '_adforest_ad_map_long',
                    'value' => array($lats_longs['long']['min'], $lats_longs['long']['max']),
                    'compare' => 'BETWEEN',
                    'type' => 'DECIMAL',
                );
                add_filter('get_meta_sql', 'adforestAPI_cast_decimal_precision_filter');
            } else {
                return array();
            }
        }

        $category = '';
        if ($term_id != "") {
            $category = array(
                array(
                    'taxonomy' => 'ad_cats',
                    'field' => 'term_id',
                    'terms' => $term_id,
                ),
            );
        }

        $author_not_in = adforestAPI_get_authors_notIn_list();


        $loc_args = '';
        $loc_args = apply_filters('adforestAPI_site_location_args', $loc_args, 'search');


        $args = array(
            'post_type' => 'ad_post',
            'post_status' => $adStatus,
            'posts_per_page' => $limit,
            'order' => 'DESC',
            'orderby' => 'date',
            'tax_query' => array($category, $loc_args),
            'meta_query' => array($status, $adType, $lat_lng_meta_query),
            'author__not_in' => $author_not_in,
        );
        $args = apply_filters('AdforestAPI_wpml_show_all_posts', $args);
        return adforestAPI_adsLoop($args, $userid, true);
    }

}
/* featured ad slider  starts */

/* site ads starts */
if (!function_exists('adforestApi_siteAds')) {

    function adforestApi_siteAds($userid = '', $status = '', $adType = '', $limit = - 1, $adStatus = 'publish') {
        $adType = ($adType != "") ? array('key' => '_adforest_is_feature', 'value' => $adType, 'compare' => '=') : array();
        $status = ($status != "") ? array('key' => '_adforest_ad_status_', 'value' => $status, 'compare' => '=') : array();
        $args = array(
            'post_type' => 'ad_post',
            'post_status' => $adStatus,
            'posts_per_page' => $limit,
            'order' => 'DESC',
            'orderby' => 'date',
            'meta_query' => array($status, $adType),
        );
        $args = apply_filters('adforestAPI_site_location_args', $args, 'ads');
        return adforestAPI_adsLoop($args, $userid, true);
    }

}
/* site ads starts */

add_action('rest_api_init', 'add_thumbnail_to_JSON');

function add_thumbnail_to_JSON() {
    register_rest_field('post', 'featured_image_src', array('get_callback' => 'get_image_src', 'update_callback' => null, 'schema' => null,));
}

function get_image_src($object, $field_name, $request) {
    $feat_img_array = wp_get_attachment_image_src($object['featured_media'], 'adforest-single-post', true);
    return $feat_img_array[0];
}

add_action('rest_api_init', 'add_post_comment_count');

function add_post_comment_count() {
    register_rest_field('post', 'post_comment_count', array('get_callback' => 'add_post_comment_count_func', 'update_callback' => null, 'schema' => null,));
}

function add_post_comment_count_func($object, $field_name, $request) {
    return 100;
}

/* Get login time */
if (!function_exists('adforestAPI_getLastLogin')) {

    function adforestAPI_getLastLogin($uid, $show_text = false) {
        $from = get_user_meta($uid, '_sb_last_login', true);
        if ($from != "") {
            /* DO Somethings */
        } else {
            $from = time();
        }
        $showText = ($show_text) ? __("Last Login:", "adforest-rest-api") : '';
        return $showText . ' ' . human_time_diff($from, time());
    }

}

/* Input */
if (!function_exists('adforestAPI_get_ad_terms_names_vals')) {

    function adforestAPI_get_ad_terms_names_vals($term_type = 'ad_cats', $only_parent = 0, $name = '', $placeholder = '') {
        $terms = get_terms(array('taxonomy' => $term_type, 'hide_empty' => false, 'parent' => $only_parent,));

        $termsArr = array();
        $catNames = '';
        $catIDS = '';
        $values = '';
        if (count($terms)) {
            foreach ($terms as $ad_trm) {
                $catIDS[] = $ad_trm->term_id;
                $catNames[] = htmlspecialchars_decode($ad_trm->name, ENT_NOQUOTES);
            }
        }

        return $arr = array("ids" => $catIDS, "names" => $catNames, "title" => $name, "placeholder" => $placeholder);
    }

}
if (!function_exists('adforestAPI_get_search_inputs')) {

    function adforestAPI_get_search_inputs($title = '', $placeholder = '') {
        return $arr = array("title" => $title, "placeholder" => $placeholder);
    }

}

if (!function_exists('adforestAPI_getSubCats')) {

    function adforestAPI_getSubCats($field_type = '', $field_type_name = '', $term_type = 'ad_cats', $only_parent = 0, $name = '', $mainTitle = '', $show_count = true, $page_ = '') {

        global $adforestAPI;
        $hasShow_template = false;
        if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == true && $term_type == "ad_cats") {
            $hasShow_template = true;
        }

        $terms = get_terms(array('taxonomy' => $term_type, 'hide_empty' => false, 'parent' => $only_parent,));

        $termsArr = array();
        $values = '';
        if (count($terms) > 0) {
            foreach ($terms as $ad_trm) {
                $term_children = get_term_children(filter_var($ad_trm->term_id, FILTER_VALIDATE_INT), filter_var($term_type, FILTER_SANITIZE_STRING));
                $has_sub = (empty($term_children) || is_wp_error($term_children)) ? false : true;
                $result = adforest_dynamic_templateID($ad_trm->term_id);
                $templateID = get_term_meta($result, '_sb_dynamic_form_fields', true);
                $has_template = (isset($templateID) && $templateID != "") ? true : false;
                if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == true && $term_type == "ad_cats") {
                    $has_template = true;
                }
                if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == false) {
                    $has_template = false;
                }
                $search_popup_cat_loc_disable = FALSE;

                if ($page_ != 'post_page') {
                    if ($term_type == 'ad_cats') {
                        $search_popup_cat_disable = isset($adforestAPI['search_popup_cat_disable']) ? $adforestAPI['search_popup_cat_disable'] : false;
                        if ($search_popup_cat_disable) {
                            $search_popup_cat_loc_disable = TRUE;
                        }
                    }

                    if ($term_type == 'ad_country') {
                        $search_popup_loc_disable = isset($adforestAPI['search_popup_loc_disable']) ? $adforestAPI['search_popup_loc_disable'] : false;
                        if ($search_popup_loc_disable) {
                            $search_popup_cat_loc_disable = TRUE;
                        }
                    }
                }

                $counts = ($show_count == true) ? ' (' . $ad_trm->count . ')' : "";
                if ($search_popup_cat_loc_disable) { // disable all cats / subcats less than 1 ad.
                    if ($ad_trm->count > 0) {
                        $termsArr[] = array("id" => $ad_trm->term_id, "name" => htmlspecialchars_decode($ad_trm->name, ENT_NOQUOTES) . $counts, "has_sub" => $has_sub, "has_template" => $has_template,);
                    }
                } else {

                    $termsArr[] = array("id" => $ad_trm->term_id, "name" => htmlspecialchars_decode($ad_trm->name, ENT_NOQUOTES) . $counts, "has_sub" => $has_sub, "has_template" => $has_template,);
                }
            }

            $values = $termsArr;
        }

        if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == false) {
            $has_template = false;
        }

        return array(
            "main_title" => $mainTitle,
            "field_type" => $field_type,
            "field_type_name" => $field_type_name,
            "field_val" => "",
            "field_name" => "",
            "title" => $name,
            "values" => $values,
            "has_cat_template" => $has_template
        );
    }

}

if (!function_exists('adforestAPI_is_multiArr')) {

    function adforestAPI_is_multiArr($a) {
        $rv = array_filter($a, 'is_array');
        if (count($rv) > 0)
            return true;
        return false;
    }

}


if (!function_exists('adforestAPI_adPrice_typesValue')) {

    function adforestAPI_adPrice_typesValue($selectVal = '') {
        global $adforestAPI;
        $array = array();
        $priceTypes = array(
            'Fixed' => __('Fixed', 'adforest-rest-api'),
            '',
            'Negotiable' => __('Negotiable', 'adforest-rest-api'),
            'on_call' => __('Price on call', 'adforest-rest-api'),
            'auction' => __('Auction', 'adforest-rest-api'),
            'free' => __('Free', 'adforest-rest-api'),
            'no_price' => __('No price', 'adforest-rest-api'),
        );

        $custom_pricetype = array();
        if (isset($adforestAPI['sb_price_types_more']) && $adforestAPI['sb_price_types_more'] != "") {
            $types = @explode("|", $adforestAPI['sb_price_types_more']);
            if (count($types) > 0) {
                foreach ($types as $t) {
                    $custom_key = str_replace(' ', '_', $t);
                    $custom_pricetype[$custom_key] = $t;
                }

                $priceTypes = array_merge($priceTypes, $custom_pricetype);
            }
        }

        $returnValue = '';
        if (isset($priceTypes[$selectVal]) && $priceTypes[$selectVal] != "") {
            $returnValue = $priceTypes[$selectVal];
        }
        return $returnValue;
    }

}

if (!function_exists('adforestAPI_adPrice_types')) {

    function adforestAPI_adPrice_types($selectVal = '') {
        global $adforestAPI;

        //echo $selectVal;

        $array = array();
        $priceTypes = array(
            'Fixed' => __('Fixed', 'adforest-rest-api'),
            '',
            'Negotiable' => __('Negotiable', 'adforest-rest-api'),
            'on_call' => __('Price on call', 'adforest-rest-api'),
            'auction' => __('Auction', 'adforest-rest-api'),
            'free' => __('Free', 'adforest-rest-api'),
            'no_price' => __('No price', 'adforest-rest-api'),
        );

        if (isset($adforestAPI['sb_price_types']) && $adforestAPI['sb_price_types'] && count($adforestAPI['sb_price_types']) > 0) {

            foreach ($adforestAPI['sb_price_types'] as $val) {
                $is_show = ($val == "on_call" || $val == "free" || $val == "no_price") ? false : true;
                if (isset($priceTypes[$val]) && $priceTypes[$val] != "" && $val != "") {
                    $value = $priceTypes[$val];
                    $array[] = array("key" => $val, "val" => $value, "is_show" => $is_show);
                }
            }
        } else {

//            foreach ($priceTypes as $p_key => $p_val) {
//                if ($p_key != "" && $p_val != "") {
//                    $is_show = ($p_key == "on_call" || $p_key == "free" || $p_key == "no_price") ? false : true;
//                    $array[] = array("key" => $p_key, "val" => $p_val, "is_show" => $is_show);
//                }
//            }
        }

        if (isset($adforestAPI['sb_price_types_more']) && $adforestAPI['sb_price_types_more'] != "") {
            $types = @explode("|", $adforestAPI['sb_price_types_more']);
            if (count($types) > 0) {
                foreach ($types as $t) {
                    $custom_key = str_replace(' ', '_', $t);
                    $array[] = array("key" => $custom_key, "val" => $t, "is_show" => true);
                }
            }
        }

        if ($selectVal != "") {
            $newKey = array();
            foreach ($array as $key => $value) {
                if ($selectVal == $value['key']) {
                    $arrIndex = $value;
                    $newKey = $arrIndex;
                    unset($array[$key]);
                    array_unshift($array, $newKey);
                }
            }
        }

//        if(empty($array)){
//           $array[] =  __('Select Price Type', 'adforest-rest-api');
//        }
        //print_r($array);
        return $array;
    }

}

if (!function_exists('adforestAPI_arraySearch')) {

    function adforestAPI_arraySearch($array, $index, $value) {
        if ($value != "") {
            $arr = array();
            $count = 0;
            foreach ($array as $key => $val) {
                $data = ($index != "") ? $val["$index"] : $val;
                if ($data == $value) {
                    $arr = ($val);
                    unset($array[$count]);
                }
                $count++;
            }
            $array = array_merge(array($arr), $array);
        }
        return $array;
    }

}

if (!function_exists('adforestAPI_objArraySearch')) {

    function adforestAPI_objArraySearch($array, $index, $value, $newIndex = array()) {
        $extractedKey = array();
        if (isset($array) && count($array) > 0) {
            foreach ($array as $key => $arrayInf) {
                if ($arrayInf->{$index} == $value) {
                    unset($array[$key]);
                    $extractedKey = $arrayInf;
                    //return $arrayInf;
                }
            }
        }
        return (isset($newIndex) && count((array) $newIndex) > 0 && $newIndex != "") ? array_merge(array(
                    $newIndex
                        ), $array) : $array;
    }

}

if (!function_exists('adforestAPI_getPostAdFields')) {

    function adforestAPI_getPostAdFields($field_type = '', $field_type_name = '', $term_type = 'ad_cats', $only_parent = 0, $name = '', $mainTitle = '', $defaultValue = '', $has_page_number = 1, $is_required = false, $update_val = '', $ad_id = '') {
        global $adforestAPI;
        $values = '';
        
        do_action('AdforestAPI_set_language');       

        $returnType = 1;
        $values_arr = array();
        $has_cat_template = false;
        if ($field_type == "select") {

            $termsArr = array();

            if ('ad_price_type' == $field_type_name && $update_val == '') {
                $termsArr[] = array(
                    'id' => '',
                    'name' => __('Select Option', 'adforest-rest-api'),
                    'has_sub' => false,
                    'has_template' => false,
                    'is_show' => true,
                );
            }
            if (is_array($term_type)) {

                $is_multiArr = adforestAPI_is_multiArr($term_type);
                if ($is_multiArr == true) {
                    $term_type = adforestAPI_arraySearch($term_type, "key", $update_val);
                    foreach ($term_type as $val) {
                        $termsArr[] = array(
                            "id" => (string) $val['key'],
                            "name" => $val['val'],
                            "has_sub" => false,
                            "has_template" => false,
                            "is_show" => $val['is_show'],
                        );
                    }
                } else {
                    foreach ($term_type as $key => $val) {
                        $termsArr[] = array(
                            "id" => (string) $key,
                            "name" => $val,
                            "has_sub" => false,
                            "has_template" => false,
                            "is_show" => true,
                        );
                    }
                }
            } else {

                $has_cat_template = ($term_type == "ad_cats") ? true : false;
                $terms = get_terms(array('taxonomy' => $term_type, 'hide_empty' => false, 'parent' => $only_parent,));

                $ad_cats = array();
                if ($ad_id != "") {
                    $ad_cats = wp_get_object_terms($ad_id, $term_type, array('fields' => 'ids', 'orderby' => 'term_order', 'order' => 'ASC',));
                    if ($term_type == "ad_country") {
                        $ad_cats = adforestAPI_getCats_idz($ad_id, 'ad_country', true);
                        /* adforestAPI_cat_ancestors( $ad_cats, 'ad_country', true); */
                    }
                    if (count($ad_cats) > 0) {
                        $count_update_val = count($ad_cats) - 1;
                        $finalCatID = $ad_cats[$count_update_val];
                        $term_data = get_term_by('id', $finalCatID, $term_type);
                        $terms = adforestAPI_objArraySearch($terms, 'term_id', $finalCatID, $term_data);
                    }
                }

                if ($term_type == "ad_country")
                //print_r( $ad_cats );
                    $termsArr = array();
                $catNames = '';
                $catIDS = '';
                if (count($terms)) {
                    if (count($ad_cats) == 0) {
                        $termsArr[] = array("id" => "", "name" => __("Select Option", "adforest-rest-api"), "has_sub" => false, "has_template" => false, "is_show" => true,);
                    }
                    foreach ($terms as $ad_trm) {

                        $result = adforest_dynamic_templateID(@$ad_trm->term_id);
                        $templateID = get_term_meta($result, '_sb_dynamic_form_fields', true);
                        $has_template = (isset($templateID) && $templateID != "") ? true : false;

                        if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == true && $term_type == "ad_cats") {
                            $has_template = true;
                        }
                        $term_children = get_term_children(filter_var(@$ad_trm->term_id, FILTER_VALIDATE_INT), filter_var($term_type, FILTER_SANITIZE_STRING));
                        $has_sub = (empty($term_children) || is_wp_error($term_children)) ? false : true;

                        $catarr_data = array(
                            "id" => (string) $ad_trm->term_id,
                            "name" => htmlspecialchars_decode(@$ad_trm->name, ENT_NOQUOTES),
                            "has_sub" => $has_sub,
                            "has_template" => $has_template,
                            "is_show" => true,
                            "is_show" => true,
                        );

                        if ($term_type == 'ad_cats') {
                            $bid_check = apply_filters('adforestAPI_check_bid_availability', true, $ad_trm->term_id);

                            $cat_pkg_data = apply_filters('AdforestAPI_check_category_price_avl', $ad_trm->term_id); // category pkg filter

                            $catarr_data['is_bidding'] = $bid_check;

                            if (isset($cat_pkg_data) && is_array($cat_pkg_data) && count($catarr_data) > 0) {
                                $catarr_data['can_post'] = $cat_pkg_data['can_post'];
                                $catarr_data['is_paid'] = $cat_pkg_data['is_paid'];
                            } else {
                                $catarr_data['can_post'] = TRUE;
                                $catarr_data['is_paid'] = FALSE;
                            }
                        }
                        $termsArr[] = $catarr_data;
                    }
                }
            }
            $values = $termsArr;
            $values_arr = $termsArr;
        }

        if ($field_type == "textfield" || "glocation_textfield" == $field_type || "textarea" == $field_type) {
            $values = $defaultValue;
            $values_arr = ($defaultValue != "") ? array(
                $defaultValue
                    ) : array();
            if ($term_type != "") {
                $update_val = get_post_meta($ad_id, $term_type, true);
            }
        }

        if ($field_type == "image") {
            $values_arr = ($defaultValue != "") ? array(
                $defaultValue
                    ) : array();
            $values = $defaultValue;
        }
        if ($field_type == "map") {
            $values_arr = ($defaultValue != "") ? array($defaultValue) : array();
            $values = $defaultValue;
        }

        if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == false) {
            $has_cat_template = false;
        }

        $values_data = $values;
        return array(
            "main_title" => $mainTitle,
            "field_type" => $field_type,
            "field_type_name" => $field_type_name,
            "field_val" => $update_val,
            "field_name" => "",
            "title" => $name,
            "values" => $values_data,
            "has_page_number" => (string) $has_page_number,
            "is_required" => $is_required,
            "has_cat_template" => $has_cat_template
        );
    }

}
if (!function_exists('adforestAPI_getCats_idz')) {
    function adforestAPI_getCats_idz($postId, $term_name, $reverse_arr = false) {
        $terms = wp_get_post_terms($postId, $term_name, array('orderby' => 'id', 'order' => 'DESC'));
        $deepestTerm = false;
        $maxDepth = - 1;
        $c = 0;
        if (isset($terms) && count($terms) > 0) {
            foreach ($terms as $term) {
                $ancestors = get_ancestors($term->term_id, $term_name);
                $termDepth = count($ancestors);
                $deepestTerm[$c] = $term->term_id;
                $maxDepth = $termDepth;
                $c++;
            }
            return ($reverse_arr == false) ? $deepestTerm : array_reverse($deepestTerm);
        } else {
            return array();
        }
    }
}

if (!function_exists('adforestAPI_getUnreadMessageCount')) {
    function adforestAPI_getUnreadMessageCount() {
        global $wpdb;
        $user_id = get_current_user_id();
        $unread_msgs = 0;
        if ($user_id) {
            $unread_msgs = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id = '$user_id' AND meta_value = '0' ");
        }
        return $unread_msgs;
    }
}

if (!function_exists('adforestAPI_getSearchFields')) {
    function adforestAPI_getSearchFields($field_type = '', $field_type_name = '', $term_type = 'ad_cats', $only_parent = 0, $name = '', $mainTitle = '', $defaultValue = '', $is_id = true) {
        global $adforestAPI;
        $values = '';
        $returnType = 1;
        $has_cat_template = false;
        if ($field_type == "select") {
            $termsArr = array();
            if (is_array($term_type)) {

                foreach ($term_type as $key => $val) {
                    $termsArr[] = array("id" => (string) $key, "name" => $val, "has_sub" => false, "has_template" => false,);
                }
            } else {
                $has_cat_template = ($term_type == "ad_cats") ? true : false;
                $show_counts = ($term_type == "ad_cats") ? true : false;
                $terms = get_terms(array('taxonomy' => $term_type, 'hide_empty' => false, 'parent' => $only_parent,));
                $termsArr = array();
                $catNames = '';
                $catIDS = '';
                if (count($terms)) {

                    $termsArr[] = array("id" => "", "name" => __("Select Option", "adforest-rest-api"), "has_sub" => false, "has_template" => false,);

                    foreach ($terms as $ad_trm) {

                        $result = adforest_dynamic_templateID($ad_trm->term_id);
                        $templateID = get_term_meta($result, '_sb_dynamic_form_fields', true);
                        $has_template = (isset($templateID) && $templateID != "") ? true : false;

                        if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == true) {
                            if ($term_type == "ad_cats") {
                                $has_template = true;
                            }
                        }

                        $countsNumber = ($show_counts == true) ? ' (' . $ad_trm->count . ')' : '';
                        $countsNumber = ''; /* Tempraroty Off */
                        $term_children = get_term_children(filter_var($ad_trm->term_id, FILTER_VALIDATE_INT), filter_var($term_type, FILTER_SANITIZE_STRING));
                        $has_sub = (empty($term_children) || is_wp_error($term_children)) ? false : true;
                        $idVal = ($is_id == true) ? $ad_trm->term_id : htmlspecialchars_decode($ad_trm->name, ENT_NOQUOTES);
                        $termsArr[] = array(
                            "id" => (string) $idVal,
                            "name" => htmlspecialchars_decode($ad_trm->name, ENT_NOQUOTES) . $countsNumber,
                            "has_sub" => $has_sub,
                            "has_template" => $has_template,
                                /* "count" => $ad_trm->count, */
                        );
                    }
                }
            }
            $values = $termsArr;
        }

        if ($field_type == "textfield" || "glocation_textfield" == $field_type || "textarea" == $field_type) {
            $values = $defaultValue;
        }

        if ($field_type == "seekbar") {
            $values = $defaultValue;
        }
        if ($field_type == "image") {
            $values = $defaultValue;
        }
        if ($field_type == "map") {
            $values = $defaultValue;
        }

        if ("range_textfield" == $field_type) {
            $title1 = $name[0];
            $title2 = $name[1];
            $array[] = array("title" => $title1);
            $array[] = array("title" => $title2);
            $returnType = 2;
            $values = '';
        }

        if ($returnType == 2) {
            return array("title" => $mainTitle, "field_type_name" => $field_type_name, "field_type" => $field_type, "data" => $array);
        } else {
            if (isset($adforestAPI['adpost_cat_template']) && $adforestAPI['adpost_cat_template'] == false) {
                $has_cat_template = false;
            }

            return array(
                "main_title" => $mainTitle,
                "field_type" => $field_type,
                "field_type_name" => $field_type_name,
                "field_val" => "",
                "field_name" => "",
                "title" => $name,
                "values" => $values,
                "has_cat_template" => $has_cat_template
            );
        }
    }

}

if (!function_exists('adforestAPI_categoryForm_data')) {
    function adforestAPI_categoryForm_data($postId) {
        $resultD = '';
        $terms = wp_get_post_terms($postId, 'ad_cats', array('orderby' => 'id', 'order' => 'DESC'));
        $deepestTerm = false;
        $maxDepth = - 1;
        $c = 0;
        foreach ($terms as $term) {
            $ancestors = get_ancestors($term->term_id, 'ad_cats');
            $termDepth = count($ancestors);
            $deepestTerm[$c] = $term;
            $maxDepth = $termDepth;
            $c++;
        }
        $term_id = '';
        if (isset($deepestTerm) && is_array($deepestTerm) && count($deepestTerm) > 0) {

            foreach ($deepestTerm as $term) {
                $term_id = $term->term_id;
                $t = adforest_dynamic_templateID($term_id);
                if ($t)
                    break;
            }
            $templateID = adforest_dynamic_templateID($term_id);
            $resultD = get_term_meta($templateID, '_sb_dynamic_form_fields', true);
        }
        return $resultD;
    }
}

if (!function_exists('adforestAPI_getCats_template')) {
    function adforestAPI_getCats_template($postId) {
        $result = adforestAPI_categoryForm_data($postId);
        $formData = sb_dynamic_form_data($result);
        $dynamicData = array();
        if (count($formData) > 0) {

            foreach ($formData as $data) {
                if ($data['titles'] != "") {

                    $values = get_post_meta($postId, "_adforest_tpl_field_" . $data['slugs'], true);
                    $value = json_decode($values);
                    $value = (is_array($value)) ? implode(", ",$value) : $values;

                    $titles = ($data['titles']);
                    $status = ($data['status']);
                    if ($value != "" && $status == 1) {
                        $type = 'textfield';
                        if ($data['types'] == '1') {
                            $type = 'textfield';
                        }
                        if ($data['types'] == '2') {
                            $type = 'select';
                        }
                        if ($data['types'] == '3') {
                            $type = 'checkbox';
                        }
                        $dynamicData[] = array(
                            "key" => esc_html($titles),
                            "value" => esc_html($value),
                            "type" => $type
                        );
                    }
                }
            }
        }
        return ($dynamicData);
    }
}

if (!function_exists('adforestAPI_get_posts_count')) {
    function adforestAPI_get_posts_count() {
        global $wp_query;
        return $wp_query->post_count;
    }
}

if (!function_exists('adforestAPI_get_customAdPostFields')) {

    function adforestAPI_get_customAdPostFields($form_type = '', $fieldsData = '', $extra_section_title = '') {
        $appArr['form_type'] = $form_type;
        $arr = array();
        if (isset($fieldsData) && count($fieldsData) > 0) {
            $rows = $fieldsData;
            if (count($rows[0]) > 0 && count($rows) > 0) {
                foreach ($rows as $row) {
                    if (isset($row['title']) && isset($row['type']) && isset($row['slug'])) {
                        $option_values = (isset($row['option_values']) && $row['option_values'] != "") ? $row['option_values'] : '';
                        $arr[] = array(
                            "section_title" => $extra_section_title,
                            "type" => $row['type'],
                            "title" => $row['title'],
                            "slug" => $row['slug'],
                            "option_values" => $option_values
                        );
                    }
                }
            }
        }
        $appArr['custom_fields'] = $arr;
        $data = json_encode($appArr, true);
        update_option("_adforestAPI_customFields", $data);
    }

}
/* Time Ago */
if (!function_exists('adforestAPI_timeago')) {

    function adforestAPI_timeago($date) {
        $timestamp = strtotime($date);

        if (function_exists('adforest_set_date_timezone')) {
            adforest_set_date_timezone();
        }
        
        
        $strTime = array(
            __('second', 'adforest-rest-api'),
            __('minute', 'adforest-rest-api'),
            __('hour', 'adforest-rest-api'),
            __('day', 'adforest-rest-api'),
            __('month', 'adforest-rest-api'),
            __('year', 'adforest-rest-api')
        );
        $length = array("60", "60", "24", "30", "12", "10");

        $currentTime = strtotime(current_time('mysql',1));
        
        if ($currentTime >= $timestamp) {
            $diff = $currentTime - $timestamp;
            for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
                $diff = $diff / $length[$i];
            }
            $diff = round($diff);
            return $diff . " " . $strTime[$i] . __('(s) ago', 'adforest-rest-api');
        }
    }

}

// Time difference n days
if (!function_exists('adforestAPI_days_diff')) {

    function adforestAPI_days_diff($now, $from) {
        $datediff = $now - $from;
        return floor($datediff / (60 * 60 * 24));
    }

}

if (!function_exists('adforestAPI_do_register')) {

    function adforestAPI_do_register($email = '', $password = '') {
        global $adforestAPI;
        $adforestAPI   =    get_option('adforestAPI');
        $user_name = explode('@', $email);             
        $u_name = adforestAPI_check_user_name($user_name[0]);
        $uid = wp_create_user($u_name, $password, $email);
        do_action('adforest_subscribe_newsletter_on_regisster', $adforestAPI, $uid);
        wp_update_user(array('ID' => $uid, 'display_name' => $u_name));
        //adforestAPI_auto_login($email, $password, true );
        $sb_allow_ads = $adforestAPI['sb_allow_ads'];
        
        
        if (isset($sb_allow_ads) && $sb_allow_ads == true) {
            $free_ads = $adforestAPI['sb_free_ads_limit'];
                    
                    
            $featured_ads =  $adforestAPI['sb_featured_ads_limit'];
            $sb_bump_ads_limit = $adforestAPI['sb_bump_ads_limit'];

            $package_validity = $adforestAPI['sb_package_validity'];
            $allow_featured_ads = $adforestAPI['sb_allow_featured_ads'];

            $free_ads = (isset($free_ads) && $free_ads != "") ? $free_ads : 0;
            $featured_ads = (isset($featured_ads) && $featured_ads != "") ? $featured_ads : 0;
            $package_validity = (isset($package_validity) && $package_validity != "") ? $package_validity : '';

            update_user_meta($uid, '_sb_simple_ads', $free_ads);
            update_user_meta($uid, '_sb_bump_ads', $sb_bump_ads_limit);

            if (isset($allow_featured_ads) && $allow_featured_ads == true) {
                update_user_meta($uid, '_sb_featured_ads', $featured_ads);
            }
            if ($package_validity == '-1') {
                update_user_meta($uid, '_sb_expire_ads', $package_validity);
            } else {
                $days = $package_validity;
                $expiry_date = date('Y-m-d', strtotime("+$days days"));
                update_user_meta($uid, '_sb_expire_ads', $expiry_date);
            }
        } else {
            update_user_meta($uid, '_sb_simple_ads', 0);
            update_user_meta($uid, '_sb_featured_ads', 0);
            update_user_meta($uid, '_sb_expire_ads', date('Y-m-d'));
        }
        update_user_meta($uid, '_sb_pkg_type', 'free');
        return $uid;
    }

}
if (!function_exists('adforestAPI_check_user_name')) {

    function adforestAPI_check_user_name($username = '') {
        if (username_exists($username)) {
            $random = mt_rand();
            $username = $username . '-' . $random;
            adforestAPI_check_user_name($username);
        }
        return $username;
    }

}

if (!function_exists('adforestAPI_get_all_ratings')) {

    function adforestAPI_get_all_ratings($user_id) {
        global $wpdb;
        $ratings = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE user_id = '$user_id' AND  meta_key like  '_user_%' ORDER BY umeta_id DESC", OBJECT);
        return $ratings;
    }

}

// Email on ad publish
add_action('transition_post_status', 'adforestAPI_send_mails_on_publish', 10, 3);

function adforestAPI_send_mails_on_publish($new_status, $old_status, $post) {
    if ('publish' !== $new_status or 'publish' === $old_status or 'ad_post' !== get_post_type($post))
        return;

    global $adforestAPI;
    if (isset($adforestAPI['email_on_ad_approval']) && $adforestAPI['email_on_ad_approval']) {

        $my_theme = wp_get_theme();
        if ($my_theme->get('Name') != 'adforest' && $my_theme->get('Name') != 'adforest child') {
            adforest_get_notify_on_ad_approval($post);
        }        
        
    }
}

// check permission for ad posting
if (!function_exists('adforestAPI_check_ads_validity')) {

    function adforestAPI_check_ads_validity() {
        global $adforestAPI;

        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        $uid = $user_id;
        $message = '';
        if (get_user_meta($user_id, '_sb_simple_ads', true) == 0 || get_user_meta($uid, '_sb_simple_ads', true) == "") {
            $message = __('Please subscribe package for ad posting.', 'adforest-rest-api');
        } else {

            if (get_user_meta($user_id, '_sb_expire_ads', true) != '-1') {
                if (get_user_meta($user_id, '_sb_expire_ads', true) < date('Y-m-d')) {
                    update_user_meta($user_id, '_sb_simple_ads', 0);
                    update_user_meta($user_id, '_sb_featured_ads', 0);
                    update_user_meta($user_id, '_sb_bump_ads', 0);
                    $message = __('Your package has been expired.', 'adforest-rest-api');
                }
            }
        }

        return $message;
    }

}

if (!function_exists('adforestAPI_make_link')) {

    function adforestAPI_make_link($url, $text) {
        return wp_kses("<a href='" . esc_url($url) . "' target='_blank'>", adforestAPI_required_tags()) . $text . wp_kses('</a>', adforestAPI_required_tags());
    }

}

if (!function_exists('adforestAPI_required_attributes')) {

    function adforestAPI_required_attributes() {
        return $default_attribs = array(
            'id' => array(),
            'src' => array(),
            'href' => array(),
            'target' => array(),
            'class' => array(),
            'title' => array(),
            'type' => array(),
            'style' => array(),
            'data' => array(),
            'role' => array(),
            'aria-haspopup' => array(),
            'aria-expanded' => array(),
            'data-toggle' => array(),
            'data-hover' => array(),
            'data-animations' => array(),
            'data-mce-id' => array(),
            'data-mce-style' => array(),
            'data-mce-bogus' => array(),
            'data-href' => array(),
            'data-tabs' => array(),
            'data-small-header' => array(),
            'data-adapt-container-width' => array(),
            'data-height' => array(),
            'data-hide-cover' => array(),
            'data-show-facepile' => array(),
        );
    }

}

if (!function_exists('adforestAPI_required_tags')) {

    function adforestAPI_required_tags() {
        return $allowed_tags = array(
            'div' => adforestAPI_required_attributes(),
            'span' => adforestAPI_required_attributes(),
            'p' => adforestAPI_required_attributes(),
            'a' => array_merge(adforestAPI_required_attributes(), array(
                'href' => array(),
                'target' => array(
                    '_blank',
                    '_top'
                ),
            )),
            'u' => adforestAPI_required_attributes(),
            'br' => adforestAPI_required_attributes(),
            'i' => adforestAPI_required_attributes(),
            'q' => adforestAPI_required_attributes(),
            'b' => adforestAPI_required_attributes(),
            'ul' => adforestAPI_required_attributes(),
            'ol' => adforestAPI_required_attributes(),
            'li' => adforestAPI_required_attributes(),
            'br' => adforestAPI_required_attributes(),
            'hr' => adforestAPI_required_attributes(),
            'strong' => adforestAPI_required_attributes(),
            'blockquote' => adforestAPI_required_attributes(),
            'del' => adforestAPI_required_attributes(),
            'strike' => adforestAPI_required_attributes(),
            'em' => adforestAPI_required_attributes(),
            'code' => adforestAPI_required_attributes(),
            'style' => adforestAPI_required_attributes(),
            'script' => adforestAPI_required_attributes(),
            'img' => adforestAPI_required_attributes(),
        );
    }

}

if (!function_exists('adforestAPI_CustomFieldsVals')) {

    function adforestAPI_CustomFieldsVals($post_id = '', $terms = array()) {
        if ($post_id == "")
            return;
        /* $terms = wp_get_post_terms($post_id, 'ad_cats'); */
        $is_show = '';
        if (isset($terms) && $terms && count($terms) > 0) {

            foreach ($terms as $term) {
                $term_id = $term;
                $t = adforest_dynamic_templateID($term_id);
                if ($t)
                    break;
            }
            $templateID = adforest_dynamic_templateID($term_id);
            $result = get_term_meta($templateID, '_sb_dynamic_form_fields', true);

            $is_show = '';
            $html = '';

            if (isset($result) && $result != "") {
                $is_show = sb_custom_form_data($result, '_sb_default_cat_image_required');
            }
        }
        return ($is_show == 1) ? 1 : 0;
    }

}

// Bad word filter
if (!function_exists('adforestAPI_badwords_filter')) {

    function adforestAPI_badwords_filter($words = array(), $string = "", $replacement ="" ) {
        foreach ($words as $word) {
            //$string = str_replace($word, $replacement, $string); 
            /* Added Fix On 19-04-2019 */
            //$string = preg_replace('/\b'.$find.'\b/i', $replace, $string);
            $string = preg_replace('/\b' . $word . '\b/i', $replacement, $string);
        }
        return $string;
    }

}

function adforestAPIincrease_timeout_for_api_requests_27091($r, $url) {
    if (false !== strpos($url, '//api.wordpress.org/')) {
        global $adforestAPI;

        $r['timeout'] = ( isset($adforestAPI['request_timeout']) && $adforestAPI['request_timeout'] != "" ) ? $adforestAPI['request_timeout'] : 3000;
    }

    return $r;
}

add_filter('http_request_args', 'adforestAPIincrease_timeout_for_api_requests_27091', 10, 2);

// ------------------------------------------------ //
// Get and Set Post Views //
// ------------------------------------------------ //
if (!function_exists('adforestAPI_getPostViews')) {

    function adforestAPI_getPostViews($postID) {
        $postID = esc_html($postID);
        $count_key = 'sb_post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if ($count == '') {
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
            return "0";
        }
        return $count;
    }

}

if (!function_exists('adforestAPI_setPostViews')) {

    function adforestAPI_setPostViews($postID) {
        $postID = esc_html($postID);
        $count_key = 'sb_post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if ($count == '') {
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
        } else {
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    }

}
if (!function_exists('adforestAPI_updateUser_onRegister')) {

    function adforestAPI_updateUser_onRegister($uid = '') {

        if (adforestAPI_getReduxValue('sb_allow_ads', '', true)) {
            $freeAds = adforestAPI_getReduxValue('sb_free_ads_limit', '', false);
            $featured = adforestAPI_getReduxValue('sb_featured_ads_limit', '', false);
            $validity = adforestAPI_getReduxValue('sb_package_validity', '', false);

            update_user_meta($uid, '_sb_simple_ads', $freeAds);
            update_user_meta($uid, '_sb_featured_ads', $featured);

            if ($validity == '-1') {
                update_user_meta($uid, '_sb_expire_ads', $validity);
            } else {
                $expiry_date = date('Y-m-d', strtotime("+$validity days"));
                update_user_meta($uid, '_sb_expire_ads', $expiry_date);
            }
        } else {
            update_user_meta($uid, '_sb_simple_ads', 0);
            update_user_meta($uid, '_sb_featured_ads', 0);
            update_user_meta($uid, '_sb_expire_ads', date('Y-m-d'));
        }

        update_user_meta($uid, '_sb_pkg_type', 'free');
    }

}

if (!function_exists('adforestAPI_firebase_notify_func')) {

    function adforestAPI_firebase_notify_func($firebase_id = '', $message_data = array()) {
        global $adforestAPI;
        if (isset($adforestAPI['api_firebase_id']) && $adforestAPI['api_firebase_id'] != "") {
            $api_firebase_id = $adforestAPI['api_firebase_id'];
            /* if(!define('API_ACCESS_KEY')){
              define('API_ACCESS_KEY', $api_firebase_id);
              } */
            $registrationIds = $firebase_id; //array( $firebase_id );

            $msg = (isset($message_data) && count($message_data) > 0) ? $message_data : '';
            if ($msg == "") {
                return '';
            }
            $fields = array(
                'to' => $registrationIds,
                'data' => $msg,
                'notification' => $msg
            );
            $headers = array(
                'Authorization: key=' . $api_firebase_id,
                'Content-Type: application/json'
            );
            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }

}
if (!function_exists('adforestAPI_social_profiles')) {

    function adforestAPI_social_profiles() {
        global $adforestAPI;
        $social_netwroks = array();
        if (isset($adforestAPI['sb_enable_social_links']) && $adforestAPI['sb_enable_social_links']) {
            $social_netwroks = array(
                'facebook' => __('Facebook', 'adforest-rest-api'),
                'twitter' => __('Twitter', 'adforest-rest-api'),
                'linkedin' => __('Linkedin', 'adforest-rest-api'),
                'google-plus' => __('Google+', 'adforest-rest-api'),
                'instagram' => __('Instagram', 'adforest-rest-api')
            );
        }

        return $social_netwroks;
    }

}

if (!function_exists('adforestAPI_payment_types')) {

    function adforestAPI_payment_types($key = '', $type = '') {

        global $adforestAPI;
        $paypalKey = (isset($adforestAPI['appKey_paypalKey']) && $adforestAPI['appKey_paypalKey'] != "") ? $adforestAPI['appKey_paypalKey'] : '';
        $stripeSKey = (isset($adforestAPI['appKey_stripeSKey']) && $adforestAPI['appKey_stripeSKey'] != "") ? $adforestAPI['appKey_stripeSKey'] : '';
        $arr = array();

        if ($type != "ios") {
            $arr['stripe'] = __('Stripe', 'adforest-rest-api');
            
            $arr['bank_transfer'] = __('Bank Transfer', 'adforest-rest-api');
            $arr['cash_on_delivery'] = __('Cash On Delivery', 'adforest-rest-api');
            $arr['cheque'] = __('Payment By Check', 'adforest-rest-api');
            //$arr['paystack'] = __('Pay Stack', 'adforest-rest-api');
            //$arr['squareup'] = __('Square UP', 'adforest-rest-api');
            $arr['authorizedotnet'] = __('Authorize.Net', 'adforest-rest-api');
            $arr['payhere'] = __('Pay here', 'adforest-rest-api');
            $arr['braintree'] = __('PayPal & Brain Tree ', 'adforest-rest-api');
            $arr['worldpay'] = __('World Pay', 'adforest-rest-api');
        }
        $arr['app_inapp'] = __('InApp Purchase', 'adforest-rest-api');
        /* $arr['payu'] 				= __( 'PayU', 'adforest-rest-api' ); */

        return (isset($arr[$key]) && $key != "") ? $arr[$key] : $arr;
    }

}

if (!function_exists('adforestAPI_determine_minMax_latLong')) {

    function adforestAPI_determine_minMax_latLong($data_arr = array(), $check_db = true) {
        global $adforestAPI;
        $data = array();
        $user_id = get_current_user_id();
        $success = false;

        if (isset($data_arr) && !empty($data_arr)) {
            $nearby_data = $data_arr;
        } else if ($user_id && $check_db) {
            $nearby_data = get_user_meta($user_id, '_sb_user_nearby_data', true);
        }

        if (isset($nearby_data) && $nearby_data != "") {

            //array("latitude" => $latitude, "longitude" => $longitude, "distance" => $distance );
            $original_lat = $nearby_data['latitude'];
            $original_long = $nearby_data['longitude'];
            $distance = $nearby_data['distance'];

            $search_radius_type = isset($adforestAPI['search_radius_type']) ? $adforestAPI['search_radius_type'] : 'km';
            if ($search_radius_type == 'mile' && $distance > 0) {
                $distance = $distance * 1.609344;  // convert kilometer to miles 
            }
            $lat = $original_lat; //latitude
            $lon = $original_long; //longitude
            $distance = $distance; //your distance in KM
            $R = 6371; //constant earth radius. You can add precision here if you wish
            $maxLat = $lat + rad2deg($distance / $R);
            $minLat = $lat - rad2deg($distance / $R);
            $maxLon = $lon + rad2deg(asin($distance / $R) / @abs(@cos(deg2rad($lat))));
            $minLon = $lon - rad2deg(asin($distance / $R) / @abs(@cos(deg2rad($lat))));

            $data['radius'] = $R;
            $data['distance'] = $distance;
            $data['lat']['original'] = $original_lat;
            $data['long']['original'] = $original_long;

            $data['lat']['min'] = $minLat;
            $data['lat']['max'] = $maxLat;

            $data['long']['min'] = $minLon;
            $data['long']['max'] = $maxLon;
        }

        return $data;
    }

    /*

      $latitude	= (isset($json_data['nearby_latitude'])) ? $json_data['nearby_latitude'] : '';
      $longitude	= (isset($json_data['nearby_longitude'])) ? $json_data['nearby_longitude'] : '';
      $distance	= (isset($json_data['nearby_distance'])) ? $json_data['nearby_distance'] : '20';
      if( $latitude != "" && $longitude != "" )
      {
      $data_array = array("latitude" => $latitude, "longitude" => $longitude, "distance" => $distance );
      adforestAPI_determine_minMax_latLong();

      $data_array = array("latitude" => '21212121212', "longitude" => '212121212121', "distance" => '100' );
      $data = adforestAPI_determine_minMax_latLong($data_array);

      }

      nearby_
     */
}

if (!function_exists('adforestAPI_get_all_countries')) {

    function adforestAPI_get_all_countries() {
        $args = array(
            'posts_per_page' => - 1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_type' => '_sb_country',
            'post_status' => 'publish',
        );
        $countries = get_posts($args);
        $res = array();
        foreach ($countries as $country) {
            $stripped = trim(preg_replace('/\s+/', ' ', $country->post_excerpt));
            $res[$stripped] = $country->post_title;
        }
        return $res;
    }

}
if (!function_exists('adforestAPI_randomString')) {

    function adforestAPI_randomString($length = 50) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

}
/* Shop Settings */
add_action('pre_get_posts', 'adforestAPI_shop_filter_cat');
if (!function_exists('adforestAPI_shop_filter_cat')) {

    function adforestAPI_shop_filter_cat($query) {
        if (!is_admin() && is_post_type_archive('product') && $query->is_main_query()) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'adforest_classified_pkgs',
                    'operator' => 'NOT IN'
                ),
            ));
        }
    }

}

if (!function_exists('adforestAPI_showPhone_to_users')) {

    function adforestAPI_showPhone_to_users() {
        global $adforestAPI;

        $restrict_phone_show = ( isset($adforestAPI['restrict_phone_show']) ) ? $adforestAPI['restrict_phone_show'] : 'all';
        $is_show_phone = false;
        if ($restrict_phone_show == "login_only") {
            $is_show_phone = true;
            if (is_user_logged_in()) {
                $is_show_phone = false;
            }
        }

        return $is_show_phone;
    }

}

if (!function_exists('adforestAPI_cast_decimal_precision_filter')) {

    function adforestAPI_cast_decimal_precision_filter($array) {
        $array['where'] = str_replace('DECIMAL', 'DECIMAL(10,3)', $array['where']);
        return $array;
    }

}

if (!function_exists('adforestAPI_removeTheme_header_footer')) {

    function adforestAPI_removeTheme_header_footer() {

        $shop_request = adforestAPI_getSpecific_headerVal('Adforest-Shop-Request');
        if ($shop_request == 'body') {
            remove_action('adforestAction_header_content', 'adforest_header_content_html');
            remove_action('adforestAction_footer_content', 'adforest_footer_content_html');
            remove_action('adforestAction_app_notifier', 'adforest_app_notifier_html');
        }
    }

}
add_action('init', 'adforestAPI_removeTheme_header_footer');


// Email on ad publish
add_action('transition_post_status', 'adforestAPI_sb_send_mails_on_publish', 10, 3);
if (!function_exists('adforestAPI_sb_send_mails_on_publish')) {

    function adforestAPI_sb_send_mails_on_publish($new_status, $old_status, $post) {
        if ('publish' !== $new_status or 'publish' === $old_status or 'ad_post' !== get_post_type($post))
            return;

        $my_theme = wp_get_theme();
        if ($my_theme->get('Name') != 'adforest' && $my_theme->get('Name') != 'adforest child') {
            global $adforestAPI;
            if (isset($adforestAPI['email_on_ad_approval']) && $adforestAPI['email_on_ad_approval']) {
                adforestAPI_get_notify_on_ad_approval($post);
            }
        }
    }

}

if (!function_exists('adforestAPI_youtube_url_to_iframe')) {

    function adforestAPI_youtube_url_to_iframe($string = '') {
        $pattern = '@(http|https)://(www\.)?youtu[^\s]*@i';
        //This was just for test
        //$string = "abc def http://www.youtube.com/watch?v=t-ZRX8984sc ghi jkm";
        $matches = array();
        preg_match_all($pattern, $string, $matches);
        foreach ($matches[0] as $matchURL) {

            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $matchURL, $match);
            $vID = (isset($match[1]) && $match[1] != "") ? $match[1] : '';
            $vidURL = 'https://www.youtube.com/embed/' . $vID;

            $string = str_replace($matchURL, '<iframe src="' . esc_url($vidURL) . '" frameborder="0" allowfullscreen style="width:100%;"></iframe>', $string);
        }
        return $string;
    }

}
if (!function_exists('adforestAPI_verify_sms_gateway')) {

    function adforestAPI_verify_sms_gateway() {
        global $adforestAPI;
        $gateway = '';
        if (isset($adforestAPI['sb_phone_verification']) && $adforestAPI['sb_phone_verification'] && in_array('wp-twilio-core/core.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $gateway = 'twilio';
        } else if (isset($adforestAPI['sb_phone_verification']) && $adforestAPI['sb_phone_verification'] && in_array('wp-iletimerkezi-sms/core.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $gateway = 'iletimerkezi-sms';
        }

        return $gateway;
    }

}

if (!function_exists('adforestAPI_check_if_phoneVerified')) {

    function adforestAPI_check_if_phoneVerified($user_id = 0) {
        global $adforestAPI;
        $verifed_phone_number = false;
        if (isset($adforestAPI['sb_phone_verification']) && $adforestAPI['sb_phone_verification']) {
            if (isset($adforestAPI['sb_new_user_sms_verified_can']) && $adforestAPI['sb_new_user_sms_verified_can'] == true) {
                $user_id = ($user_id) ? $user_id : get_current_user_id();
                if (get_user_meta($user_id, '_sb_is_ph_verified', true) != '1') {
                    get_user_meta($user_id, '_sb_is_ph_verified', true);
                    $verifed_phone_number = true;
                }
            }
        }
        return $verifed_phone_number;
    }
}

/* if (!function_exists('adforest_authorization_htaccess_contents'))
  {
  function adforest_authorization_htaccess_contents( $rules )
  {
  $my_content = <<<EOD
  \n# BEGIN ADDING ADFOREST Authorization
  SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0
  # END ADDING ADFOREST Authorization\n
  EOD;
  return $rules .$my_content;
  }
  }
  add_filter('mod_rewrite_rules', 'adforest_authorization_htaccess_contents'); */