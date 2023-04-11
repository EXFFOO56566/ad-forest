<?php
add_filter('adforestAPI_check_bid_availability', 'adforestAPI_check_bid_availability_callback', 10, 2);
add_filter('adforestAPI_site_location_args', 'adforestAPI_site_location_args_callback', 10, 2);
add_filter('AdforestAPI_check_category_price_avl', 'AdforestAPI_check_category_price_avl_callback', 10, 1);
add_filter('AdforestAPI_verified_phone_ad_posting', 'AdforestAPI_verified_phone_ad_posting_callback', 10, 1);
add_filter('AdforestAPI_category_has_parent', 'AdforestAPI_category_has_parent_callback', 10, 1);
function adforestAPI_check_bid_availability_callback($bid_categories = true, $cat_id = 0) {
    global $adforestAPI;

    $_sb_allow_bidding = get_user_meta(get_current_user_id(), '_sb_allow_bidding', true);
    $sb_enable_comments_offer = isset($adforestAPI['sb_enable_comments_offer']) ? $adforestAPI['sb_enable_comments_offer'] : false;
    if (!$sb_enable_comments_offer) { /// check bidding is enable or not
        return false;
    }
    if (isset($_sb_allow_bidding) && !empty($_sb_allow_bidding) && $_sb_allow_bidding != '-1' && $_sb_allow_bidding <= 0) {
        $bid_categories = false;
    } else {
        $sb_make_bid_categorised = isset($adforestAPI['sb_make_bid_categorised']) ? $adforestAPI['sb_make_bid_categorised'] : true;
        $bid_categorised_type = isset($adforestAPI['bid_categorised_type']) ? $adforestAPI['bid_categorised_type'] : 'all';
        if ($sb_make_bid_categorised && $bid_categorised_type == 'selective') {
            $cat_id = isset($cat_id) && !empty($cat_id) ? $cat_id : 0;
            $bid_cat_base = get_term_meta($cat_id, 'adforest_make_bid_cat_base', true);
            if (isset($bid_cat_base) && $bid_cat_base == 'yes') {
                $bid_categories = true;
            } else {
                $bid_categories = false;
            }
        } else {
            $bid_categories = true;
        }
    }
    return $bid_categories;
}

function adforestAPI_site_location_args_callback($loc_args, $arg_type = 'search') {
    global $adforestAPI;

    if (empty($loc_args)) {
        $loc_args = '';
    }
    
    if (isset($adforestAPI["app_top_location"]) && $adforestAPI["app_top_location"]) {
       
        $adforest_site_location_id = adforestAPI_getSpecific_headerVal('Adforest-Location-Id');
        //$adforest_site_location_id = isset($adforestAPI["adforest_site_location_id"]) && $adforestAPI["adforest_site_location_id"] != '' ? $adforestAPI["adforest_site_location_id"] : '';

        if ($adforest_site_location_id != "" && $adforest_site_location_id != "0") {
            $loc_args = isset($loc_args) && !empty($loc_args) ? $loc_args : array();
            if ($arg_type == 'ads') {
                $loc_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'ad_country',
                        'field' => 'term_id',
                        'terms' => $adforest_site_location_id,
                    ),
                );
            } else {
                $loc_args[] = array(
                    'taxonomy' => 'ad_country',
                    'field' => 'term_id',
                    'terms' => $adforest_site_location_id,
                );
            }
        }
    }

    return $loc_args;
}

//function AdforestAPI_check_category_price_avl_callback($paid_category = false, $cat_id = 0) {
//
//    $cat_id = isset($cat_id) && !empty($cat_id) ? $cat_id : 0;
//    if ($cat_id != 0) {
//
//        $adforest_make_cat_paid = get_term_meta($cat_id, 'adforest_make_cat_paid', true);
//        $paid_category = FALSE;
//        if (isset($adforest_make_cat_paid) && $adforest_make_cat_paid == 'yes') {
//            $paid_category = TRUE;
//        }
//        $selected_categories = get_user_meta(get_current_user_id(), "package_allow_categories", true);
//        $selected_categories = isset($selected_categories) && !empty($selected_categories) ? $selected_categories : '';
//        $selected_categories_arr = array();
//
//        $category_package_flag = FALSE;
//
//        if ($selected_categories == '') {    // scanerio 1  select paid category but package is empty
//            if ($paid_category) {
//                $category_package_flag = TRUE; // display package error
//            }
//        }
//
//        if ($selected_categories == 'all') {    // scanerio 2  select Any category but package selection is all
//            $category_package_flag = FALSE; // display package free
//        }
//
//        if ($selected_categories != '' && $selected_categories != 'all') { // selected category is not in buy package or/not
//            $selected_categories_arr = explode(",", $selected_categories);
//            if ($paid_category) {
//                if (!in_array($cat_id, $selected_categories_arr)) {
//                    $category_package_flag = TRUE; // display package error
//                }
//            }
//        }
//
//        return $category_package_flag;
//    } else {
//        return $paid_category;
//    }
//}


function AdforestAPI_check_category_price_avl_callback($cat_id = 0) {

    /*
     * for package base categories
     */
    //$cat_id = $ad_trm->term_id;

    if ($cat_id == 0)
        return $cat_id;


    global $adforest_theme, $adforestAPI;


    $adforest_theme = wp_get_theme();
    if ($adforest_theme->get('Name') != 'adforest' && $adforest_theme->get('Name') != 'adforest child') {
        $cat_pkg_type = isset($adforestAPI['cat_pkg_type']) && $adforestAPI['cat_pkg_type'] != '' ? $adforestAPI['cat_pkg_type'] : 'parent';
    } else {
        $cat_pkg_type = isset($adforest_theme['cat_pkg_type']) && $adforest_theme['cat_pkg_type'] != '' ? $adforest_theme['cat_pkg_type'] : 'parent';
    }


    $parent_child_pkg_flag = FALSE;

    if ($cat_pkg_type == 'child') {
        $parent_child_pkg_flag = TRUE;
    } else {
        if (!apply_filters('AdforestAPI_category_has_parent', $cat_id)) { // applied only in parent paid categories
            $parent_child_pkg_flag = TRUE;
        }
    }

    if ($parent_child_pkg_flag) {
        $cat_pkg_data = array();
        $adforest_make_cat_paid = get_term_meta($cat_id, 'adforest_make_cat_paid', true);
        $paid_category = FALSE;
        if (isset($adforest_make_cat_paid) && $adforest_make_cat_paid == 'yes') {
            $paid_category = TRUE;
        }
        $selected_categories = get_user_meta(get_current_user_id(), "package_allow_categories", true);
        $selected_categories = isset($selected_categories) && !empty($selected_categories) ? $selected_categories : '';
        $selected_categories_arr = array();
        $category_package_flag = FALSE;
        if ($selected_categories == '') {    // scanerio 1  select paid category but package is empty
            if ($paid_category) {
                $category_package_flag = TRUE; // display package error
            }
        }
        if ($selected_categories == 'all') {    // scanerio 2  select Any category but package selection is all
            $category_package_flag = FALSE; // display package free
        }
        if ($selected_categories != '' && $selected_categories != 'all') { // selected category is not in buy package or/not
            $selected_categories_arr = explode(",", $selected_categories);
            if ($paid_category) {
                if (!in_array($cat_id, $selected_categories_arr)) {
                    $category_package_flag = TRUE; // display package error
                }
            }
        }
        $can_post = TRUE;
        if ($category_package_flag) {
            $can_post = FALSE;
        }

        $cat_pkg_data['can_post'] = $can_post;
        $cat_pkg_data['is_paid'] = $paid_category;

        return $cat_pkg_data;
    } else {
        return $cat_id;
    }
}

function AdforestAPI_category_has_parent_callback($catid) {
    $category = get_term($catid);
    if ($category->parent > 0) {
        return true;
    }
    return false;
}

if (!function_exists('AdforestAPI_verified_phone_ad_posting_callback')) {

    function AdforestAPI_verified_phone_ad_posting_callback($can_post = TRUE) {
        global $adforestAPI;
        if (is_user_logged_in()) {

            $enable_phone_verification = isset($adforestAPI['sb_phone_verification']) && $adforestAPI['sb_phone_verification'] ? True : FALSE;
            $ad_post_with_phone_verification = isset($adforestAPI['ad_post_restriction']) && $adforestAPI['ad_post_restriction'] == 'phn_verify' ? True : FALSE;
            if ($enable_phone_verification && $ad_post_with_phone_verification) {
                $user_id = get_current_user_id();
                if (get_user_meta($user_id, '_sb_is_ph_verified', true) != '1') {
                    $can_post = FALSE;
                }
            }
        }
        return $can_post;
    }

}

$adforest_theme = wp_get_theme();
if ($adforest_theme->get('Name') != 'adforest' && $adforest_theme->get('Name') != 'adforest child') {


    /* AdForest Custom Package */
    if (!function_exists('adforestAPI_register_custom_packages')) {

        function adforestAPI_register_custom_packages() {
            if (class_exists('WooCommerce')) {

                if (!class_exists('WC_Product_adforest_custom_packages')) {

                    class WC_Product_adforest_custom_packages extends WC_Product {

                        public $product_type = 'adforest_classified_pkgs';

                        public function __construct($product) {
                            parent::__construct($product);
                        }

                    }

                }
            }
        }

    }
    add_action('init', 'adforestAPI_register_custom_packages', 1);

    if (!function_exists('adforestAPI_add_packages_type')) {

        function adforestAPI_add_packages_type($types) {
            // Key should be exactly the same as in the class product_type parameter
            $types['adforest_classified_pkgs'] = __('AdForest Packages', 'adforest');
            return $types;
        }

    }
    add_filter('product_type_selector', 'adforestAPI_add_packages_type', 1);

//class for custom product type
    if (!function_exists('adforestAPI_woocommerce_product_class')) {

        function adforestAPI_woocommerce_product_class($classname, $product_type) {
            if ($product_type == 'adforest_classified_pkgs') { // notice the checking here.
                $classname = 'WC_Product_adforest_custom_packages';
            }
            return $classname;
        }

    }
    add_filter('woocommerce_product_class', 'adforestAPI_woocommerce_product_class', 10, 2);
    /*     * * Show pricing fields for simple_rental product. */
    if (!function_exists('adforestAPI_render_package_custom_js')) {

        function adforestAPI_render_package_custom_js() {

            if ('product' != get_post_type()) :
                return;
            endif;
            ?><script type='text/javascript'>
                jQuery(document).ready(function () {
                    jQuery('#sb_thmemes_adforest_metaboxes').hide();
                    jQuery('.options_group.pricing').addClass('show_if_adforest_classified_pkgs').show();
                    jQuery('#product-type').on('change', function ()
                    {
                        if (jQuery(this).val() == 'adforest_classified_pkgs')
                        {
                            jQuery('#sb_thmemes_adforest_metaboxes').show();
                        } else
                        {
                            jQuery('#sb_thmemes_adforest_metaboxes').hide();
                        }
                    });
                    jQuery('#product-type').trigger('change');
                });

            </script><?php
        }

    }
    add_action('admin_footer', 'adforestAPI_render_package_custom_js');

    if (!function_exists('adforestAPI_hide_attributes_data_panel')) {

        function adforestAPI_hide_attributes_data_panel($tabs) {
            $tabs['attribute']['class'][] = 'hide_if_adforest_classified_pkgs';
            $tabs['shipping']['class'][] = 'hide_if_adforest_classified_pkgs';
            $tabs['linked_product']['class'][] = 'hide_if_adforest_classified_pkgs';
            $tabs['advanced']['class'][] = 'hide_if_adforest_classified_pkgs';
            return $tabs;
        }

    }
    add_filter('woocommerce_product_data_tabs', 'adforestAPI_hide_attributes_data_panel');
}

function AdForestAPI_action_redux_options_args_opt_name_saved( $this_options, $this_transients_changed_values ) { 

    $array_keys = array();
    $array_keys_values = array();
    $array_keys_values_new = array();
    if( isset($this_transients_changed_values['appKey_pCode']) && $this_transients_changed_values['appKey_pCode'] != "")
    {   
        $array_keys['api_is_buy_android_app'] = 'appKey_pCode';
        $array_keys_values['appKey_pCode'] = $this_transients_changed_values['appKey_pCode'];
         $array_keys_values_new['appKey_pCode'] = $this_options['appKey_pCode'];
    }
    if( isset($this_transients_changed_values['appKey_pCode_ios'])  && $this_transients_changed_values['appKey_pCode_ios'] != "")
    {
         $array_keys['api_is_buy_ios_app'] = 'appKey_pCode_ios';
         $array_keys_values['appKey_pCode_ios'] = $this_transients_changed_values['appKey_pCode_ios'];
         $array_keys_values_new['appKey_pCode_ios'] = $this_options['appKey_pCode_ios'];
    }        

    if(isset($array_keys) && count($array_keys) > 0)
    {
        $app_keyname = array("_", "s", "b", "_", "p", "u", "r", "c", "h", "a", "s", "e", "_", "c", "o", "d", "e");
        $kyname = implode($app_keyname);
        $app_keynamelink = array("h", "t", "t", "p", "s", ":", "/", "/", "a", "u", "t", "h", "e", "n", "t", "i", "c", "a", "t", "e", ".", "s", "c", "r", "i", "p", "t", "s", "b", "u", "n", "d", "l", "e", ".", "c", "o", "m", "/", "a", "d", "f", "o", "r", "e", "s", "t", "/", "v", "e", "r", "i", "f", "y", "_", "p", "c", "o", "d", "e", ".", "p", "h", "p");
        $app_keynameUrl = implode($app_keynamelink);
            foreach ($array_keys as $key => $value) {
               
                    $key_value = (isset($array_keys_values[$value]) && $array_keys_values[$value] != "") ? $array_keys_values[$value] : "";
                    $key_value_new = (isset($array_keys_values_new[$value]) && $array_keys_values_new[$value] != "") ? $array_keys_values_new[$value] : "";
                    $app_name = ($key == "api_is_buy_ios_app") ? "ios" : "android";
                    if($key_value_new != "" && $key_value_new != $key_value){
                        $db_value = get_option($key);
                        if($db_value == $key_value_new ) return;
                        $theme_name = "Adforest - " . $app_name;
                        $data = "?purchase_code=" . $key_value_new . "&id=" . get_option('admin_email') . '&url=' . get_option('siteurl') . '&theme_name=' . $theme_name;
                        $url = esc_url($app_keynameUrl) . $data;
                        $response = @wp_remote_get($url);
                        if (is_array($response) && !is_wp_error($response)) {
                            update_option($key, $key_value_new);
                        }
                    }
            }
    }
}
add_action( "redux/options/adforestAPI/saved", 'AdForestAPI_action_redux_options_args_opt_name_saved', 10, 2 );