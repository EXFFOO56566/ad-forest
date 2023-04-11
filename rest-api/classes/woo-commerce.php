<?php
/* ----	Woo Products Starts Here	 ----- */
add_action('rest_api_init', 'adforestAPI_packages_get_hook', 0);

function adforestAPI_packages_get_hook() {
    register_rest_route(
            'adforest/v1', '/packages/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_packages_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

add_action('rest_api_init', 'adforestAPI_empty_cart_on_app_logout', 0);

function adforestAPI_empty_cart_on_app_logout() {
    register_rest_route(
            'adforest/v1', '/cart-empty/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_cart_empty',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_cart_empty')) {

    function adforestAPI_cart_empty() {

        $success = FALSE;
        $message = __('Cart Items Exists.', 'adforest-rest-api');
/*       if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
           if (class_exists('WooCommerce')) {
               foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                   WC()->cart->remove_cart_item($cart_item_key);
               }
               $success = TRUE;
               $message = __('Cart Empty.', 'adforest-rest-api');
           }
       }*/

        $response = array('success' => $success, 'message' => $message);
        return $response;
    }

}

if (!function_exists('adforestAPI_packages_get')) {

    function adforestAPI_packages_get() {
        $user = wp_get_current_user();
        $user_id = @$user->data->ID;
        $pdata = array();
        $products = array();
        global $adforestAPI;
        $message = '';
        $success = true;

        $yes_no_arr = array(
            'yes' => __('Yes', 'adforest-rest-api'),
            'no' => __('No', 'adforest-rest-api'),
        );


        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

            if (isset($adforestAPI['api_woo_products_multi'])) {
                $productsData = $adforestAPI['api_woo_products_multi'];
                if (count($productsData) > 0) {
                    foreach ($productsData as $product) {

                        $pdata = array();
                        $product = apply_filters('AdforestAPI_language_page_id', $product);
                        $productData = new WC_Product($product);




                        $unlimited_text = __('Unlimited', 'adforest-rest-api');
                        $pdata['color'] = 'light';
                        if (get_post_meta($product, 'package_bg_color', true) == 'dark')
                            $pdata['color'] = 'dark';
                        $pdata['days_text'] = __('Validity', 'adforest-rest-api');
                        $pdata['days_value'] = '0';

                        if (get_post_meta($product, 'package_expiry_days', true) == "-1") {
                            $pdata['days_value'] = __('Lifetime', 'adforest-rest-api');
                        } else if (get_post_meta($product, 'package_expiry_days', true) != "") {
                            $pdata['days_value'] = get_post_meta($product, 'package_expiry_days', true) . ' ' . __('Days', 'adforest-rest-api');
                        }
                        $pdata['free_ads_text'] = __('Free Ads', 'adforest-rest-api');
                        $pdata['free_ads_value'] = '0';
                        if (get_post_meta($product, 'package_free_ads', true) != "") {

                            if (get_post_meta($product, 'package_free_ads', true) == '-1') {
                                $freeValue = $unlimited_text;
                            } else {
                                $freeValue = get_post_meta($product, 'package_free_ads', true);
                            }

                            $pdata['free_ads_value'] = $freeValue;
                        }

                        if (get_post_meta($product, 'package_allow_bidding', true) != "") {
                            $pdata['allow_bidding_text'] = __('Allowed Bidding', 'adforest-rest-api');
                            $package_allow_bidding = get_post_meta($product, 'package_allow_bidding', true);
                            $package_allow_bidding = isset($package_allow_bidding) ? $package_allow_bidding : '';
                            $package_allow_bidding = $package_allow_bidding == -1 ? __('Unlimited', 'adforest') : $package_allow_bidding;
                            $pdata['allow_bidding_value'] = $package_allow_bidding;
                        }

                        if (get_post_meta($product, 'package_num_of_images', true) != "") {
                            $pdata['num_of_images_text'] = __('Num of Images', 'adforest-rest-api');
                            $package_num_of_images = get_post_meta($product, 'package_num_of_images', true);
                            $package_num_of_images = isset($package_num_of_images) ? $package_num_of_images : '';
                            $package_num_of_images = $package_num_of_images == -1 ? __('Unlimited', 'adforest') : $package_num_of_images;
                            $pdata['num_of_images_val'] = $package_num_of_images;
                        }

                        if (get_post_meta($product, 'package_video_links', true) != "") {
                            $pdata['video_url_text'] = __('Video URL', 'adforest-rest-api');
                            $package_video_links = get_post_meta($product, 'package_video_links', true);
                            $package_video_links = isset($package_video_links) ? $package_video_links : 'yes';
                            $pdata['video_url_val'] = $yes_no_arr[$package_video_links];
                        }

                        if (get_post_meta($product, 'package_allow_tags', true) != "") {
                            $pdata['allow_tags_text'] = __('Allow Tags', 'adforest-rest-api');
                            $package_allow_tags = get_post_meta($product, 'package_allow_tags', true);
                            $package_allow_tags = isset($package_allow_tags) ? $package_allow_tags : 'yes';
                            $pdata['allow_tags_val'] = $yes_no_arr[$package_allow_tags];
                        }

                        if (get_post_meta($product, 'package_allow_categories', true) != "") {
                            $selected_categories = get_post_meta($product, "package_allow_categories", true);
                            $selected_categories = isset($selected_categories) && !empty($selected_categories) ? $selected_categories : '';
                            $selected_categories_arr = array();
                            if ($selected_categories != '') {
                                $selected_categories_arr = explode(",", $selected_categories);
                            }
                            if (isset($selected_categories_arr) && !empty($selected_categories_arr) && is_array($selected_categories_arr)) {
                                if (isset($selected_categories_arr[0]) && $selected_categories_arr[0] != 'all') {
                                    $cat_list_ = array();
                                    $cat_list = array();

                                    $count = 1;
                                    foreach ($selected_categories_arr as $single_cat_id) {
                                        $category = get_term($single_cat_id);
                                        $cat_list['cat_id'] = $single_cat_id;
                                        $cat_list['cat_name'] = html_entity_decode($category->name);
                                        $count++;
                                        $cat_list_[] = $cat_list;
                                    }
                                    $pdata['allow_cats_val_arr'] = $cat_list_;
                                }
                            }
                            if (isset($selected_categories_arr[0]) && $selected_categories_arr[0] == 'all') {
                                $cat_list_ = __('All', 'adforest-rest-api');
                                $pdata['allow_cats_val'] = $cat_list_;
                            }

                            $pdata['allow_cats_text'] = __('Allow Categories', 'adforest-rest-api');
                        }

                        $pdata['featured_ads_text'] = __('Featured Ads', 'adforest-rest-api');
                        $pdata['featured_ads_value'] = '0';
                        if (get_post_meta($product, 'package_featured_ads', true) != "") {

                            if (get_post_meta($product, 'package_featured_ads', true) == '-1') {
                                $fValue = $unlimited_text;
                            } else {
                                $fValue = get_post_meta($product, 'package_featured_ads', true);
                            }
                            $pdata['featured_ads_value'] = $fValue;
                        }

                        $pdata['bump_ads_text'] = __('Bump up Ads', 'adforest-rest-api');
                        $pdata['bump_ads_value'] = '0';
                        if (get_post_meta($product, 'package_bump_ads', true) != "") {
                            if (get_post_meta($product, 'package_bump_ads', true) == '-1') {
                                $bValue = $unlimited_text;
                            } else {
                                $bValue = get_post_meta($product, 'package_bump_ads', true);
                            }

                            $pdata['bump_ads_value'] = $bValue;
                        }

                        $pdata['product_id'] = $product."";
                        $pdata['product_title'] = get_the_title($product);
                       // $pdata ['product_desc']   =  $productData->get_short_description();
                        $pdata['product_price'] = html_entity_decode(strip_tags(wc_price($productData->get_price())));
                        $pdata['product_amount']['value'] = html_entity_decode(strip_tags($productData->get_price()));
                        $pdata['product_amount']['currency'] = html_entity_decode(strip_tags(wc_price($productData->get_price())));
                        $pdata['product_link'] = get_the_permalink($product);
                        $pdata['product_qty'] = 1;
                        $pdata['product_btn'] = __('Select Plan', 'adforest-rest-api');
                        $pdata['payment_types_value'] = __('Select Option', 'adforest-rest-api');
                        $pdata["see_all_cats"] = __('See All', 'adforest-rest-api');
                        /* Get Android and IOS Product code Starts */
                        $pdata['product_appCode']['android'] = get_post_meta($product, 'package_product_code_android', true);
                        $pdata['product_appCode']['ios'] = get_post_meta($product, 'package_product_code_ios', true);
                        $pdata['product_appCode']['message'] = __('InApp purchase not available for this product.', 'adforest-rest-api');


                        if ($productData->is_on_sale()) {
                            $pdata['is_sale'] = true;
                            $pdata['sale_text'] = __('Sale', 'adforest-rest-api');
                            $pdata['regular_price'] = $productData->get_regular_price();
                        }


                        /* Get Android and IOS Product code Ends */
                        $products[] = $pdata;
                    }
                } else {
                    $success = false;
                    $message = __("No Product Found", "adforest-rest-api");
                }
            } else {
                $success = false;
                $message = __("No Product Found", "adforest-rest-api");
            }
        } else {
            $success = false;
            $message = __("No Product Found", "adforest-rest-api");
        }
        $data["products"] = $products;
        $methods = array();
        $methods[] = array("key" => "", "value" => __('Select Option', 'adforest-rest-api'));
        if (ADFOREST_API_REQUEST_FROM == 'ios') {
            $paymentPackages = ( isset($adforestAPI['api-payment-packages-ios']) && count($adforestAPI['api-payment-packages-ios']) > 0 ) ? $adforestAPI['api-payment-packages-ios'] : array();
        } else {
            $paymentPackages = ( isset($adforestAPI['api-payment-packages']) && count($adforestAPI['api-payment-packages']) > 0 ) ? $adforestAPI['api-payment-packages'] : array();
        }

        if (isset($paymentPackages) && count($paymentPackages) > 0) {
            foreach ($paymentPackages as $type) {
                $name = adforestAPI_payment_types($type);
                if ($name != "") {
                    $methods[] = array("key" => $type, "value" => $name);
                }
            }
        }

        $data["payment_types"] = $methods;
        $extra["page_title"] = __('Packages', 'adforest-rest-api');


        $extra["billing_error"] = __('something went wrong while billing your account.', 'adforest-rest-api');
        /* Paypal Account Currency Settings Starts */
        $paypalKey = ( isset($adforestAPI['appKey_paypalKey']) && $adforestAPI['appKey_paypalKey'] != "" ) ? $adforestAPI['appKey_paypalKey'] : '';
        $merchant_name = ( isset($adforestAPI['paypalKey_merchant_name']) && $adforestAPI['paypalKey_merchant_name'] != "" ) ? $adforestAPI['paypalKey_merchant_name'] : '';

        $paypal_currency = ( isset($adforestAPI['paypalKey_currency']) && $adforestAPI['paypalKey_currency'] != "" ) ? $adforestAPI['paypalKey_currency'] : '';
        $privecy_url = ( isset($adforestAPI['paypalKey_privecy_url']) && $adforestAPI['paypalKey_privecy_url'] != "" ) ? $adforestAPI['paypalKey_privecy_url'] : '';
        $agreement_url = ( isset($adforestAPI['paypalKey_agreement']) && $adforestAPI['paypalKey_agreement'] != "" ) ? $adforestAPI['paypalKey_agreement'] : '';

        $appKey_paypalMode = ( isset($adforestAPI['appKey_paypalMode']) && $adforestAPI['appKey_paypalMode'] != "" ) ? $adforestAPI['appKey_paypalMode'] : 'live';

        $has_key = ( $paypalKey == "" ) ? false : true;
        $data["is_paypal_key"] = $has_key;
        if ($has_key == true) {
            $data["paypal"]["mode"] = $appKey_paypalMode;
            $data["paypal"]["api_key"] = $paypalKey;
            $data["paypal"]["merchant_name"] = $merchant_name;
            $data["paypal"]["currency"] = $paypal_currency;
            $data["paypal"]["privecy_url"] = $privecy_url;
            $data["paypal"]["agreement_url"] = $agreement_url;
        }

        /* Android All InApp Settings */
        $inappAndroid = (isset($adforestAPI['inApp_androidSecret']) && $adforestAPI['inApp_androidSecret'] != "" ) ? $adforestAPI['inApp_androidSecret'] : '';
        $inappAndroid_on = (isset($adforestAPI['api-inapp-android-app']) && $adforestAPI['api-inapp-android-app'] ) ? true : false;
        $extra['android']['title_text'] = __('InApp Purchases', 'adforest-rest-api');
        $extra['android']['in_app_on'] = $inappAndroid_on;
        $extra['android']['secret_code'] = $inappAndroid; /* Secret code */
        $extra['android']['message']['no_market'] = __('Play Market app is not installed.', 'adforest-rest-api');
        $extra['android']['message']['one_time'] = __('One Time Purchase not Supported on your Device.', 'adforest-rest-api');
        /* IOS All InApp Settings */
        $inappIos = (isset($adforestAPI['inApp_iosSecret']) && $adforestAPI['inApp_iosSecret'] != "" ) ? $adforestAPI['inApp_iosSecret'] : '';
        $iosInApp_on = (isset($adforestAPI['api-inapp-ios-app']) && $adforestAPI['api-inapp-ios-app'] ) ? true : false;
        $extra['ios']['title_text'] = __('InApp Purchases', 'adforest-rest-api');
        $extra['ios']['in_app_on'] = $iosInApp_on;
        $extra['ios']['secret_code'] = $inappIos; /* Secret code */
        /* Paypal Account Currency Settings Ends */
        $response = array('success' => $success, 'data' => $data, 'message' => $message, 'extra' => $extra);
        return $response;
    }

}

/* When Order Completed By Admin starts */
$adforest_theme = wp_get_theme();
if ($adforest_theme->get('Name') != 'adforest' && $adforest_theme->get('Name') != 'adforest child') {
    add_action('woocommerce_order_status_completed', 'adforestAPI_after_payment');
}
if (!function_exists('adforestAPI_after_payment')) {

    function adforestAPI_after_payment($order_id) {
        global $adforestAPI;
        $order = new WC_Order($order_id);
        /* Get user Id From Order */
        $uid = get_post_meta($order_id, '_customer_user', true);
        $items = $order->get_items();
        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $product_type = wc_get_product($product_id);
            if (isset($adforestAPI['shop-turn-on']) && $adforestAPI['shop-turn-on'] && $product_type->get_type() != 'adforest_classified_pkgs') {
                continue;
            }


            /*
             * new features added start
             */
            $package_video_links = get_post_meta($product_id, 'package_video_links', true);
            $num_of_images = get_post_meta($product_id, 'package_num_of_images', true);
            $package_allow_tags = get_post_meta($product_id, 'package_allow_tags', true);
            $package_allow_bidding = get_post_meta($product_id, 'package_allow_bidding', true);
            $package_allow_categories = get_post_meta($product_id, 'package_allow_categories', true);

            $package_ad_expiry_days = get_post_meta($product_id, 'package_ad_expiry_days', true);
            $package_adFeatured_expiry_days = get_post_meta($product_id, 'package_adFeatured_expiry_days', true);

            update_user_meta($uid, '_sb_video_links', $package_video_links);
            update_user_meta($uid, '_sb_allow_tags', $package_allow_tags);
            update_user_meta($uid, 'package_allow_categories', $package_allow_categories);
            update_user_meta($uid, 'package_ad_expiry_days', $package_ad_expiry_days);
            update_user_meta($uid, 'package_adFeatured_expiry_days', $package_adFeatured_expiry_days);

            if ($num_of_images == '-1') {
                update_user_meta($uid, '_sb_num_of_images', $num_of_images);
            } else if (is_numeric($num_of_images) && $num_of_images != 0) {
                update_user_meta($uid, '_sb_num_of_images', $num_of_images);
            }
            if ($package_allow_bidding == '-1') {
                update_user_meta($uid, '_sb_allow_bidding', $package_allow_bidding);
            } else if (is_numeric($package_allow_bidding) && $package_allow_bidding != 0) {
                $already_stored_biddings = get_user_meta($uid, '_sb_allow_bidding', true);
                if ($already_stored_biddings != '-1') {
                    $new_bidding_count = $package_allow_bidding + $already_stored_biddings;
                    update_user_meta($uid, '_sb_allow_bidding', $new_bidding_count);
                } else if ($already_stored_biddings == '-1') {
                    update_user_meta($uid, '_sb_allow_bidding', $package_allow_bidding);
                }
            }

            /*
             * new features added end
             */

            $ads = get_post_meta($product_id, 'package_free_ads', true);
            $featured_ads = get_post_meta($product_id, 'package_featured_ads', true);
            $bump_ads = get_post_meta($product_id, 'package_bump_ads', true);
            $days = get_post_meta($product_id, 'package_expiry_days', true);

            update_user_meta($uid, '_sb_pkg_type', get_the_title($product_id));
            if ($ads == '-1') {
                update_user_meta($uid, '_sb_simple_ads', '-1');
            } else if (is_numeric($ads) && $ads != 0) {
                $simple_ads = get_user_meta($uid, '_sb_simple_ads', true);
                if ($simple_ads != '-1') {
                    $simple_ads = $simple_ads;
                    $new_ads = $ads + $simple_ads;
                    update_user_meta($uid, '_sb_simple_ads', $new_ads);
                } else if ($simple_ads == '-1') {
                    update_user_meta($uid, '_sb_simple_ads', $ads);
                }
            }
            if ($featured_ads == '-1') {
                update_user_meta($uid, '_sb_featured_ads', '-1');
            } else if (is_numeric($featured_ads) && $featured_ads != 0) {
                $f_ads = get_user_meta($uid, '_sb_featured_ads', true);
                if ($f_ads != '-1') {
                    $f_ads = (int) $f_ads;
                    $new_f_fads = $featured_ads + $f_ads;
                    update_user_meta($uid, '_sb_featured_ads', $new_f_fads);
                } else if ($f_ads == '-1') {
                    update_user_meta($uid, '_sb_featured_ads', $featured_ads);
                }
            }

            if ($bump_ads == '-1') {
                update_user_meta($uid, '_sb_bump_ads', '-1');
            } else if (is_numeric($bump_ads) && $bump_ads != 0) {
                $b_ads = get_user_meta($uid, '_sb_bump_ads', true);
                if ($b_ads != '-1') {
                    $b_ads = (int) $b_ads;
                    $new_b_fads = $bump_ads + $b_ads;
                    update_user_meta($uid, '_sb_bump_ads', $new_b_fads);
                } else if ($b_ads == '-1') {
                    update_user_meta($uid, '_sb_bump_ads', $bump_ads);
                }
            }

            if ($days == '-1') {
                update_user_meta($uid, '_sb_expire_ads', '-1');
            } else {
                $expiry_date = get_user_meta($uid, '_sb_expire_ads', true);
                $e_date = strtotime($expiry_date);
                $today = strtotime(date('Y-m-d'));
                if ($today > $e_date) {
                    $new_expiry = date('Y-m-d', strtotime("+$days days"));
                } else {
                    $date = date_create($expiry_date);
                    date_add($date, date_interval_create_from_date_string("$days days"));
                    $new_expiry = date_format($date, "Y-m-d");
                }
                update_user_meta($uid, '_sb_expire_ads', $new_expiry);
            }
        }
    }

}
/* Whern Order Completed By Admin starts */
/* Added Meta For The Android InApp Purchase */
add_action('add_meta_boxes', 'adforestAPI_andrid_product_key_hook');

function adforestAPI_andrid_product_key_hook() {
    add_meta_box('adforestAPI_metaboxes_product_android_ios', __('InApp Purchase Settings For Android and IOS Apps', 'adforest-rest-api'), 'adforestAPI_andrid_product_key_func', 'product', 'normal', 'high');
}

if (!function_exists('adforestAPI_andrid_product_key_func')) {

    function adforestAPI_andrid_product_key_func($post) {
        wp_nonce_field('adforestAPI_metaboxes_product_android_ios', 'meta_box_nonce_product');
        ?>
        <div>
            <p><?php echo __('Android Product Code', 'adforest-rest-api');?></p>
            <input type="text" name="package_product_code_android" class="project_meta" placeholder="<?php echo esc_attr__('Enter you android product code here.', 'adforest-rest-api');?>" size="30" value="<?php echo esc_attr(get_post_meta($post->ID, "package_product_code_android", true));?>" id="package_product_code_android" spellcheck="true" autocomplete="off">
            <div><?php echo __("Please enter product code for the andrid product. Leave empty if you dont't have any. Only enter in case you have bought android app.", 'adforest-rest-api');?></div>
        </div>
        <div>
            <p><?php echo __('IOS Product Code', 'adforest-rest-api');?></p>
            <input type="text" name="package_product_code_ios" class="project_meta" placeholder="<?php echo esc_attr__('Enter you ios product code here.', 'adforest-rest-api');?>" size="30" value="<?php echo esc_attr(get_post_meta($post->ID, "package_product_code_ios", true));?>" id="package_product_code_ios" spellcheck="true" autocomplete="off">
            <div><?php echo __("Please enter product code for the andrid product. Leave empty if you dont't have any. Only enter in case you have bought ios app.", 'adforest-rest-api');?></div>
        </div>  

        <p><strong>*<?php echo __("Please make sure you have created the **** product while create packages in AppStore/PlayStore accounts.", 'adforest-rest-api');?></strong></p>      
        <?php
    }

}

add_action('save_post', 'adforestAPI_save_appProduct_ids');
if (!function_exists('adforestAPI_save_appProduct_ids')) {

    function adforestAPI_save_appProduct_ids($post_id) {
        /* Bail if we're doing an auto save */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        /* if our nonce isn't there, or we can't verify it, bail */
        if (!isset($_POST['meta_box_nonce_product']) || !wp_verify_nonce($_POST['meta_box_nonce_product'], 'my_meta_box_nonce_product'))
            return;
        /* if our current user can't edit this post, bail */
        if (!current_user_can('edit_post'))
            return;
        /* Make sure your data is set before trying to save it */
        if (isset($_POST['package_product_code_android'])) {
            update_post_meta($post_id, 'package_product_code_android', $_POST['package_product_code_android']);
        } else {
            update_post_meta($post_id, 'package_product_code_android', '');
        }
        /* For IOS */
        if (isset($_POST['package_product_code_ios'])) {
            update_post_meta($post_id, 'package_product_code_ios', $_POST['package_product_code_ios']);
        } else {
            update_post_meta($post_id, 'package_product_code_ios', '');
        }
    }

}
/* Cart Settings Here */
add_action('rest_api_init', 'adforestAPI_woocommerce_get_cart_hook', 0);

function adforestAPI_woocommerce_get_cart_hook() {
    register_rest_route(
            'adforest/v1', '/cart/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_woocommerce_get_cart_func',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_woocommerce_get_cart_func')) {

    function adforestAPI_woocommerce_get_cart_func() {
        global $product;
        global $woocommerce;
        $data = array();
        $user = wp_get_current_user();
        $cart_items = array();
        foreach (WC()->cart->get_cart() as $cart_item) {
            $cart_key = $cart_item['key'];
            $product_id = $cart_item['product_id'];

            $item_name = $cart_item['data']->get_title();
            $quantity = $cart_item['quantity'];
            $price = $cart_item['data']->get_price();
            $regular_price = $cart_item['data']->get_regular_price();
            $image_id = $cart_item['data']->get_image_id();
            $stock_quantity = $cart_item['data']->get_stock_quantity();
            $stock_quantity = ( isset($stock_quantity) && $stock_quantity != "" ) ? $stock_quantity : 100;
            /* Set Thumbnail */
            $product_default = wc_placeholder_img_src();
            $product_thumbnail = get_the_post_thumbnail_url($image_id);
            if (!$product_thumbnail) {
                $product_thumbnail = $product_default;
            }

            $currency_symbol = adforestAPI_convert_uniText(get_woocommerce_currency_symbol());
            $get_regular_price = adforestAPI_get_adPrice_currencyPos($regular_price, $currency_symbol);
            $get_sale_price = adforestAPI_get_adPrice_currencyPos($price, $currency_symbol);

            $price_data = array('symbol' => $currency_symbol, 'sale_price' => $get_sale_price, 'regular_price' => $get_regular_price);
            $cart_items[] = array(
                'cart_key' => $cart_key,
                'product_id' => $product_id,
                'product_name' => $item_name,
                'product_quantity' => $quantity,
                'product_price' => $price_data,
                'product_thumbnail' => $product_thumbnail,
                'quantity_txt' => __("Quantity", "adforest-rest-api"),
                'quantity_limit' => array("min" => 0, "max" => $stock_quantity),
            );
        }

        $data['cart_items'] = $cart_items;
        $lists['subtotal'] = array("key" => __("Subtotal", 'adforest-rest-api'), "val" => "");
        $lists['shipping'] = array("key" => __("Shipping", 'adforest-rest-api'), "val" => "");
        $lists['total'] = array("key" => __("Total", 'adforest-rest-api'), "val" => "");
        $data['cart_summery']['title'] = __("Shop Summery", 'adforest-rest-api');
        $data['cart_summery']['list'] = $lists;
        $data['cart_total'] = count($cart_items);
        $data['page_title'] = __("Cart", 'adforest-rest-api');
        $data['btn_txt']['check_out'] = __("Checkout", "adforest-rest-api");
        $data['btn_txt']['update_cart'] = __("Update Cart", "adforest-rest-api");
        $data['popup']['title'] = __("Shop Summary", "adforest-rest-api");
        $message = ( count($cart_items) > 0 ) ? "" : __("No cart item found.", "adforest-rest-api");
        $response = array('success' => true, 'data' => $data, 'message' => $message);
        return $response;
    }

}

/* Remove From Cart */
add_action('rest_api_init', 'adforestAPI_woocommerce_remove_item_from_cart_hook', 0);

function adforestAPI_woocommerce_remove_item_from_cart_hook() {
    register_rest_route(
            'adforest/v1', '/cart/remove_item', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_woocommerce_remove_item_from_cart_func',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_apply_matched_coupons')) {

    function adforestAPI_apply_matched_coupons($request) {
        $coupon_code = 'freeweek';
        if (WC()->cart->has_discount($coupon_code))
            return;
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $autocoupon = array(745);
            if (in_array($cart_item['product_id'], $autocoupon)) {
                WC()->cart->add_discount($coupon_code);
                wc_print_notices();
            }
        }
    }

}
if (!function_exists('adforestAPI_woocommerce_remove_item_from_cart_func')) {

    function adforestAPI_woocommerce_remove_item_from_cart_func($request) {
        global $adforestAPI;
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        global $product;
        $json_data = $request->get_json_params();
        $product_id = isset($json_data['product_id']) ? (int) $json_data['product_id'] : '';
        $cartId = WC()->cart->generate_cart_id($product_id);
        $cartItemKey = WC()->cart->find_product_in_cart($cartId);
        if ($cartItemKey) {
            WC()->cart->remove_cart_item($cartItemKey);
            $message = __("Item removed from cart.", "adforest-rest-api");
            $success = true;
        } else {
            $success = false;
            $message = __("No item found in the cart.", "adforest-rest-api");
        }
        $response = array('success' => $success, 'data' => '', 'message' => $message);
        return $response;
    }

}


/* Update the Cart */
add_action('rest_api_init', 'adforestAPI_woocommerce_add_item_from_cart_hook', 0);

function adforestAPI_woocommerce_add_item_from_cart_hook() {
    register_rest_route(
            'adforest/v1', '/cart/add/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_woocommerce_add_item_from_cart_func',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_woocommerce_add_item_from_cart_func')) {

    function adforestAPI_woocommerce_add_item_from_cart_func($request) {
        global $product;
        global $woocommerce;
        $user = wp_get_current_user();
        $json_data = $request->get_json_params();
        $product_id = isset($json_data['product_id']) ? (int) $json_data['product_id'] : 0;
        $product_quantity = isset($json_data['product_quantity']) ? (int) $json_data['product_quantity'] : 0;
        $products_data = isset($json_data['products']) ? $json_data['products'] : array();
        $success = true;
        $message = '';

        if (isset($products_data) && count($products_data) > 0) {
            foreach ($products_data as $pd) {
                $pid = (int) $pd['product_id'];
                $quantity = (int) $pd['quantity'];
                if (($pid != '' && $pid > 0 ) && $quantity > 0) {
                    $message = adforestAPI_woocommerce_add_item_cart_add_func($pid, $quantity);
                }
            }
        } else {
            $success = false;
            $message = __("Product quantity should be atleaast one.", "adforest-rest-api");
        }

        $response = array('success' => $success, 'data' => '', 'message' => $message);
        return $response;
    }

}

if (!function_exists('adforestAPI_woocommerce_add_item_cart_add_func')) {

    function adforestAPI_woocommerce_add_item_cart_add_func($product_id = 0, $product_quantity = 0) {
        //global $product;
        //global $woocommerce;		
        $success = true;

        $product_data = wc_get_product($product_id);
        $product_cart_id = WC()->cart->generate_cart_id($product_id);
        $in_cart = WC()->cart->find_product_in_cart($product_cart_id);

        $stock_quantity = $product_data->get_stock_quantity();
        $total_in_cart = adforestAPI_woocommerce_get_cart_item_quantity_func($product_id);
        $new_total = $total_in_cart + $product_quantity;
        if ($stock_quantity == "" || $new_total <= $stock_quantity) {
            if ($in_cart) {
                WC()->cart->set_quantity($in_cart, $new_total, true);
                $message = __("Cart updated successfully.", "adforest-rest-api");
            } else {
                $added = WC()->cart->add_to_cart($product_id, $product_quantity);
                if ($added) {
                    $message = __("Product added to cart successfully.", "adforest-rest-api");
                } else {
                    $message = __("Item not added to cart. Something went wrong.", "adforest-rest-api");
                }
            }
        } else if ($product_quantity == 0) {
            WC()->cart->add_to_cart($product_id, $product_quantity);
            $message = __("Product out of stock.", "adforest-rest-api");
        } else {
            $success = false;
            $message = __("Products available in stock are ", "adforest-rest-api") . " (" . $stock_quantity . "). " . __("You are ordering", "adforest-rest-api") . " (" . $new_total . ").";
        }

        return $message;
    }

}

/* Update the Cart */
add_action('rest_api_init', 'adforestAPI_woocommerce_update_item_from_cart_hook', 0);

function adforestAPI_woocommerce_update_item_from_cart_hook() {
    register_rest_route(
            'adforest/v1', '/cart/update/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_woocommerce_update_item_from_cart_func',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_woocommerce_update_item_from_cart_func')) {

    function adforestAPI_woocommerce_update_item_from_cart_func($request) {
        global $product;
        global $woocommerce;
        $user = wp_get_current_user();
        $json_data = $request->get_json_params();
        $cart_data = isset($json_data['cart_data']) ? $json_data['cart_data'] : array();

        if (isset($cart_data) && count($cart_data) > 0) {
            $cart_keys = array();
            foreach (WC()->cart->get_cart() as $cart_item) {
                $cart_keys[] = $cart_item['key'];
            }
            foreach ($cart_data as $key => $val) {
                if (in_array($key, $cart_keys)) {
                    WC()->cart->set_quantity($key, $val, true);
                }
            }
            $message = __("Cart updated successfully.", "adforest-rest-api");
            $success = true;
        } else {
            $message = __("Something went wrong while updating cart.", "adforest-rest-api");
            $success = false;
        }
        $response = array('success' => $success, 'data' => $cart_data, 'message' => $message);
        return $response;
    }

}

if (!function_exists('adforestAPI_woocommerce_get_cart_item_quantity_func')) {

    function adforestAPI_woocommerce_get_cart_item_quantity_func($product_id = 0) {
        $qty = 0;
        foreach (WC()->cart->get_cart() as $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                $qty = $cart_item['quantity'];
                break;
            }
        }
        return $qty;
    }

}