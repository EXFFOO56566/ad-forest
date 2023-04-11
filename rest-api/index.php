<?php
/**
 * Plugin Name: AdForest Apps API
 * Plugin URI: https://codecanyon.net/user/scriptsbundle
 * Description: This plugin is essential for the AdFrest android and ios apps.
 * Version: 3.4.0
 * Author: Scripts Bundle
 * Author URI: https://codecanyon.net/user/scriptsbundle
 * License: GPL2
 * Text Domain: adforest-rest-api
 */
/* Get Theme Info If There Is */
$my_theme = wp_get_theme();
$my_theme->get('Name');
/* Load text domain */
add_action('plugins_loaded', 'adforest_rest_api_load_plugin_textdomain');

function adforest_rest_api_load_plugin_textdomain() {
    load_plugin_textdomain('adforest-rest-api', FALSE, basename(dirname(__FILE__)) . '/languages/');
}

/* For Demo case please make it false */
define('ADFOREST_API_ALLOW_EDITING', true);
/* Define Paths For The Plugin */
define('ADFOREST_API_PLUGIN_FRAMEWORK_PATH', plugin_dir_path(__FILE__));
define('ADFOREST_API_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ADFOREST_API_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ADFOREST_API_PLUGIN_PATH_LANGS', plugin_dir_path(__FILE__) . 'languages/');
/* Theme Directry/Folder Paths */
define('ADFOREST_API_THEMEURL_PLUGIN', get_template_directory_uri() . '/');
define('ADFOREST_API_IMAGES_PLUGIN', ADFOREST_API_THEMEURL_PLUGIN . 'images/');
define('ADFOREST_API_CSS_PLUGIN', ADFOREST_API_THEMEURL_PLUGIN . 'css/');
define('ADFOREST_API_JS_PLUGIN', ADFOREST_API_THEMEURL_PLUGIN . 'js/');

/* Only check if plugin activate by theme */

$my_theme = wp_get_theme();
if ($my_theme->get('Name') != 'adforest' && $my_theme->get('Name') != 'adforest child') {
    require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/tgm/tgm-init.php';
}
/* Options Init */
add_action('init', function() {
    // Load the theme/plugin options
    if (file_exists(dirname(__FILE__) . '/inc/options-init.php')) {
        require_once( dirname(__FILE__) . '/inc/options-init.php' );
        if (class_exists('Redux')) {
            Redux::init('adforestAPI');
        }
    }
});
/* Added In Version 1.6.0 */
if (!function_exists('adforestAPI_getallheaders')) {

    function adforestAPI_getallheaders() {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

}
$lang_code = $request_from = '';

if (!function_exists('adforestAPI_getSpecific_headerVal')) {

    function adforestAPI_getSpecific_headerVal($header_key = '') {
        $header_val = '';
        if (count(adforestAPI_getallheaders()) > 0) {

            foreach (adforestAPI_getallheaders() as $name => $value) {
                if (($name == $header_key || strtolower($name) == strtolower($header_key)) && $value != "") {
                    $header_val = $value;
                    break;
                }
            }
        }
        return $header_val;
    }

}
add_action('plugins_loaded', 'adforest_switch_lang_api_callback',0);

if (!function_exists('adforest_switch_lang_api_callback')) {
    function adforest_switch_lang_api_callback() {
        global $sitepress;
        $lang = adforestAPI_getSpecific_headerVal('Adforest-Lang-Locale');
        if (class_exists('SitePress') && !is_admin()) {
            $sitepress->switch_lang($lang, true);
            $opt_name = "adforestAPI";
            $adforest_api_options = get_option($opt_name);
            if (apply_filters('wpml_is_rtl', NULL)) {
//                if (class_exists('Redux')) {
//                    Redux::setOption($opt_name, 'app_settings_rtl', '1');
//                }
            } else {
//                if (class_exists('Redux')) {
//                    Redux::setOption($opt_name, 'app_settings_rtl', '0');
//                }
            }
        }
    }
}

$request_from = adforestAPI_getSpecific_headerVal('Adforest-Request-From');
define('ADFOREST_API_REQUEST_FROM', $request_from);
/* Added In Version 1.6.0 */




/* require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/cpt.php'; */
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/classes.php';
/* require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/cpt.php'; */
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/adforest-api-wpml-functions.php';
/* Include Function  */
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/functions.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/sb-app-functions.php';

/* Include Classes */
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/basic-auth.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/auth.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/email-templates.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/categories-images.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/inc/notifications.php';
/* Include Other Classes */
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/settings.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/posts.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/home.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/users.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/index.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/ad_message.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/register.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/login.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/logout.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/ads.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/ad_post.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/bid.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/profile.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/woo-commerce.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/payment.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/push-notification.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/phone-verification.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/ad_rating.php';
/* Woo-Commerce Starts */
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/woocommerce.php';
require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/shop.php';



require ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/classes/whizchat-chat.php';








/*
 * Notification to all users before package expiry
 * Schedule runs daily
 */
$adforest_app_options = get_option('adforestAPI');

if (isset($adforest_app_options['package_expiry_notification']) && ($adforest_app_options['package_expiry_notification'])) {
    if (!wp_next_scheduled('adforestAPI_package_expiray_notification')) {
        wp_schedule_event(time(), 'daily', 'adforestAPI_package_expiray_notification');
    }
} else {
    if (wp_next_scheduled('adforestAPI_package_expiray_notification')) {
        $timestamp = wp_next_scheduled('adforestAPI_package_expiray_notification');
        wp_unschedule_event($timestamp, 'adforestAPI_package_expiray_notification');
    }
}

add_action('adforestAPI_package_expiray_notification', 'adforestAPI_package_expiray_notification_callback');
if (!function_exists('adforestAPI_package_expiray_notification_callback')) {
    function adforestAPI_package_expiray_notification_callback() {

        $adforest_app_options = get_option('adforestAPI');
        $before_days = isset($adforest_app_options['package_expire_notify_before']) ? $adforest_app_options['package_expire_notify_before'] : 0;
        if (isset($adforest_app_options['package_expiry_notification']) && isset($adforest_app_options['package_expiry_notification'])) {
            $adforest_users = get_users(['role__in' => ['subscriber']]);
            if (isset($adforest_users) && !empty($adforest_users) && is_array($adforest_users)) {
                foreach ($adforest_users as $key => $user) {
                    $package_expiry_data = get_user_meta($user->ID, '_sb_expire_ads', true);
                    $sb_pkg_name = get_user_meta($user->ID, '_sb_pkg_type', true);
                    $user_data = $user->data;
                    $user_display_name = $user_data->display_name;
                    if (empty($package_expiry_data) || $package_expiry_data == -1) {
                        continue;
                    }
                    $notification_date = date("Y-m-d", strtotime("-{$before_days} days", strtotime($package_expiry_data)));
                    $today_data = date("Y-m-d");
                    if ($today_data == $notification_date) {
                        do_action('adforestAPI_package_expiry_notification', $before_days, $user->ID);
                    }
                }
            }
        }
    }
}
add_action('adforestAPI_app_redirect_trigger', 'adforestAPI_app_redirect_trigger', 10, 1);

if (!function_exists('adforestAPI_app_redirect_trigger')) {
    function adforestAPI_app_redirect_trigger($ad_id = 0) {
        if ($ad_id != 0) { ?>
            <script>
                var fallbackToStore = function () {
                    window.location.replace('https://adforest-testapp.scriptsbundle.com/?ad_id=<?php echo $ad_id; ?>');
                };
                var openApp = function () {};
                var triggerAppOpen = function () {
                    openApp();
                    setTimeout(fallbackToStore, 250);
                };
                triggerAppOpen();
            </script>
            <?php
        }
    }
}
/*Woo-Commerce ENds*/
/* Plugin Ends Here */