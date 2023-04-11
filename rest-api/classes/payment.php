<?php
/* ----- 	Payment Procedure Starts Here	 ----- */
add_action('rest_api_init', 'adforestAPI_payment_get_hook', 0);

function adforestAPI_payment_get_hook() {
    register_rest_route(
            'adforest/v1', '/payment/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_payment_process',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_payment_process')) {

    function adforestAPI_payment_process($request) {
        $json_data = $request->get_json_params();
        $package_id = (isset($json_data['package_id'])) ? trim($json_data['package_id']) : '';
        $source_token = (isset($json_data['source_token'])) ? trim($json_data['source_token']) : '';
        $payment_from = (isset($json_data['payment_from']) && $json_data['payment_from'] != "" ) ? trim($json_data['payment_from']) : 'stripe';
        if ($package_id == "") {
            $response = array('success' => false, 'data' => '', 'message' => __("Package Id not found", "adforest-rest-api"));
            return $response;
        }
        global $adforestAPI;
        global $woocommerce;
        $package = wc_get_product($package_id);
        if ($package) {
            if ($payment_from == "cheque") {
                adforestAPI_create_ad_order($package_id, "cheque");
                return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "bank_transfer") {
                adforestAPI_create_ad_order($package_id, "bacs");
                return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "cash_on_delivery") {
                adforestAPI_create_ad_order($package_id, "cod");
                return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "paystack") {
               // adforestAPI_create_ad_order($package_id, "paystack");
                //return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "squareup") {
                //adforestAPI_create_ad_order($package_id, "squareup");
                //return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "authorizedotnet") {
                adforestAPI_create_ad_order($package_id, "authorizedotnet");
                return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "worldpay") {
                adforestAPI_create_ad_order($package_id, "worldpay");
                return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "payhere") {
                adforestAPI_create_ad_order($package_id, "payhere");
                return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "braintree") {
                adforestAPI_create_ad_order($package_id, "braintree");
                return array('success' => true, 'data' => '', 'message' => __("Ordr placed successfully.", "adforest-rest-api"));
            } else if ($payment_from == "paypal") {
                if ($source_token == "") {
                    $response = array('success' => false, 'data' => '', 'message' => __("Invalid Payment Token", "adforest-rest-api"));
                    return $response;
                }
                $payment_id = $source_token;
                $payment_client = (isset($json_data['payment_client'])) ? trim($json_data['payment_client']) : '';
                $paymentClient_json = $payment_client;
                $path = ADFOREST_API_PLUGIN_FRAMEWORK_PATH . 'inc/PayPal-PHP-SDK/autoload.php';
                $charge = '';
                require_once( $path );
                $pay_pal = new PayPal\Api\Payment;
                /** 				
                 * verifying the mobile payment on the server side
                 * method - POST
                 * @param paymentId paypal payment id
                 * @param paymentClientJson paypal json after the payment
                 */
                $success_status = false;
                $response_message = __("Payment verified successfully.", "adforest-rest-api");

                $appKey_paypalMode = (isset($adforestAPI['appKey_paypalMode']) && $adforestAPI['appKey_paypalMode'] != "") ? trim($adforestAPI['appKey_paypalMode']) : 'sandbox';

                $paypalKey = (isset($adforestAPI['appKey_paypalKey']) && $adforestAPI['appKey_paypalKey'] != "") ? trim($adforestAPI['appKey_paypalKey']) : '';
                $paypalClientSecret = (isset($adforestAPI['appKey_paypalClientSecret']) && $adforestAPI['appKey_paypalClientSecret'] != "") ? trim($adforestAPI['appKey_paypalClientSecret']) : '';

                $paypalKey_currency = (isset($adforestAPI['paypalKey_currency']) && $adforestAPI['paypalKey_currency'] != "") ? trim($adforestAPI['paypalKey_currency']) : 'USD';

                if ($paypalKey == "" || $paypalClientSecret == "") {
                    $success_status = false;
                    $response_message = __("Something went wrong. Please check back later.", "adforest-rest-api");
                    return array('success' => $success_status, 'data' => '', 'message' => $response_message);
                }

                try {
                    $paymentId = $payment_id;
                    $payment_client = json_decode($paymentClient_json, true);

                    $apiContext = new \PayPal\Rest\ApiContext(
                            new \PayPal\Auth\OAuthTokenCredential(
                            $paypalKey, /* ClientID */ $paypalClientSecret /* ClientSecret */
                            )
                    );

                    $apiContext->setConfig(array('mode' => $appKey_paypalMode));


                    try {
                        $payment = $pay_pal::get($paymentId, $apiContext);
                    } catch (Exception $ex) {
                        $success_status = false;
                        $response_message = __("Please check api settings", "adforest-rest-api");
                        return array('success' => $success_status, 'data' => '', 'message' => $response_message);
                    }
                    // Verifying the state approved
                    if ($payment->getState() != 'approved') {
                        $success_status = false;
                        $response_message = __("Payment has not been verified. Status is ", "adforest-rest-api") . $payment->getState();
                        return array('success' => $success_status, 'data' => '', 'message' => $response_message);
                    }
                    // Amount on client side
                    $amount_client = $payment_client["amount"];
                    // Currency on client side
                    $currency_client = $payment_client["currency_code"];
                    // Amount on server side
                    $amount_server = html_entity_decode(strip_tags($package->get_price($package_id)));
                    // Currency on server side
                    $currency_server = $paypalKey_currency;
                    $sale_state = 'completed';
                    // Verifying the amount
                    if ($amount_server != $amount_client) {
                        $success_status = false;
                        $response_message = __("Payment amount doesn't matched.", "adforest-rest-api");
                        return array('success' => $success_status, 'data' => '', 'message' => $response_message);
                    }
                    // Verifying the currency
                    if ($currency_server != $currency_client) {
                        $success_status = false;
                        $response_message = __("Payment currency doesn't matched.", "adforest-rest-api");
                        return array('success' => $success_status, 'data' => '', 'message' => $response_message);
                    }
                    // Verifying the sale state
                    if ($sale_state != 'completed') {
                        $success_status = false;
                        $response_message = __("Sale not completed", "adforest-rest-api");
                        return array('success' => $success_status, 'data' => '', 'message' => $response_message);
                    }
                    // storing the saled items
                    /* insertItemSales($payment_id_in_db, $transaction, $sale_state); */
                    adforestAPI_create_ad_order($package_id, "paypal");
                    $response_message = __("Payment Made successfully.", "adforest-rest-api");
                    return array('success' => true, 'data' => '', 'message' => $response_message);

                    echoResponse(200, $response);
                } catch (\PayPal\Exception\PayPalConnectionException $exc) {
                    if ($exc->getCode() == 404) {
                        $response_message = __("Payment not found!", "adforest-rest-api");
                        $success_status = false;
                    } else {
                        $response_message = __("Unknown error occurred!", "adforest-rest-api") . ' ' . $exc->getMessage();
                        $success_status = false;
                    }
                } catch (Exception $exc) {
                    $response_message = __("Unknown error occurred!", "adforest-rest-api") . ' ' . $exc->getMessage();
                    $success_status = false;
                }
            } else if ($payment_from == "stripe") {
                $appKey_stripeSKey = (isset($adforestAPI['appKey_stripeSKey']) ) ? $adforestAPI['appKey_stripeSKey'] : '';
                if ($appKey_stripeSKey == '') {
                    $response = array('success' => false, 'data' => '', 'message' => __("Stripe secret key not setup", "adforest-rest-api"));
                    return $response;
                }
                if ($source_token == "") {
                    $response = array('success' => false, 'data' => '', 'message' => __("Invalid Payment Token", "adforest-rest-api"));
                    return $response;
                }
                /* Stripe Payment Starts */
                $currency = get_woocommerce_currency();
                $amount = (float) $package->get_price() * 100;
                $path = ADFOREST_API_PLUGIN_FRAMEWORK_PATH . 'inc/stripe-php/init.php';
                $charge = '';
                require_once( $path );
                $curl = new \Stripe\HttpClient\CurlClient(array(CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2));
                \Stripe\ApiRequestor::setHttpClient($curl);
                \Stripe\Stripe::setApiKey($appKey_stripeSKey);
                $args = array();
                $success = false;

                try {

                    /* Stripe_Charge::all(); */
                    $args = array('source' => $source_token, 'amount' => $amount, 'currency' => $currency);

                    $charge = \Stripe\Charge::create($args);
                    if ($charge) {
                        $success = true;
                        adforestAPI_create_ad_order($package_id, "stripe");
                        $message = __("You order has been placed.", "adforest-rest-api");
                    } else {
                        $success = false;
                        $message = __("Payment not confirmed", "adforest-rest-api");
                    }
                } catch (Exception $e) {

                    $message = $e->getMessage();
                }
                $response = array('success' => $success, 'data' => $args, 'message' => $message);
                return $response;
                /* Stripe Payment Ends */
            } else if ($payment_from == "payu") {
                //adforestAPI_create_ad_order( $package_id, "payu" );			
                return array('success' => false, 'data' => '', 'message' => __("Something Went Wrong in payu.", "adforest-rest-api"));
            } else if ($payment_from == "app_inapp") {
                adforestAPI_create_ad_order($package_id, "app_inapp");
                return array('success' => true, 'data' => '', 'message' => __("Order placed successfully.", "adforest-rest-api"));
            } else {
                return array('success' => false, 'data' => '', 'message' => __("Something Went Wrong.", "adforest-rest-api"));
            }
        }
        /* Get Product Info Ends */
    }

}

add_action('rest_api_init', 'adforestAPI_payment_card_hook', 0);

function adforestAPI_payment_card_hook() {
    register_rest_route(
            'adforest/v1', '/payment/card/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_payment_card_get',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists("adforestAPI_payment_card_get")) {

    function adforestAPI_payment_card_get() {
        $data['page_title'] = __('Checkout Process', 'adforest-rest-api');
        $data['page_title_paystack'] = __('PayStack Checkout Process', 'adforest-rest-api');
        $current_year = date('Y');
        $year_arr = range($current_year, $current_year + 12);
        $data['form']['card_input_text'] = __('Card Number', 'adforest-rest-api');
        $data['form']['select_title'] = __('Expiry Date', 'adforest-rest-api');
        $data['form']['select_month'] = __('Month', 'adforest-rest-api');
        $data['form']['select_year'] = __('Year', 'adforest-rest-api');
        $data['form']['select_option_year'] = $year_arr;
        $data['form']['cvc_input_text'] = __('CVC Number', 'adforest-rest-api');
        $data['form']['btn_text'] = __('Checkout', 'adforest-rest-api');
        $data['error']['card_number'] = __('The card number that you entered is invalid', 'adforest-rest-api');
        $data['error']['expiration_date'] = __('The expiration date that you entered is invalid', 'adforest-rest-api');
        $data['error']['invalid_cvc'] = __('The CVC code that you entered is invalid', 'adforest-rest-api');
        $data['error']['card_details'] = __('The card details that you entered are invalid', 'adforest-rest-api');
        $response = array('success' => true, 'data' => $data, 'message' => '');
        return $response;
    }

}

if (!function_exists("adforestAPI_create_ad_order")) {

    function adforestAPI_create_ad_order($product_id = '', $payment_method = '') {

        global $woocommerce;
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        $sb_address = get_user_meta($user_id, '_sb_address', true);
        $st_address = $city = $state = $country = '';
        if ($sb_address != "") {
            $exp = explode(",", $sb_address);
            if (count($exp) == 4) {

                $country = array_slice($exp, -1, 1);
                $country = ( isset($country[0]) && $country[0] != "" ) ? trim($country[0]) : '';
                $state = array_slice($exp, -2, 1);
                $state = ( isset($state[0]) && $state[0] != "" ) ? trim($state[0]) : '';
                $city = array_slice($exp, -3, 1);
                $city = ( isset($city[0]) && $city[0] != "" ) ? trim($city[0]) : '';
                $st_address = array_slice($exp, -4, 1);
                $st_address = ( isset($st_address[0]) && $st_address[0] != "" ) ? trim($st_address[0]) : '';
            } else if (count($exp) == 3) {
                $country = array_slice($exp, -1, 1);
                $country = ( isset($country[0]) && $country[0] != "" ) ? trim($country[0]) : '';
                $state = array_slice($exp, -2, 1);
                $state = ( isset($state[0]) && $state[0] != "" ) ? trim($state[0]) : '';
                $city = array_slice($exp, -3, 1);
                $city = ( isset($city[0]) && $city[0] != "" ) ? trim($city[0]) : '';
            } else if (count($exp) == 2) {
                $country = array_slice($exp, -1, 1);
                $country = ( isset($country[0]) && $country[0] != "" ) ? trim($country[0]) : '';
                $state = array_slice($exp, -2, 1);
                $state = ( isset($state[0]) && $state[0] != "" ) ? trim($state[0]) : '';
            } else if (count($exp) == 1) {
                $country = array_slice($exp, -1, 1);
                $country = ( isset($country[0]) && $country[0] != "" ) ? trim($country[0]) : '';
            }
        }

        $address = array(
            'first_name' => $user->data->display_name,
            'last_name' => '',
            'company' => '',
            'email' => $user->data->user_email,
            'phone' => get_user_meta($user_id, '_sb_contact', true),
            'address_1' => $st_address,
            'address_2' => '',
            'city' => $city,
            'state' => $state,
            'postcode' => '',
            'country' => $country
        );
        /* Now we create the order */
        $order = wc_create_order();
        $order->add_product(get_product($product_id), 1); // This is an existing SIMPLE product
        $order->set_address($address, 'billing');
        $order->calculate_totals();

        $payment_gateways = WC()->payment_gateways->payment_gateways();
        $saveGateway = ($payment_method != "" ) ? @$payment_gateways[$payment_method] : '';
        $saveGateway = (isset($saveGateway) && $saveGateway != "" ) ? $saveGateway : '';
        /* Gateway settings Ends */
        $order->set_payment_method($saveGateway);
        $order->set_payment_method_title($payment_method);
        $order->update_status("processing", 'Imported order', TRUE);

        $count = wc_update_new_customer_past_orders($user_id);
        update_user_meta($user_id, '_wc_linked_order_count', $count);
        return $order;
    }

}
/* ----- 	Payment Procedure Done Starts Here	 ----- */
add_action('rest_api_init', 'adforestAPI_payment_complete_get_hook', 0);

function adforestAPI_payment_complete_get_hook() {
    register_rest_route(
            'adforest/v1', '/payment/complete/', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'adforestAPI_payment_complete_process',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
    register_rest_route(
            'adforest/v1', '/payment/complete/', array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => 'adforestAPI_payment_complete_process',
        'permission_callback' => function () {
            return adforestAPI_basic_auth();
        },
            )
    );
}

if (!function_exists('adforestAPI_payment_complete_process')) {

    function adforestAPI_payment_complete_process($request) {
        global $woocommerce;
        global $adforestAPI;

        $user_id = get_current_user_id();
        $order = wc_get_customer_last_order($user_id);
        $methods = WC()->payment_gateways->payment_gateways();

        $app_settings_rtl = isset($adforestAPI['app_settings_rtl']) && $adforestAPI['app_settings_rtl'] ? 'rtl' : 'ltr';
        $app_settings_rtl = apply_filters('AdforestAPI_app_direction', $app_settings_rtl);
        $html = '';
        $html .= '<div class="woocommerce-order" dir="' . $app_settings_rtl . '">';

        if ($order) :

            if ($order->has_status('failed')) :

                $html .= '<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">' . __('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'adforest-rest-api') . '</p>';

                $html .= '<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="' . esc_url($order->get_checkout_payment_url()) . '" class="button pay">' . __('Pay', 'adforest-rest-api') . '</a>';
                if (is_user_logged_in()) :
                /* $html .= '<a href="'.esc_url( wc_get_page_permalink( 'myaccount' ) ).'" class="button pay">'. __( 'My account', 'woocommerce' ).'</a>'; */
                endif;
                $html .= '</p>';
            else :
                $html .= '<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">' . apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'adforest-rest-api'), $order) . '</p>';

                $html .= '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';

                $html .= '<li class="woocommerce-order-overview__order order">';
                $html .= __('Order number:', 'adforest-rest-api');
                $html .= '<strong>' . $order->get_order_number() . '</strong>';
                $html .= '</li>';

                $html .= '<li class="woocommerce-order-overview__date date">';
                $html .= __('Date:', 'adforest-rest-api');
                $html .= '<strong>' . wc_format_datetime($order->get_date_created()) . '</strong>';
                $html .= '</li>';

                if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()) :
                    $html .= '<li class="woocommerce-order-overview__email email">';
                    $html .= __('Email:', 'adforest-rest-api');
                    $html .= '<strong>' . $order->get_billing_email() . '</strong>';
                    $html .= '</li>';
                endif;

                $html .= '<li class="woocommerce-order-overview__total total">';
                $html .= __('Total:', 'adforest-rest-api');
                $html .= '<strong>' . $order->get_formatted_order_total() . ' </strong>';
                $html .= '</li>';

                if ($order->get_payment_method_title()) :
                    $html .= '<li class="woocommerce-order-overview__payment-method method">';
                    $html .= __('Payment method:', 'adforest-rest-api');
                    $html .= '<strong>' . wp_kses_post($order->get_payment_method_title()) . '</strong>';
                    $html .= '</li>';
                endif;

                $html .= '</ul>';

            endif;

            $thankyou = $get_payment_method = '';
            ob_start();
            do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id());
            $get_payment_method = ob_get_contents();
            ob_end_clean();
            ob_start();
            do_action('woocommerce_thankyou', $order->get_id());
            $thankyou = ob_get_contents();
            ob_end_clean();

            $html .= $get_payment_method;
            $html .= $thankyou;
        else :

            $html .= '<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">' . apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'adforest-rest-api'), null) . '</p>';

        endif;

        $html .= '</div>';

        $thankYou = (isset($adforestAPI['payment_thankyou'])) ? $adforestAPI['payment_thankyou'] : __('Thank You For Your Order', 'adforest-rest-api');
        $thankBtn = (isset($adforestAPI['btn_thankyou'])) ? $adforestAPI['btn_thankyou'] : __('Continue', 'adforest-rest-api');
        $html = adforestAPI_strip_single_tag($html, 'a');
        $html = str_replace("\r", "", $html);
        $html = str_replace("\n", "", $html);
        $html_title = __('Thank you. Your order has been received.', 'adforest-rest-api');
        $html_data = '<!doctype html><html><head><meta charset="utf-8"><title>' . $thankYou . '</title></head><body>' . $html . '</body></html>';
        $data['data'] = ($html_data);
        $data['order_thankyou_title'] = (@$thankYou);
        $data['order_thankyou_btn'] = (@$thankBtn);


        $response = array('success' => true, 'data' => $data, 'message' => '');
        return $response;
    }

}

function adforestAPI_strip_single_tag($str, $tag) {
    $str1 = preg_replace('/<\/' . $tag . '>/i', '', $str);
    if ($str1 != $str) {
        $str = preg_replace('/<' . $tag . '[^>]*>/i', '', $str1);
    }
    return $str;
}

/*----- 	Payment Procedure Done Ends Here	 -----*/