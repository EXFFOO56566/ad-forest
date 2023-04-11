<?php
/* ----- 	Woo Products Starts Here	 ----- */
add_action('rest_api_init', 'adforestAPI_shop_hook', 0);

function adforestAPI_shop_hook() {
    register_rest_route(
            'adforest/v1', '/shop/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_woocommerce_shop_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );

    register_rest_route(
            'adforest/v1', '/shop/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_woocommerce_shop_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_woocommerce_shop_get')) {

    function adforestAPI_woocommerce_shop_get($request) {
        global $adforestAPI;
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        $json_data = $request->get_json_params();
        /* Pagination Settings */
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $sort_order = isset($json_data['sort_order']) ? $json_data['sort_order'] : '';
        $cat_slug = isset($json_data['cat_slug']) ? $json_data['cat_slug'] : '';
        $shop_posts = (isset($adforestAPI['shop-number-of-products'])) ? $adforestAPI['shop-number-of-products'] : 10;

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $shop_posts,
            'post_status' => 'publish',
            'paged' => $paged,
            'tax_query', array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'adforest_classified_pkgs', 'operator' => 'NOT IN'),)
        );

        if ($sort_order == 'rating') {
            $args['order'] = 'ASC';
            $args['orderby'] = 'meta_value_num';
            $args['orderby_meta_key'] = '_wc_average_rating';
        } else if ($sort_order == 'popularity') {
            $args['order'] = 'DESC';
            $args['orderby'] = 'meta_value_num';
            $args['orderby_meta_key'] = 'total_sales';
        } else if ($sort_order == 'date') {
            $args['order'] = 'DESC';
            $args['orderby'] = 'date';
        } else if ($sort_order == 'price') {
            $args['order'] = 'ASC';
            $args['orderby'] = 'meta_value_num';
            $args['orderby_meta_key'] = '_price';
        } else if ($sort_order == 'price-desc') {
            $args['order'] = 'DESC';
            $args['orderby'] = 'meta_value_num';
            $args['orderby_meta_key'] = '_price';
        } else {
            $args['order'] = 'DESC';
            $args['orderby'] = 'date';
        }

        if ($cat_slug != "") {
            $args['tax_query'] = array(array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat_slug));
        }

        $shop_item = array();
        $loop = new WP_Query($args);
        $count = 0;
        while ($loop->have_posts()) {
            $loop->the_post();
            global $product;
            $product_ID = get_the_ID();
            /* get_woocommerce_currency */
            $currency = get_woocommerce_currency_symbol();
            $price = get_post_meta(get_the_ID(), '_regular_price', true);
            $sale = get_post_meta(get_the_ID(), '_sale_price', true);
            $regular_price = adforestAPI_get_adPrice_currencyPos($price, $currency);
            $sale_price = adforestAPI_get_adPrice_currencyPos($sale, $currency);

            /* get_woocommerce_categories */
            $list = array();
            $term_lists = wp_get_post_terms($product_ID, 'product_cat', array('fields' => 'all'));
            foreach ($term_lists as $term_list)
                $list[] = array('id' => $term_list->term_id, 'name' => $term_list->name);

            /* Get Product Thumb */
            $product_default = wc_placeholder_img_src();
            $product_thumbnail = get_the_post_thumbnail_url($product_ID);
            if (!$product_thumbnail) {
                $product_thumbnail = $product_default;
            }

            $shop_item[$count]['ID'] = ($product_ID);
            $shop_item[$count]['title'] = get_the_title($product_ID);
            $shop_item[$count]['thumbnail'] = $product_thumbnail;
            /* $shop_item[$count]['cats'] = $list;	 */
            $shop_item[$count]['reg_price'] = $regular_price;
            $shop_item[$count]['sale_price'] = $sale_price;
            $shop_item[$count]['rating']['text'] = $product->get_review_count(false) . ' ' . __('Reviews', 'adforest-rest-api');
            $shop_item[$count]['rating']['stars'] = round($product->get_average_rating(false));
            $shop_item[$count]['date'] = get_the_date("Y-m-d H:i:s", $product_ID);

            $count++;
        }

        wp_reset_query();
        $data['page_title'] = (isset($adforestAPI['shop-number-page-title'])) ? $adforestAPI['shop-number-page-title'] : __('Shop', 'adforest-rest-api');
        $data['products'] = $shop_item;
        $message = (count($shop_item) == 0 ) ? __("No Product Found.", "adforest-rest-api") : '';
        /* Pagination Parameters Settings */
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $loop->max_num_pages ) ? true : false;

        $data['pagination'] = array("max_num_pages" => (int) $loop->max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $shop_posts, "current_no_of_ads" => (int) count($loop->posts), "has_next_page" => $has_next_page);

        $data['total_products'] = __("No. Of Products", "adforest-rest-api") . ' ' . $loop->found_posts;
        $data['load_more_btn'] = __("Load More", "adforest-rest-api");
        /* ORDER SETTINGS STARTS */
        $catalog_orderby_options = array(
            '' => __('Select option', 'adforest-rest-api'),
            'menu_order' => __('Default sorting', 'adforest-rest-api'),
            'popularity' => __('Sort by popularity', 'adforest-rest-api'),
            'rating' => __('Sort by average rating', 'adforest-rest-api'),
            'date' => __('Sort by newness', 'adforest-rest-api'),
            'price' => __('Sort by price: low to high', 'adforest-rest-api'),
            'price-desc' => __('Sort by price: high to low', 'adforest-rest-api'),
        );

        $data_key = array();
        foreach ($catalog_orderby_options as $key => $val) {
            $data_key[] = array("key" => $key, "value" => $val);
        }

        $data['sort_order'] = $data_key;
        $data['show_count'] = __("Total Products", "adforest-rest-api") . ' ' . $loop->found_posts;
        $data['select_option'] = __("Select Option", "adforest-rest-api");
        $product_ids = array();
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_ids[] = $cart_item['product_id'];
        }
        $data['cart_total'] = count($product_ids);
        /* ORDER SETTINGS ENDS HERE */
        $response = array('success' => true, 'data' => $data, 'message' => $message);
        return $response;
    }

}

/* Shop Details API */
add_action('rest_api_init', 'adforestAPI_shop_detail_hook', 0);

function adforestAPI_shop_detail_hook() {
    register_rest_route(
            'adforest/v1', '/shop/detail/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_woocommerce_shop_detail_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_woocommerce_get_products_from_ids')) {

    function adforestAPI_woocommerce_get_products_from_ids($product_ids = array(), $user_id = 0) {
        global $adforestAPI;
        global $product;
        $p_data = array();
        if (isset($product_ids) && count($product_ids) > 0) {
            $count = 0;
            foreach ($product_ids as $ids) {
                $product_data = wc_get_product($ids);
                $p_data[$count]['id'] = $ids;
                $p_data[$count]['title'] = $product_data->get_name();
                $currency_symbol = adforestAPI_convert_uniText(get_woocommerce_currency_symbol());
                $p_data[$count]['currency_symbol'] = $currency_symbol;
                $p_data[$count]['price'] = adforestAPI_get_adPrice_currencyPos($product_data->get_regular_price(), $currency_symbol);
                $p_data[$count]['sale_price'] = adforestAPI_get_adPrice_currencyPos($product_data->get_sale_price(), $currency_symbol);
                $p_data[$count]['min_max'] = array("min" => 0, "max" => 50);
                $count++;
            }
        }
        return $p_data;
    }

}

if (!function_exists('adforestAPI_woocommerce_shop_detail_get')) {

    function adforestAPI_woocommerce_shop_detail_get($request) {
        global $adforestAPI;
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        global $product;
        $json_data = $request->get_json_params();
        $product_id = isset($json_data['product_id']) ? (int) $json_data['product_id'] : '';
        if ($product_id == "") {
            return array('success' => false, 'data' => '', 'message' => __("Invalid Product Id", "adforest-rest-api"));
        }
        /* Product Details */
        $data['product']['ID'] = $product_id;
        $products = wc_get_product($product_id);
        //simple, grouped, external, variable
        $get_type = 'simple';
        if ($products->is_type('simple')) {
            $get_type = 'simple';
        } else if ($products->is_type('grouped')) {
            $get_type = 'grouped';

            $data['product']['children'] = adforestAPI_woocommerce_get_products_from_ids($products->get_children());
            $data['product']['upsell_ids'] = adforestAPI_woocommerce_get_products_from_ids($products->get_upsell_ids());
        } else if ($products->is_type('variable')) {
            $get_type = 'variable';
            $data['product']['upsell_ids'] = adforestAPI_woocommerce_get_products_from_ids($products->get_upsell_ids());
            $data['product']['cross_sell_ids'] = adforestAPI_woocommerce_get_products_from_ids($products->get_cross_sell_ids());
        } else if ($products->is_type('external')) {
            $get_type = 'external';

            $data['product']['external']['url'] = $products->get_product_url();
            $data['product']['external']['btn_text'] = $products->get_button_text();
        }

        $data['product']['type'] = $get_type;
        $data['product']['title'] = $products->get_name();
        $data['product']['short_desc'] = array("key" => __("Short Description", "adforest-rest-api"), "value" => $products->get_short_description());

        /* Product Features */
        $features = array();
        $features[] = array("key" => __("SKU", "adforest-rest-api"), "value" => $products->get_sku());
        $features[] = array("key" => __("Stock Status", "adforest-rest-api"), "value" => $products->get_stock_status());

        $category_ids = $products->get_category_ids();
        if (isset($category_ids) && count($category_ids) > 0) {
            $cats = array();
            $tname = '';
            foreach ($category_ids as $id) {
                $term = get_term_by('id', $id, 'product_cat');
                $tname .= $term->name . ',';
            }
            $features[] = array("key" => __("Category", "adforest-rest-api"), "value" => rtrim($tname, ","));
        }

        $data['product']['short_features'] = $features;
        /* Ends */
        $data['product']['desc'] = array("key" => __("Description", "adforest-rest-api"), "value" => $products->get_description());
        $data['product']['status'] = $products->get_status();

        $currency_symbol = adforestAPI_convert_uniText(get_woocommerce_currency_symbol());
        $data['product']['currency_symbol'] = $currency_symbol;
        $data['product']['price'] = adforestAPI_get_adPrice_currencyPos($products->get_regular_price(), $currency_symbol);
        $data['product']['sale_price'] = adforestAPI_get_adPrice_currencyPos($products->get_sale_price(), $currency_symbol);
        $data['product']['reviews_allowed'] = $products->get_reviews_allowed();
        /* Set Thumbnail */
        $product_default = wc_placeholder_img_src();
        $product_thumbnail = get_the_post_thumbnail_url($products->get_image_id());
        if (!$product_thumbnail) {
            $product_thumbnail = $product_default;
        }
        $data['product']['image'] = $product_thumbnail;

        /* Set Images Gallery */
        $gallery_images = $products->get_gallery_image_ids();
        $product_gallery = array();
        if (isset($gallery_images) && count($gallery_images) > 0) {
            foreach ($gallery_images as $img) {
                $product_gallery[] = wp_get_attachment_url($img);
            }
        }
        $data['product']['gallery'] = $product_gallery;
        $get_tag_ids = $products->get_tag_ids();
        $tagsString = '';
        if (isset($get_tag_ids) && count($get_tag_ids) > 0) {
            foreach ($get_tag_ids as $id) {
                $term = get_term_by('id', $id, 'product_tag');
                $tagsString .= $term->name . ',';
            }
        }

        $data['product']['tags'] = array("key" => __("Tags", "adforest-rest-api"), "value" => rtrim($tagsString, ","));
        $data['rating']['is_show'] = true;
        $data['rating']['text'] = $products->get_review_count(false) . ' ' . __('Reviews', 'adforest-rest-api');
        $data['rating']['stars'] = round($products->get_average_rating(false));

        $attributes = $products->get_attributes();
        $formatted_attributes = array();
        foreach ($attributes as $attr => $attr_deets) {
            $attribute_label = wc_attribute_label($attr);
            if (isset($attributes[$attr]) || isset($attributes['pa_' . $attr])) {
                $attribute = isset($attributes[$attr]) ? $attributes[$attr] : $attributes['pa_' . $attr];
                if ($attribute['is_taxonomy']) {
                    $formatted_attributes[$attribute_label] = wc_get_product_terms($product_id, $attribute['name']);
                } else {
                    $formatted_attributes[$attribute_label] = $attribute['value'];
                }
            }
        }

        $pAttr = adforestAPI_wc_display_product_attributes($products);
        $data['attributes_title'] = __("Additional Information", "adforest-rest-api");
        $data['attributes'] = $pAttr;
        $data['attributes_message'] = (count($pAttr) > 0) ? "" : __("No attribute available", "adforest-rest-api");
        $reting_data = $form = array();
        /* Controll Rettings and Form Starts */
        $reting_data['title'] = __("User's Retings", "adforest-rest-api");
        $reting_data['review_data'] = adforestAPI_get_woo_product_reviews($product_id);
        /* Form Settings Starts */
        $form['title'] = __("Add a review", "adforest-rest-api");
        $form['stars']['title'] = __("Your rating", "adforest-rest-api");
        $form['stars']['total'] = 5;
        $enable_review_rating = ( get_option('woocommerce_enable_review_rating') === 'yes' ) ? true : false;
        $form['stars']['select_is_show'] = $enable_review_rating;
        $form['stars']['select']['name'] = 'review_value';
        $select_data = array();
        $select_data[] = array("key" => '', "value" => __("select rating", "adforest-rest-api"),);
        $select_data[] = array("key" => '5', "value" => __("Perfect", "adforest-rest-api"),);
        $select_data[] = array("key" => '4', "value" => __("Good", "adforest-rest-api"),);
        $select_data[] = array("key" => '3', "value" => __("Average", "adforest-rest-api"),);
        $select_data[] = array("key" => '2', "value" => __("Not that bad", "adforest-rest-api"),);
        $select_data[] = array("key" => '1', "value" => __("Very poor", "adforest-rest-api"),);
        $form['stars']['select'] = $select_data;
        $form['stars']['textarea']['name'] = 'review_desc';
        $form['stars']['textarea'] = __("Write Your review", "adforest-rest-api");
        $form['stars']['btn'] = __("Submit Review", "adforest-rest-api");
        /* Form Settings Ends */
        $data['show_review_section'] = true;
        $get_rating['rating'] = $reting_data;
        $get_rating['form'] = $form;
        $data['rating_details'] = $get_rating;
        /* COntroll Rettings and Form Starts */
        /* Pagination Settings */
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $shop_posts = (isset($adforestAPI['shop-number-of-products'])) ? $adforestAPI['shop-number-of-products'] : 10;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $shop_posts,
            'post_status' => 'publish',
            'paged' => $paged,
            'order' => 'DESC',
            'orderby' => 'date'
        );
        $shop_item = array();
        $loop = new WP_Query($args);
        $count = 0;
        while ($loop->have_posts()) {
            $loop->the_post();
            global $product;
            $product_ID = get_the_ID();
            /* get_woocommerce_currency */
            $currency = get_woocommerce_currency_symbol();
            $price = get_post_meta(get_the_ID(), '_regular_price', true);
            $sale = get_post_meta(get_the_ID(), '_sale_price', true);
            $regular_price = adforestAPI_get_adPrice_currencyPos($price, $currency);
            $sale_price = adforestAPI_get_adPrice_currencyPos($sale, $currency);
            /* get_woocommerce_categories */
            $list = array();
            $term_lists = wp_get_post_terms($product_ID, 'product_cat', array('fields' => 'all'));
            foreach ($term_lists as $term_list)
                $list[] = array('id' => $term_list->term_id, 'name' => $term_list->name);
            /* Get Product Thumb */
            $product_default = wc_placeholder_img_src();
            $product_thumbnail = get_the_post_thumbnail_url($product_ID);
            if (!$product_thumbnail) {
                $product_thumbnail = $product_default;
            }
            $shop_item[$count]['ID'] = ($product_ID);
            $shop_item[$count]['title'] = get_the_title($product_ID);
            $shop_item[$count]['thumbnail'] = $product_thumbnail;
            /* $shop_item[$count]['cats'] = $list;	 */
            $shop_item[$count]['reg_price'] = $regular_price;
            $shop_item[$count]['sale_price'] = $sale_price;
            //woocommerce_get_product_thumbnail();
            $count++;
        }
        wp_reset_query();
        $data['page_title'] = (isset($adforestAPI['shop-number-page-title'])) ? $adforestAPI['shop-number-page-title'] : __('Shop', 'adforest-rest-api');
        $data['related_products_title'] = __('Related Products', 'adforest-rest-api');
        $data['related_products_show'] = (isset($adforestAPI['shop-related-single-on']) && $adforestAPI['shop-related-single-on']) ? true : false;
        $data['related_products'] = $shop_item;
        $message = (count($shop_item) == 0 ) ? __("No Product Found.", "adforest-rest-api") : '';
        /* Pagination Parameters Settings */
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $loop->max_num_pages ) ? true : false;
        $data['cart_section']['text'] = __("Add To Cart", "adforest-rest-api");
        $data['cart_section']['quantity_text'] = __("Quantity", "adforest-rest-api");
        $data['cart_section']['max_quantity'] = 10;
        $data['cart_section']['current_quantity'] = 1;
        $response = array('success' => true, 'data' => $data, 'message' => $message);
        return $response;
    }

}

function adforestAPI_wc_display_product_attributes($product) {

    $arr = array();
    if ($product->has_weight()) {
        $arr[] = array("key" => __("Weight", "adforest-rest-api"), "val" => esc_html(wc_format_weight($product->get_weight())));
    }
    if ($product->has_dimensions()) {
        $arr[] = array("key" => __("Dimensions", "adforest-rest-api"), "val" => esc_html(wc_format_dimensions($product->get_dimensions(false))));
    }

    $attributes = $product->get_attributes();
    if (isset($attributes) && count($attributes) > 0) {
        foreach ($attributes as $attribute) {
            $values = array();
            $the_attribute_name = $attribute->get_name();
            if ($attribute->is_taxonomy()) {
                $attribute_taxonomy = $attribute->get_taxonomy_object();
                $the_attribute_name = ( $attribute_taxonomy->attribute_label );
                $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'all'));
                if (isset($attribute_values) && count($attribute_values) > 0) {
                    foreach ($attribute_values as $attribute_value) {
                        $value_name = esc_html($attribute_value->name);
                        if ($attribute_taxonomy->attribute_public) {
                            $values[] = '<a href="' . esc_url(get_term_link($attribute_value->term_id, $attribute->get_name())) . '" rel="tag">' . $value_name . '</a>';
                        } else {
                            $values[] = $value_name;
                        }
                    }
                }
            } else {
                $values = $attribute->get_options();
                foreach ($values as &$value) {
                    $value = make_clickable(esc_html($value));
                }
            }
            $val = apply_filters('woocommerce_attribute', wpautop(wptexturize(implode(', ', $values))), $attribute, $values);
            $arr[] = array("key" => adforestAPI_convert_uniText($the_attribute_name), "val" => adforestAPI_convert_uniText(strip_tags(trim($val))));
        }
    }
    return $arr;
}

if (!function_exists('adforestAPI_get_woo_product_reviews')) {

    function adforestAPI_get_woo_product_reviews($product_id = 0, $paged = 1) {
        $reviews = array();
        $reviews_data = array();
        if ($product_id) {
            $comments_count = wp_count_comments($product_id);
            $total_posts = $comments_count->approved;
            $parent_comments = adforestAPI_parent_comment_counter($product_id);
            $posts_per_page = 2; //get_option( 'posts_per_page' );		
            $max_num_pages = ceil($parent_comments / $posts_per_page);
            $get_offset = ($paged - 1);
            $offset = $get_offset * $posts_per_page;
            $args = array(
                'post_type' => 'product',
                'post_id' => $product_id,
                'order' => 'DESC',
                'number' => $posts_per_page,
                'offset' => $offset,
            );
            $reviews_comments = get_comments($args);
            if (isset($reviews_comments) && $reviews_comments && count($reviews_comments) > 0) {
                foreach ($reviews_comments as $rc) {
                    $reviews_data[] = array(
                        "comment_ID" => (int) $rc->comment_ID,
                        "user_id" => (int) $rc->user_id,
                        "user_img" => adforestAPI_user_dp($rc->user_id),
                        "comment_author" => $rc->comment_author,
                        "comment_content" => $rc->comment_content,
                        "comment_date" => date_i18n(get_option('date_format'), strtotime($rc->comment_date)),
                        "rating_stars" => (int) get_comment_meta($rc->comment_ID, 'rating', true),
                    );
                }
            }
        }

        $reviews['load_more'] = __("Load More", "adforest-rest-api");
        $reviews['has_reviews'] = (count($reviews_data) > 0) ? true : false;
        $reviews['message'] = (count($reviews_data) > 0) ? '' : __("No Review Found. Be the first to post review.", "adforest-rest-api");
        $reviews['reviews'] = $reviews_data;
        $nextPaged = $paged + 1;
        $has_next_page = ( $nextPaged <= (int) $max_num_pages ) ? true : false;
        $reviews['pagination'] = array("max_num_pages" => (int) $max_num_pages, "current_page" => (int) $paged, "next_page" => (int) $nextPaged, "increment" => (int) $posts_per_page, "current_no_of_ads" => (int) $total_posts, "has_next_page" => $has_next_page);
        return $reviews;
    }

}

/* Shop Submit Review API */
add_action('rest_api_init', 'adforestAPI_woocommerce_shop_submit_review_hook', 0);

function adforestAPI_woocommerce_shop_submit_review_hook() {
    register_rest_route(
            'adforest/v1', '/shop/get_review/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_woocommerce_shop_get_review',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route(
            'adforest/v1', '/shop/submit_review/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_woocommerce_shop_submit_review',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_woocommerce_shop_get_review')) {

    function adforestAPI_woocommerce_shop_get_review($request) {
        $json_data = $request->get_json_params();
        $product_id = isset($json_data['product_id']) ? (int) $json_data['product_id'] : '';
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (isset($json_data['page_number'])) {
            $paged = $json_data['page_number'];
        } else {
            $paged = 1;
        }

        $review_data['page_title'] = __("Product Reviews", "adforest-rest-api");
        $review_data['review_data'] = adforestAPI_get_woo_product_reviews($product_id, $paged);
        return array('success' => true, 'data' => $review_data, 'message' => '');
    }

}

if (!function_exists('adforestAPI_woocommerce_shop_submit_review')) {

    function adforestAPI_woocommerce_shop_submit_review($request) {
        global $adforestAPI;
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        global $product;
        $json_data = $request->get_json_params();
        $product_id = isset($json_data['product_id']) ? (int) $json_data['product_id'] : '';
        $stars = isset($json_data['stars']) ? $json_data['stars'] : '';
        $type = isset($json_data['type']) ? $json_data['type'] : '';
        $desc = isset($json_data['desc']) ? $json_data['desc'] : '';

        if ($product_id == "") {
            return array('success' => false, 'data' => '', 'message' => __("Please select product id.", "adforest-rest-api"));
        }
        if ($stars == "") {
            return array('success' => false, 'data' => '', 'message' => __("Please select star rating.", "adforest-rest-api"));
        }
        if ($type == "") {
            return array('success' => false, 'data' => '', 'message' => __("Please select rating type.", "adforest-rest-api"));
        }
        if ($desc == "") {
            return array('success' => false, 'data' => '', 'message' => __("Please add review description.", "adforest-rest-api"));
        }
        if (function_exists('adforest_set_date_timezone')) {
            adforest_set_date_timezone();
        }

        $time = current_time('mysql', 1);
        $review_data = array();
        $data = array(
            'comment_post_ID' => $product_id,
            'comment_author' => $user->data->display_name,
            'comment_author_email' => $user->data->user_email,
            'comment_content' => $desc,
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $user_id,
            'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
            'comment_date' => $time,
            'comment_approved' => 1,
        );

        $comment_id = wp_insert_comment($data);
        if ($comment_id) {
            update_comment_meta($comment_id, 'rating', $type);
            update_comment_meta($comment_id, 'verified', 0);
        }
        return array('success' => true, 'data' => $review_data, 'message' => __("Review posted successfully", "adforest-rest-api"));
    }

}