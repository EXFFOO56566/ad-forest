<?php
/* Theme Options For AdForest WordPress API Theme */
if (!class_exists('Redux'))
    return;
$opt_name = "adforestAPI";
$theme = wp_get_theme();
$args = array(
    'opt_name' => 'adforestAPI',
    'dev_mode' => false,
    'display_name' => __('AdForest Apps API Options', "adforest-rest-api"),
    'display_version' => '3.4.0',
    'page_title' => __('AdForest Apps API Options', "adforest-rest-api"),
    'update_notice' => true,
    'admin_bar' => true,
    'menu_type' => 'submenu',
    'menu_title' => __('Apps API Options', "adforest-rest-api"),
    'allow_sub_menu' => true,
    'page_parent_post_type' => 'your_post_type',
    'customizer' => true,
    'default_show' => true,
    'default_mark' => '*',
    'hints' => array(
        'icon_position' => 'right',
        'icon_size' => 'normal',
        'tip_style' => array(
            'color' => 'light',
        ),
        'tip_position' => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect' => array(
            'show' => array(
                'duration' => '500',
                'event' => 'mouseover',
            ),
            'hide' => array(
                'duration' => '500',
                'event' => 'mouseleave unfocus',
            ),
        ),
    ),
    'output' => true,
    'output_tag' => true,
    'settings_api' => true,
    'cdn_check_time' => '1440',
    'compiler' => true,
    'global_variable' => 'adforestAPI',
    'page_permissions' => 'manage_options',
    'save_defaults' => true,
    'show_import_export' => true,
    'database' => 'options',
    'transient_time' => '3600',
    'network_sites' => true,
);

$args['share_icons'][] = array(
    'url' => 'https://www.facebook.com/scriptsbundle',
    'title' => __('Like us on Facebook', "adforest-rest-api"),
    'icon' => 'el el-facebook'
);

Redux::setArgs($opt_name, $args);

/* ------------------ App Settings ----------------------- */
Redux::setSection($opt_name, array(
    'title' => __('App Settings', "adforest-rest-api"),
    'id' => 'api_app_settings',
    'desc' => '',
    'icon' => 'el el-cogs',
    'fields' => array(
        array(
            'id' => 'app_is_open',
            'type' => 'switch',
            'title' => __('Make App Open', "adforest-rest-api"),
            'desc' => __('Make App Open For Public', "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'id' => 'app_logo',
            'type' => 'media',
            'url' => true,
            'title' => __('Logo', 'adforest-rest-api'),
            'compiler' => 'true',
            'desc' => __('Site Logo image for the site.', 'adforest-rest-api'),
            'subtitle' => __('Dimensions: 230 x 40', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/logo.png"
            ),
        ),
        array(
            'id' => 'sb_location_type',
            'type' => 'button_set',
            'title' => __('Address Type', 'adforest-rest-api'),
            'options' => array(
                'cities' => __('Cities', 'adforest-rest-api'),
                'regions' => __('Adresses', 'adforest-rest-api'),
            ),
            'default' => 'cities'
        ),
        array(
            'id' => 'app_settings_message_firebase',
            'type' => 'switch',
            'title' => __('Message Settings', "adforest-rest-api"),
            'desc' => __('Send message notification through firebase when receive new message on ad.', "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'sb_enable_social_links',
            'type' => 'switch',
            'title' => __('Enable Social Profiles', 'adforest-rest-api'),
            'subtitle' => __('for display', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'allow_near_by',
            'type' => 'switch',
            'title' => __('Nearby Option', "adforest-rest-api"),
            'desc' => __('Turn on/off nearby option in app', "adforest-rest-api"),
            'default' => false,
        ),

        array(
            'id' => 'allow_home_icon',
            'type' => 'switch',
            'title' => __('Home Icon', "adforest-rest-api"),
            'desc' => __('Turn on/off home icon in app header', "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'id' => 'allow_adv_search_icon',
            'type' => 'switch',
            'title' => __('Advance Search Icon', "adforest-rest-api"),
            'desc' => __('Turn on/off Advance Search icon in app header', "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'id' => 'search_radius_type',
            'type' => 'button_set',
            'title' => __('Search Nearby in', 'adforest'),
            'options' => array(
                'km' => 'Kilometer',
                'mile' => 'Miles',
            ),
            'default' => 'km',
            'required' => array('allow_near_by', '=', array('1')),
        ),
        //TimeOut settings
        array(
            'id' => 'request_timeout',
            'type' => 'text',
            'title' => __('Request Timeout', 'adforest-rest-api'),
            'desc' => __('App Api Request timeout settings (i.e. 9000)', 'adforest-rest-api'),
            'default' => '9000',
        ),
        // header to locations
        array(
            'id' => 'app_top_location',
            'type' => 'switch',
            'title' => __('Top Location', 'adforest-rest-api'),
            'desc' => __('Enable/Disable top bar locations', 'adforest-rest-api'),
            'subtitle' => __('Use this to switch all ads according to the current location selected.', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'app_top_location_list',
            'type' => 'select',
            'data' => 'terms',
            'args' => array('taxonomies' => array('ad_country'), 'hide_empty' => false,),
            'required' => array('app_top_location', '=', true),
            'multi' => true,
            'sortable' => true,
            'title' => __('Select Locations', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Featured Slider Settings', "adforest-rest-api"),
    'id' => 'api_app_featured_slider_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_enable_featured_slider_scroll',
            'type' => 'switch',
            'title' => __('Enable Scroll On Featured Ads', 'adforest-rest-api'),
            'desc' => __('Turn on/off auto scroll on the featured ads slider', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_enable_featured_slider_duration',
            'type' => 'text',
            'title' => __('Slider Scroll Speed', 'adforest-rest-api'),
            'default' => 3000,
            'required' => array(
                'sb_enable_featured_slider_scroll',
                '=',
                true
            ),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Minimum value should be 3000', 'adforest-rest-api'),
            ),
            'desc' => __('Minimum value should be 3000. Enter value in milisecons 1000 is 1 second. ', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_enable_featured_slider_loop',
            'type' => 'text',
            'title' => __('Slider Loop scroll', 'adforest-rest-api'),
            'default' => 3000,
            'required' => array(
                'sb_enable_featured_slider_scroll',
                '=',
                true
            ),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Minimum value should be 3000', 'adforest-rest-api'),
            ),
            'desc' => __('Minimum value should be 3000. Enter value in milisecons 1000 is 1 second. Once end starts again after X number of seconds.', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Language Settings', "adforest-rest-api"),
    'id' => 'api_app_language_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_settings_rtl',
            'type' => 'switch',
            'title' => __('RTL', "adforest-rest-api"),
            'desc' => __('Make app RTL', "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'id' => 'gmap_lang',
            'type' => 'text',
            'title' => __('App Language', 'adforest-rest-api'),
            'desc' => adforestAPI_make_link('https://developers.google.com/maps/faq#languagesupport', __('List of available languages.', 'adforest-rest-api')) . __('If you have selected RTL put language code here like for arabic ar', "adforest-rest-api"),
            'default' => 'en',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('App Color', "adforest-rest-api"),
    'id' => 'api_app_color_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_settings_color',
            'type' => 'button_set',
            'title' => __('App Colors', 'adforest-rest-api'),
            'options' => array(
                '#f58936' => __('Default', 'adforest-rest-api'),
                '#e74c3c' => __('Red', 'adforest-rest-api'),
                '#00a651' => __('Green', 'adforest-rest-api'),
                '#0083c9' => __('Blue', 'adforest-rest-api'),
                '#7dba21' => __('Sea Green', 'adforest-rest-api'),
            ),
            'default' => '#f58936'
        ),
        array(
            'id' => 'app_settings_color_custom_btn',
            'type' => 'switch',
            'title' => __('Custom Color', "adforest-rest-api"),
            'desc' => __("Turn on custom color. You can select the custom color. However it's not recommended. It might conflict with font colors on buttons etc..", "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'required' => array('app_settings_color_custom_btn', '=', true),
            'id' => 'app_settings_color_custom',
            'type' => 'color',
            'title' => __('Select Custom Color', 'adforest-rest-api'),
            'desc' => __("You can select the custom color. However it's not recommended. It might conflict with font colors on buttons etc.. (default: #f58936).", "adforest-rest-api"),
            'default' => '#f58936',
            'transparent' => false
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Profile Image Settings', "adforest-rest-api"),
    'id' => 'api_app_profile_img',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_user_dp',
            'type' => 'media',
            'url' => true,
            'title' => __('Default user picture', 'adforest-rest-api'),
            'compiler' => 'true',
            'subtitle' => __('Dimensions: 200 x 200', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/user.jpg"
            ),
        ),
        array(
            'id' => 'sb_user_guest_dp',
            'type' => 'media',
            'url' => true,
            'title' => __('Default guest picture', 'adforest-rest-api'),
            'compiler' => 'true',
            'subtitle' => __('Dimensions: 200 x 200', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/user.jpg"
            ),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Social Login Settings', "adforest-rest-api"),
    'id' => 'api_app_social_login_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_settings_fb_btn',
            'type' => 'switch',
            'title' => __('Facebook Login/Register', "adforest-rest-api"),
            'desc' => __('Show or hide google button.', "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_settings_google_btn',
            'type' => 'switch',
            'title' => __('Google Login/Register', "adforest-rest-api"),
            'desc' => __('Show or hide google button.', "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_settings_linkedin_btn',
            'type' => 'switch',
            'title' => __('Linkedin Login/Register', "adforest-rest-api"),
            'desc' => __('Show or hide Linkedin button.', "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'id' => 'sb_disable_linkedin_edit',
            'type' => 'switch',
            'title' => __('Disable linkedin Editable', 'adforest'),
            'desc' => __("Enable this to restrict users to don't change the linkedin profile URL", "adforest"),
            'required' => array(
                array('app_settings_linkedin_btn', '=', '1'),
            ),
            'default' => false,
        ),
        array(
            'id' => 'app_settings_apple_btn',
            'type' => 'switch',
            'title' => __('Apple Login/Register', "adforest-rest-api"),
            'subtitle' => __('Show or hide apple button.', "adforest-rest-api"),
            'desc' => __('<b style="color:red;">Note :</b> Only For IOS App', "adforest-restb-api"),
            'required' => array('api-is-buy-ios-app', '=', '1'),
            'default' => true,
        ),
          array(
           'id' => 'sb_register_with_phone',
           'type' => 'switch',
           'title' => __('Register user with phone number with otp', 'adforest'),
           'default' => false,          
           'desc'=>  __('it will allow the user to register and login with their phone number by sending otp','adforest'),
       ),

      array(
           'id' => 'welcome_txt',
           'type' => 'text',
           'title' => __('welcome text', 'adforest'),
           'default' =>  __("Welcome to AdForest", "adforest-rest-api"),        
       ),
        array(
           'id' => 'intro_text',
           'type' => 'text',
           'title' => __('Intro text', 'adforest-rest-api'),
           'default' =>  __("Search now World,s largest classifieds.", "adforest-rest-api")         
       ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('App Dynamic Pages', "adforest-rest-api"),
    'id' => 'api_app_dynamic_pages',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_settings_pages',
            'type' => 'select',
            'data' => 'pages',
            'multi' => true,
            'sortable' => true,
            'title' => __('Select Pages', 'adforest-rest-api'),
            'subtitle' => __('For simple webview', 'adforest-rest-api'),
            'desc' => __('Select pages simple text pages', 'adforest-rest-api'),
        ),
        array(
            'id' => 'app_settings_pages_webview',
            'type' => 'select',
            'data' => 'pages',
            'multi' => true,
            'sortable' => true,
            'title' => __('Select Pages', 'adforest-rest-api'),
            'subtitle' => __('For Advance webview', 'adforest-rest-api'),
            'desc' => __('Select pages for webview. Works best with (AdForest WordPress Theme)', 'adforest-rest-api'),
        ),
        array(
            'id' => 'app_settings_pages_default_icon',
            'type' => 'media',
            'url' => true,
            'title' => __('Default Page Icon', 'adforest-rest-api'),
            'compiler' => 'true',
            'desc' => __('Default Icon For app pages.', 'adforest-rest-api'),
            'subtitle' => __('Dimensions: 24X24 png', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/page-icon.png"
            ),
        ),
    )
));

//Redux::setSection($opt_name, array(
//    'title' => __('App Intro Pages', "adforest-rest-api"),
//    'id' => 'api_app_intro_pages',
//    'desc' => '',
//    'subsection' => true,
//    'fields' => array(
//        array(
//            'id' => 'app_intro_switch',
//            'type' => 'switch',
//            'title' => __('App Intro', "adforest-rest-api"),
//            'desc' => __('Enable/Disable app intro module.', "adforest-rest-api"),
//            'default' => false,
//        ),
//        array(
//            'id' => 'app-intro-slides',
//            'type' => 'slides',
//            'title' => __('Intro Slides', 'adforest-rest-api'),
//            'subtitle' => __('Please add introduction slides.you can add as slides as you can.', 'adforest-rest-api'),
//            //'desc' => __('This field will store all slides values into a multidimensional array to use into a foreach loop.', 'adforest-rest-api'),
//            'placeholder' => array(
//                'title' => __('Add slide Title', 'adforest-rest-api'),
//                'description' => __('Add slide Description.', 'adforest-rest-api'),
//            ),
//            'show' => array(
//                'title' => true,
//                'description' => true,
//                'url' => false,
//                'upload' => false,
//            ),
//            'required' => array('app_intro_switch', '=', '1'),
//        ),
//    )
//));




/* ------------------ App Settings ----------------------- */
Redux::setSection($opt_name, array(
    'title' => __('App Extra Settings', "adforest-rest-api"),
    'id' => 'api_app_extra_settings',
    'desc' => '',
    'icon' => 'el el-cogs',
    'fields' => array(
    )
));


Redux::setSection($opt_name, array(
    'title' => __('App About', "adforest-rest-api"),
    'id' => 'api_app_about_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_about_show',
            'type' => 'switch',
            'title' => __('Show About Section', "adforest-rest-api"),
            'desc' => __('Show app about section in setting page', "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_about_title',
            'type' => 'text',
            'title' => __('Title', 'adforest-rest-api'),
            'default' => __("About App", "adforest-rest-api"),
            'desc' => __('Enter app version title.', 'adforest-rest-api'),
            'required' => array('app_version_show', '=', '1'),
        ),
        array(
            'id' => 'app_about_desc',
            'type' => 'text',
            'title' => __('App About Description', 'adforest-rest-api'),
            'default' => "",
            'desc' => __('Enter app about description.', 'adforest-rest-api'),
            'required' => array('app_about_show', '=', '1'),
        ),
    )
));


Redux::setSection($opt_name, array(
    'title' => __('App Version', "adforest-rest-api"),
    'id' => 'api_app_version_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_version_show',
            'type' => 'switch',
            'title' => __('Show App Version', "adforest-rest-api"),
            'desc' => __('Show app version in setting page', "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_version_title',
            'type' => 'text',
            'title' => __('Title', 'adforest-rest-api'),
            'default' => __("App Version", "adforest-rest-api"),
            'desc' => __('Enter app version title.', 'adforest-rest-api'),
            'required' => array('app_version_show', '=', '1'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('App Reting Settings', "adforest-rest-api"),
    'id' => 'api_app_reting_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'allow_app_rating',
            'type' => 'switch',
            'title' => __('App Rating', "adforest-rest-api"),
            'desc' => __('Show app rating icon on the top.', "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'id' => 'allow_app_rating_title',
            'type' => 'text',
            'title' => __('Rating Title', 'adforest-rest-api'),
            'default' => __("App Store Rating", "adforest-rest-api"),
            'required' => array('allow_app_rating', '=', '1'),
            'desc' => __('Rating title in the popup.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'allow_app_rating_url',
            'type' => 'text',
            'title' => __('App URL (For Android)', 'adforest-rest-api'),
            'default' => '',
            'required' => array('allow_app_rating', '=', '1'),
            'desc' => __('Enter app URL for app rating. URL is required', 'adforest-rest-api'),
        ),
        array(
            'id' => 'allow_app_rating_url_ios',
            'type' => 'text',
            'title' => __('App ID For Ios App', 'adforest-rest-api'),
            'default' => '',
            'required' => array('allow_app_rating', '=', '1'),
            'desc' => __('Enter app ID for app rating', 'adforest-rest-api'),
        ),
    )
));



Redux::setSection($opt_name, array(
    'title' => __('App Share Settings', "adforest-rest-api"),
    'id' => 'api_app_share_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'allow_app_share',
            'type' => 'switch',
            'title' => __('App Share', "adforest-rest-api"),
            'desc' => __('Show app share icon on the top.', "adforest-rest-api"),
            'default' => false,
        ),
        array(
            'id' => 'allow_app_share_title',
            'type' => 'text',
            'title' => __('Share Popup Title', 'adforest-rest-api'),
            'default' => __("Share this", "adforest-rest-api"),
            'required' => array('allow_app_share', '=', '1'),
            'desc' => __('title in the popup.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'allow_app_share_text',
            'type' => 'text',
            'title' => __('Subject', 'adforest-rest-api'),
            'subtitle' => __('Android App', 'adforest-rest-api'),
            'default' => '',
            'required' => array('allow_app_share', '=', '1'),
            'desc' => __('App share subject. Not required.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'allow_app_share_url',
            'type' => 'text',
            'title' => __('App Share URL', 'adforest-rest-api'),
            'subtitle' => __('Android App', 'adforest-rest-api'),
            'default' => '',
            'required' => array('allow_app_share', '=', '1'),
            'desc' => __('Enter app share URL for app sharing. URL is required.', 'adforest-rest-api'),
        ),

        array(
            'id' => 'allow_app_share_text_ios',
            'type' => 'text',
            'title' => __('Subject', 'adforest-rest-api'),
            'subtitle' => __('IOS App', 'adforest-rest-api'),
            'default' => '',
            'required' => array('allow_app_share', '=', '1'),
            'desc' => __('App share subject. Not required.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'allow_app_share_url_ios',
            'type' => 'text',
            'title' => __('App Share URL', 'adforest-rest-api'),
            'subtitle' => __('IOS App', 'adforest-rest-api'),
            'default' => '',
            'required' => array('allow_app_share', '=', '1'),
            'desc' => __('Enter app share URL for app sharing. URL is required.', 'adforest-rest-api'),
        ),


    )
));

Redux::setSection($opt_name, array(
    'title' => __("App Faq's Settings", "adforest-rest-api"),
    'id' => 'api_app_faqs_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_faqs_show',
            'type' => 'switch',
            'title' => __("Show Faq's Section", "adforest-rest-api"),
            'desc' => __("Show app faq's section in setting page", "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_faqs_title',
            'type' => 'text',
            'title' => __('Title', 'adforest-rest-api'),
            'default' => __("Faq's", "adforest-rest-api"),
            'desc' => __("Enter app faq's  title.", 'adforest-rest-api'),
            'required' => array('app_faqs_show', '=', '1'),
        ),
        array(
            'id' => 'app_faqs_url',
            'type' => 'text',
            'title' => __("App Faq's URL", 'adforest-rest-api'),
            'default' => "",
            'desc' => __("Enter app faq's URL.", 'adforest-rest-api'),
            'required' => array('app_faqs_show', '=', '1'),
        ),
    )
));



Redux::setSection($opt_name, array(
    'title' => __("Privacy Policy Settings", "adforest-rest-api"),
    'id' => 'api_app_privacy_policy_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_privacy_policy_show',
            'type' => 'switch',
            'title' => __("Show Privacy Policy Section", "adforest-rest-api"),
            'desc' => __("Show app Privacy Policy section in setting page", "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_privacy_policy_title',
            'type' => 'text',
            'title' => __('Title', 'adforest-rest-api'),
            'default' => __("Privacy Policy", "adforest-rest-api"),
            'desc' => __("Enter App Privacy Policy  Title.", 'adforest-rest-api'),
            'required' => array('app_privacy_policy_show', '=', '1'),
        ),
        array(
            'id' => 'app_privacy_policy_url',
            'type' => 'text',
            'title' => __("App Privacy Policy URL", 'adforest-rest-api'),
            'default' => "",
            'desc' => __("Enter app Privacy Policy URL.", 'adforest-rest-api'),
            'required' => array('app_privacy_policy_show', '=', '1'),
        ),
    )
));


Redux::setSection($opt_name, array(
    'title' => __("Terms & Condition Settings", "adforest-rest-api"),
    'id' => 'api_app_tandc_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_tandc_show',
            'type' => 'switch',
            'title' => __("Show Privacy Policy Section", "adforest-rest-api"),
            'desc' => __("Show app Terms and Condition section in setting page", "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_tandc_title',
            'type' => 'text',
            'title' => __('Title', 'adforest-rest-api'),
            'default' => __("Terms and Condition", "adforest-rest-api"),
            'desc' => __("Enter App Terms and Condition  Title.", 'adforest-rest-api'),
            'required' => array('app_tandc_show', '=', '1'),
        ),
        array(
            'id' => 'app_tandc_url',
            'type' => 'text',
            'title' => __("Terms and Condition URL", 'adforest-rest-api'),
            'default' => "",
            'desc' => __("Enter app Terms and Condition URL.", 'adforest-rest-api'),
            'required' => array('app_tandc_show', '=', '1'),
        ),
    )
));


Redux::setSection($opt_name, array(
    'title' => __("Feedback Settings", "adforest-rest-api"),
    'id' => 'api_app_feedback_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'app_feedback_show',
            'type' => 'switch',
            'title' => __("Feedback Section", "adforest-rest-api"),
            'desc' => __("Show app feedback section in setting page", "adforest-rest-api"),
            'default' => true,
        ),
        array(
            'id' => 'app_feedback_title',
            'type' => 'text',
            'title' => __('Title', 'adforest-rest-api'),
            'default' => __("Feedback", "adforest-rest-api"),
            'desc' => __("Enter App Feedback  Title.", 'adforest-rest-api'),
            'required' => array('app_feedback_show', '=', '1'),
        ),
        array(
            'id' => 'app_feedback_subline',
            'type' => 'text',
            'title' => __('Subline', 'adforest-rest-api'),
            'default' => __("Got any queries? We are here to help you!", "adforest-rest-api"),
            'desc' => __("Enter App Feedback  subline.", 'adforest-rest-api'),
            'required' => array('app_feedback_show', '=', '1'),
        ),
        array(
            'id' => 'app_feedback_admin_email',
            'type' => 'text',
            'title' => __("Feedback Admin email", 'adforest-rest-api'),
            'default' => get_option('admin_email'),
            'desc' => __("Enter app feedback email for admin where he received emails.", 'adforest-rest-api'),
            'required' => array('app_feedback_show', '=', '1'),
        ),
        array(
            'id' => 'api_key_settings-info1',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => __('Info', 'adforest-rest-api'),
            'desc' => __('Email tempate settings.', 'adforest-rest-api')
        ),
        /* Email Template */
        array(
            'id' => 'sb_app_feedback_subject',
            'type' => 'text',
            'title' => __('Feedback email subject', 'adforest-rest-api'),
            'desc' => __('%feedback_from% , %site_name% will be translated accordingly.', 'adforest-rest-api'),
            'default' => 'You Have a new feedback On %feedback_from%',
        ),
        array(
            'id' => 'sb_app_feedback_from',
            'type' => 'text',
            'title' => __('Ad Feedback email FROM', 'adforest-rest-api'),
            'desc' => __('NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_app_feedback_message',
            'type' => 'editor',
            'title' => __('Feedback Email template', 'adforest-rest-api'),
            'desc' => __('%feedback_subject% , %feedback_email% , %feedback_message% , %feedback_from% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello </span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>Admin,</b></span></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>You have received a new feedback on:</strong> %feedback_from%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Subject:</strong> %feedback_subject%</p><strong>Email: </strong>%feedback_email%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Message:</strong></p>%feedback_message%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>',
        ),
    )
));





Redux::setSection($opt_name, array(
    'title' => __('App Key Settings', "adforest-rest-api"),
    'id' => 'api_key_settings',
    'desc' => '',
    'icon' => 'el el-key',
    'fields' => array(
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Android Key Settings', "adforest-rest-api"),
    'id' => 'api_key_settings_android',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'api_key_settings-info1',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => __('Alert', 'adforest-rest-api'),
            'desc' => __('Once added be carefull editing next time. Those Key Should Be Same In App Header.', 'adforest-rest-api')
        ),
        array(
            'id' => 'api_key_settings-info1-1',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => __('Info', 'adforest-rest-api'),
            'desc' => __('Below section is only for if you have purchased Android App. Then turn it on and enter the purchase code in below text field that will appears.', 'adforest-rest-api')
        ),
        array(
            'id' => 'api-is-buy-android-app',
            'type' => 'switch',
            'title' => __('For Android App', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('If you have purchased the android app.', 'adforest-rest-api'),
        ),
        array(
            'required' => array('api-is-buy-android-app', '=', true),
            'id' => 'appKey_pCode',
            'type' => 'text',
            'title' => __('Enter You Android Purchase Code Here', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('Your android item purchase code got from codecanyon. You have purchased the item seprately.', 'adforest-rest-api'),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
            ),
        ),
        array(
            'required' => array(
                'api-is-buy-android-app',
                '=',
                true
            ),
            'id' => 'appKey_Scode',
            'type' => 'text',
            'title' => __('Enter Your Android Secret Code Here', 'adforest-rest-api'),
            'default' => '',
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
            ),
            'desc' => __('Just a random number generated by you for app security.', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('IOS Key Settings', "adforest-rest-api"),
    'id' => 'api_key_settings_ios',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'api_key_settings-info1-2',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => __('Info', 'adforest-rest-api'),
            'desc' => __('Below section is only for if you have purchased IOS App. Then turn it on and enter the purchase code in below text field that will appears.', 'adforest-rest-api')
        ),
        array(
            'id' => 'api-is-buy-ios-app',
            'type' => 'switch',
            'title' => __('For IOS App', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('If you have purchased the ios app.', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'api-is-buy-ios-app',
                '=',
                true
            ),
            'id' => 'appKey_pCode_ios',
            'type' => 'text',
            'title' => __('Enter You IOS Purchase Code Here', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('Your IOS item purchase code got from codecanyon. You have purchased the item seprately.', 'adforest-rest-api'),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
            ),
        ),
        array(
            'required' => array(
                'api-is-buy-ios-app',
                '=',
                true
            ),
            'id' => 'appKey_Scode_ios',
            'type' => 'text',
            'title' => __('Enter Your IOS Secret Code Here', 'adforest-rest-api'),
            'default' => '',
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
            ),
            'desc' => __('Just a random number generated by you for app security.', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Strip Key Settings', "adforest-rest-api"),
    'id' => 'api_key_settings_stripe',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'api_key_settings-info1-3',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => __('Info', 'adforest-rest-api'),
            'desc' => __('Below section is Other API and Payment settings', 'adforest-rest-api')
        ),
        array(
            'id' => 'appKey_stripeKey',
            'type' => 'text',
            'title' => __('Enter Your Stripe Publishable key Here', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('This will use in app', 'adforest-rest-api'),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
            )
        ),
        array(
            'id' => 'appKey_stripeSKey',
            'type' => 'text',
            'title' => __('Enter Your Stripe Secret key Here', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('This will use at server for varification', 'adforest-rest-api'),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
            )
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('YouTube Key Settings', "adforest-rest-api"),
    'id' => 'api_key_settings_youtube',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'appKey_youtubeKey',
            'type' => 'text',
            'title' => __('Enter Your Youtube Key', 'adforest-rest-api'),
            'default' => '',
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
            )
        ),
    )
));

// Redux::setSection($opt_name, array(
//     'title' => __('Paypal Settings', "adforest-rest-api"),
//     'id' => 'api_payment_paypal',
//     'desc' => '',
//     'subsection' => true,
//     'fields' => array(
//         array(
//             'id' => 'appKey_paypalMode',
//             'type' => 'button_set',
//             'title' => __('Paypal Mode', 'adforest-rest-api'),
//             'options' => array(
//                 'live' => __('Live', 'adforest-rest-api'),
//                 'sandbox' => __('Sandbox', 'adforest-rest-api'),
//             ),
//             'default' => 'live',
//         ),
//         array(
//             'id' => 'appKey_paypalKey',
//             'type' => 'text',
//             'title' => __('Enter Your Paypal Key', 'adforest-rest-api'),
//             'default' => '',
//             'text_hint' => array(
//                 'title' => __('Alert', 'adforest-rest-api'),
//                 'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
//             ),
//             'desc' => __('Enter your paypal client id here', 'adforest-rest-api'),
//         ),
//         array(
//             'id' => 'appKey_paypalClientSecret',
//             'type' => 'text',
//             'title' => __('Enter Your Paypal Secret', 'adforest-rest-api'),
//             'default' => '',
//             'text_hint' => array(
//                 'title' => __('Alert', 'adforest-rest-api'),
//                 'content' => __('Once added be carefull editing next time. This key Should be same in app header.'),
//             ),
//             'desc' => __('Enter your paypal Secret id here', 'adforest-rest-api'),
//         ),
//         array(
//             'id' => 'paypalKey_merchant_name',
//             'type' => 'text',
//             'title' => __('Merchant Name', 'adforest-rest-api'),
//             'default' => '',
//             'desc' => __('Enter the merchant name', 'adforest-rest-api'),
//         ),
//         array(
//             'id' => 'paypalKey_currency',
//             'type' => 'text',
//             'title' => __('Account Currency', 'adforest-rest-api'),
//             'default' => '',
//             'desc' => __('Currency name i.e. USD Supported currency list here: ', 'adforest-rest-api') . ' https://developer.paypal.com/docs/integration/direct/rest/currency-codes/',
//         ),
//         array(
//             'id' => 'paypalKey_privecy_url',
//             'type' => 'text',
//             'title' => __('Privecy Url', 'adforest-rest-api'),
//             'default' => '',
//             'desc' => __('Example link ', 'adforest-rest-api') . 'https://www.example.com/privacy',
//         ),
//         array(
//             'id' => 'paypalKey_agreement',
//             'type' => 'text',
//             'title' => __('Agreement Url', 'adforest-rest-api'),
//             'default' => '',
//             'desc' => __('Example link ', 'adforest-rest-api') . 'https://www.example.com/legal',
//         ),
//     )
// ));
//Redux::setSection($opt_name, array(
//    'title' => __('SquareUp Payment Settings', "adforest-rest-api"),
//    'id' => 'squareup_payment_settings',
//    'desc' => '',
//    'subsection' => true,
//    'fields' => array(
//        
//        array(
//            'id' => 'squareup_app_id',
//            'type' => 'text',
//            'title' => __('App id', 'adforest-rest-api'),
//            'default' => '',
//           // 'desc' => __('Enter the merchant name', 'adforest-rest-api'),
//        ),
//        array(
//            'id' => 'squareup_app_access_token',
//            'type' => 'text',
//            'title' => __('App Access Token', 'adforest-rest-api'),
//            'default' => '',
//        ),
//    )
//));
//Redux::setSection($opt_name, array(
//    'title' => __('Paystack Payment Settings', "adforest-rest-api"),
//    'id' => 'paystack_payment_settings',
//    'desc' => __('<p style="color:red;"><b>Note:</b> Paystack only accepts payment in <b>Naira</b></p>', "adforest-rest-api"),
//    'subsection' => true,
//    'fields' => array(
//        array(
//            'id' => 'paystack_public_key',
//            'type' => 'text',
//            'title' => __('Public Key', 'adforest-rest-api'),
//            'default' => '',
//        // 'desc' => __('Enter the merchant name', 'adforest-rest-api'),
//        ),
//        array(
//            'id' => 'paystack_public_secret_key',
//            'type' => 'text',
//            'title' => __('Secret Key', 'adforest-rest-api'),
//        // 'default' => '',
//        ),
//        array(
//            'id' => 'paystack_receiver_email',
//            'type' => 'text',
//            'title' => __('Receiving Email', 'adforest-rest-api'),
//        // 'default' => '',
//        ),
//    )
//));

Redux::setSection($opt_name, array(
    'title' => __('PayHere Payment Settings', "adforest-rest-api"),
    'id' => 'payhere_payment_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'payhere_pay_Mode',
            'type' => 'button_set',
            'title' => __('PayHere Payment Mode', 'adforest-rest-api'),
            'options' => array(
                'live' => __('Live', 'adforest-rest-api'),
                'sandbox' => __('Sandbox', 'adforest-rest-api'),
            ),
            'default' => 'sandbox',
        ),
        array(
            'id' => 'payhere_mercahnt_id',
            'type' => 'text',
            'title' => __('Merchant id', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_mercahnt_secret_id',
            'type' => 'text',
            'title' => __('Merchant Secret Key', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_first_name',
            'type' => 'text',
            'title' => __('First Name', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_last_name',
            'type' => 'text',
            'title' => __('Last Name', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_email',
            'type' => 'text',
            'title' => __('Email', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_phone',
            'type' => 'text',
            'title' => __('Phone', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_address',
            'type' => 'textarea',
            'title' => __('Address', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_city',
            'type' => 'text',
            'title' => __('City', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_country',
            'type' => 'text',
            'title' => __('Country', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'payhere_currency_sign',
            'type' => 'select',
            'title' => __('Currency Sign', 'adforest-rest-api'),
            'options' => array(
                'USD' => __('USD', 'adforest-rest-api'), //
                'GBP' => __('GBP', 'adforest-rest-api'), //
                'EUR' => __('EUR', 'adforest-rest-api'), //
                'LKR' => __('LKR', 'adforest-rest-api'),
                'AUD' => __('AUD', 'adforest-rest-api'),
            ),
            'default' => 'USD',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('WorldPay Payment Settings', "adforest-rest-api"),
    'id' => 'worldpay_payment_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'worldpay_client_key',
            'type' => 'text',
            'title' => __('Client Key', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'worldpay_service_key',
            'type' => 'text',
            'title' => __('Service Key', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'worldpay_currency_sign',
            'type' => 'select',
            'title' => __('Currency Sign', 'adforest-rest-api'),
            'options' => array(
                'USD' => __('USD', 'adforest-rest-api'),
                'GBP' => __('GBP', 'adforest-rest-api'),
                'EUR' => __('EUR', 'adforest-rest-api'),
                'CAD' => __('CAD', 'adforest-rest-api'),
                'NOK' => __('NOK', 'adforest-rest-api'),
                'SEK' => __('SEK', 'adforest-rest-api'),
                'SGD' => __('SGD', 'adforest-rest-api'),
                'HKD' => __('HKD', 'adforest-rest-api'),
                'DKK' => __('DKK', 'adforest-rest-api'),
            ),
            'default' => 'USD',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Paypal & Braintree Payment Settings', "adforest-rest-api"),
    'id' => 'braintree_payment_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'braintree_pay_Mode',
            'type' => 'button_set',
            'title' => __('Paypal & Braintree Payment Mode', 'adforest-rest-api'),
            'options' => array(
                'live' => __('Live', 'adforest-rest-api'),
                'sandbox' => __('Sandbox', 'adforest-rest-api'),
            ),
            'default' => 'sandbox',
        ),
        array(
            'id' => 'braintree_merchant_id',
            'type' => 'text',
            'title' => __('Merchant Id', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'braintree_public_key',
            'type' => 'text',
            'title' => __('Public Key', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'braintree_private_key',
            'type' => 'text',
            'title' => __('Private Key', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'braintree_token_key',
            'type' => 'text',
            'title' => __('Tokenization Key', 'adforest-rest-api'),
            'default' => '',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Authorize.Net Payment Settings', "adforest-rest-api"),
    'id' => 'authorizenet_payment_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'authorizenet_name',
            'type' => 'text',
            'title' => __('Name', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'authorizenet_transaction_key',
            'type' => 'text',
            'title' => __('Transaction Key', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'authorizenet_reference_num',
            'type' => 'text',
            'title' => __('Reference Number', 'adforest-rest-api'),
            'default' => '',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('InApp Purchase Settings', "adforest-rest-api"),
    'id' => 'api_payment_inapp',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'api-inapp-android-app',
            'type' => 'switch',
            'title' => __('Android InApp Purchase', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('If you have purchased the android app.', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'api-inapp-android-app',
                '=',
                true
            ),
            'id' => 'api_inapp-info1-1',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => __('Info', 'adforest-rest-api'),
            'desc' => __('Go to Application then you will see Development tools option on the left side of menu. Click this option now navigate to Services &APIs. Now you will Licensing & in-app billing section copy the key from here.', 'adforest-rest-api')
        ),
        array(
            'required' => array(
                'api-inapp-android-app',
                '=',
                true
            ),
            'id' => 'inApp_androidSecret',
            'type' => 'textarea',
            'title' => __('Your Android InApp Secret Code Here', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('Enter the secret code you got from store. While copy paste please make sure there is no white space.', 'adforest-rest-api'),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time.'),
            ),
        ),
        array(
            'id' => 'api-inapp-ios-app',
            'type' => 'switch',
            'title' => __('AppStore InApp Purchase', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('If you have purchased the AppStore app.', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'api-inapp-ios-app',
                '=',
                true
            ),
            'id' => 'inApp_iosSecret',
            'type' => 'textarea',
            'title' => __('Your AppStore InApp Secret Code Here', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('Enter the secret code you got from store. While copy paste please make sure there is no white space.', 'adforest-rest-api'),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Once added be carefull editing next time.'),
            ),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Mailchimp API Settings', "adforest-rest-api"),
    'id' => 'api_mailchimp_settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(

        array(
            'id' => 'mailchimp_api_key',
            'type' => 'text',
            'title' => __('MailChimp API Key', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('How to Find it', 'adforest-rest-api') . ' => ' . 'http://kb.mailchimp.com/integrations/api-integrations/about-api-keys',
        ),

    )
));



/*
  Redux::setSection( $opt_name, array(
  'title'      => __( 'PayU Settings', "adforest-rest-api" ),
  'id'         => 'api_payment_payu',
  'desc'       => '',
  'icon' => 'el el-check',
  'subsection' => true,
  'fields'     => array(

  array(
  'id'       => 'appKey_payuMode',
  'type'     => 'button_set',
  'title'    => __( 'PayU Mode', 'adforest-rest-api' ),
  'options'  => array(
  'live' => __('Live', 'adforest-rest-api' ),
  'sandbox' => __('Sandbox', 'adforest-rest-api' ),
  ),
  'default'  => 'live',
  ),
  array(
  'id'       => 'appKey_payumarchantKey',
  'type'     => 'text',
  'title'    => __( 'Enter Your PayU marchant Key', 'adforest-rest-api' ),
  'default'  => '',
  'text_hint' => array(
  'title'   => __( 'Alert', 'adforest-rest-api' ),
  'content' => __( 'Once added be carefull editing next time.' ),
  ),
  'desc'  => __( 'Enter your PayU marchant key here', 'adforest-rest-api' ),

  ),
  array(
  'id'       => 'payu_salt_id',
  'type'     => 'text',
  'title'    => __( 'Salt', 'adforest-rest-api' ),
  'default'  => '',
  'desc'  => __( 'Enter salt', 'adforest-rest-api' ),
  ),

  )

  ));
 */

Redux::setSection($opt_name, array(
    'title' => __('Thank You Settings', "adforest-rest-api"),
    'id' => 'api_payment_thankyou',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'payment_thankyou',
            'type' => 'text',
            'title' => __('Thank You Title', 'adforest-rest-api'),
            'default' => __('Thank You For Your Order', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Ads/Reporting Settings', "adforest-rest-api"),
    'id' => 'api_ads_screen',
    'desc' => '',
    'icon' => 'el el-picture',
    'fields' => array(
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Ads Settings', "adforest-rest-api"),
    'id' => 'api_ads_screen1',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-info-warning0',
            'type' => 'info',
            'style' => 'warning',
            'title' => __('Ads Setting (AdMob)', 'adforest-rest-api'),
            'desc' => __('Here you can set the AdMob settings for the app', 'adforest-rest-api')
        ),
        array(
            'id' => 'api_ad_show',
            'type' => 'switch',
            'title' => __('Show Ads', 'adforest-rest-api'),
            'desc' => __('Trun ads on or off.', 'adforest-rest-api'),
            'default' => false,
        ),
        /* array(
          'id'       => 'api_ad_type',
          'type'     => 'button_set',
          'title'    => __( 'Add Type', 'adforest-rest-api' ),
          'required' => array( 'api_ad_show', '=', '1' ),
          'options'  => array(
          'banner' => __( 'Banner', 'adforest-rest-api' ),
          'interstital' => __( 'Interstital', 'adforest-rest-api' ),
          ),
          'multi'    => true,
          'default'  => 'banner'
          ), */
        array(
            'id' => 'api_ad_type_banner',
            'type' => 'switch',
            'title' => __('Show Banner Ads', 'adforest-rest-api'),
            'subtitle' => __('Turn on or off for banner ads', 'adforest-rest-api'),
            'default' => false,
            'required' => array(
                'api_ad_show',
                '=',
                '1'
            ),
        ),
        array(
            'id' => 'api_ad_position',
            'type' => 'button_set',
            'title' => __('Banner Ad Position', 'adforest-rest-api'),
            'required' => array(
                'api_ad_type_banner',
                '=',
                true
            ),
            'options' => array(
                'top' => __('Top', 'adforest-rest-api'),
                'bottom' => __('Bottom', 'adforest-rest-api'),
            ),
            'default' => 'top'
        ),
        array(
            'id' => 'api_ad_key_banner',
            'type' => 'text',
            'title' => __('Enter Your Ad Key (banner) Android', 'adforest-rest-api'),
            'default' => '',
            'required' => array(
                array(
                    'api-is-buy-android-app',
                    '=',
                    true
                ),
                array(
                    'api_ad_type_banner',
                    '=',
                    true
                )
            ),
            'desc' => __('Please make sure you are putting correct ad id your selected above banner', 'adforest-rest-api'),
        ),
        /* Added For IOS */
        array(
            'id' => 'api_ad_key_banner_ios',
            'type' => 'text',
            'title' => __('Enter Your Ad Key (banner) IOS', 'adforest-rest-api'),
            'default' => '',
            'required' => array(
                array(
                    'api-is-buy-ios-app',
                    '=',
                    true
                ),
                array(
                    'api_ad_type_banner',
                    '=',
                    true
                )
            ),
            'desc' => __('Please make sure you are putting correct ad id your selected above banner', 'adforest-rest-api'),
        ),
        /* Added For IOS   */
        array(
            'id' => 'api_ad_type_initial',
            'type' => 'switch',
            'title' => __('Show Initial Ads', 'adforest-rest-api'),
            'subtitle' => __('Turn on or off for initial ads', 'adforest-rest-api'),
            'default' => false,
            'required' => array(
                'api_ad_show',
                '=',
                '1'
            ),
            'desc' => __('<b style="color:red;">Note : Timer for interstitial ad,s will work only till 2.3.1</b>', 'adforest-rest-api'),
        ),
        array(
            'id' => 'api_ad_key',
            'type' => 'text',
            'title' => __('Enter Your Ad Key (initial) Android', 'adforest-rest-api'),
            'default' => '',
            'required' => array(
                array(
                    'api-is-buy-android-app',
                    '=',
                    true
                ),
                array(
                    'api_ad_type_initial',
                    '=',
                    true
                )
            ),
            'desc' => __('Please make sure you are putting correct ad id your selected above interstital', 'adforest-rest-api'),
        ),
        /* For IOS */
        array(
            'id' => 'api_ad_key_ios',
            'type' => 'text',
            'title' => __('Enter Your Ad Key (initial) IOS', 'adforest-rest-api'),
            'default' => '',
            'required' => array(
                array(
                    'api-is-buy-ios-app',
                    '=',
                    true
                ),
                array(
                    'api_ad_type_initial',
                    '=',
                    true
                )
            ),
            'desc' => __('Please make sure you are putting correct ad id your selected above interstital', 'adforest-rest-api'),
        ),
        /* For IOS ends */
        array(
            'id' => 'api_ad_time_initial',
            'type' => 'text',
            'title' => __('Show 1st Ad After', 'adforest-rest-api'),
            'subtitle' => __('Minumim value should be 20', 'adforest-rest-api'),
            'default' => '',
            'required' => array(
                'api_ad_type_initial',
                '=',
                true
            ),
            'desc' => __('Show 1st ad after specific time. In seconds 1 is for 1 second', 'adforest-rest-api'),
        ),
        array(
            'id' => 'api_ad_time',
            'type' => 'text',
            'title' => __('Show Ad After', 'adforest-rest-api'),
            'subtitle' => __('Minumim value should be 20', 'adforest-rest-api'),
            'default' => '',
            'required' => array(
                'api_ad_type_initial',
                '=',
                true
            ),
            'desc' => __('Show ads next time after specific time. In seconds 1 is for 1 second', 'adforest-rest-api'),
        ),
//        array(
//            'id' => 'ads_reward_switch',
//            'type' => 'switch',
//            'title' => __('Ads Rewards', 'adforest-rest-api'),
//            'subtitle' => __('Turn on or off for reward ads', 'adforest-rest-api'),
//            'default' => false,
//        ),
//        array(
//            'id' => 'ads_reward_limit',
//            'type' => 'text',
//            'title' => __('Ads Reward count', 'adforest-rest-api'),
//            //'subtitle' => __('Minumim value should be 20', 'adforest-rest-api'),
//            'default' => '5',
//            'required' => array(
//                'ads_reward_switch',
//                '=',
//                true
//            ),
//          //  'desc' => __('Show 1st ad after specific time. In seconds 1 is for 1 second', 'adforest-rest-api'),
//        ),
//        array(
//            'id' => 'featured_reward_ads',
//            'type' => 'text',
//            'title' => __('Featured Reward Ads', 'adforest-rest-api'),
//            //'subtitle' => __('Minumim value should be 20', 'adforest-rest-api'),
//            'default' => '1',
//            'required' => array(
//                'ads_reward_switch',
//                '=',
//                true
//            ),
//          //  'desc' => __('Show 1st ad after specific time. In seconds 1 is for 1 second', 'adforest-rest-api'),
//        ),
//        array(
//            'id' => 'bumpup_reward_ads',
//            'type' => 'text',
//            'title' => __('Bump Up Reward Ads', 'adforest-rest-api'),
//            //'subtitle' => __('Minumim value should be 20', 'adforest-rest-api'),
//            'default' => '1',
//            'required' => array(
//                'ads_reward_switch',
//                '=',
//                true
//            ),
//          //  'desc' => __('Show 1st ad after specific time. In seconds 1 is for 1 second', 'adforest-rest-api'),
//        ),
//        array(
//            'id' => 'free_reward_ads',
//            'type' => 'text',
//            'title' => __('Simple Reward Ads', 'adforest-rest-api'),
//            //'subtitle' => __('Minumim value should be 20', 'adforest-rest-api'),
//            'default' => '1',
//            'required' => array(
//                'ads_reward_switch',
//                '=',
//                true
//            ),
//          //  'desc' => __('Show 1st ad after specific time. In seconds 1 is for 1 second', 'adforest-rest-api'),
//        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Analytics Settings', "adforest-rest-api"),
    'id' => 'api_ads_screen2',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-info-warning1',
            'type' => 'info',
            'style' => 'warning',
            'title' => __('App Analytics', 'adforest-rest-api'),
            'desc' => __('Below you can setup analytics for the app.', 'adforest-rest-api')
        ),
        array(
            'id' => 'api_analytics_show',
            'type' => 'switch',
            'title' => __('Make Analytics', 'adforest-rest-api'),
            'desc' => __('Trun ads on or off.', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'api_analytics_id',
            'type' => 'text',
            'title' => __('Analytics ID', 'adforest-rest-api'),
            'default' => '',
            'required' => array(
                'api_analytics_show',
                '=',
                true
            ),
            'desc' => __('Put analytics id here i.e.', 'adforest-rest-api') . ' UA-XXXXXXXXX-X',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Firebase Settings', "adforest-rest-api"),
    'id' => 'api_ads_screen3',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-info-warning2',
            'type' => 'info',
            'style' => 'warning',
            'title' => __('Puch Nofifications', 'adforest-rest-api'),
            'desc' => __('Below you can setup Puch Nofifications for the app.', 'adforest-rest-api')
        ),
        array(
            'id' => 'api_firebase_id',
            'type' => 'text',
            'title' => __('Firebase API KEY', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('Put firebase api key', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Menu Settings', "adforest-rest-api"),
    'id' => 'api_menu_settings',
    'desc' => '',
    'icon' => 'el el-align-justify',
    'fields' => array(
        array(
            'id' => 'api-sortable-app-switch',
            'type' => 'switch',
            'title' => __('Turn Custom Menu', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Turn on custom menu settings', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'api-sortable-app-switch',
                '=',
                true
            ),
            'id' => 'api-sortable-app-menu',
            'type' => 'text',
            'title' => __('Menu Title Control', 'adforest-rest-api'),
            'desc' => __('Chnage menu title to what you want.', 'adforest-rest-api'),
            'label' => true,
            'options' => array(
                'home' => __("Home", "adforest-rest-api"),
                'profile' => __("Profile", "adforest-rest-api"),
                'search' => __("Advance Search", "adforest-rest-api"),
                'messages' => __("Messages", "adforest-rest-api"),
                'packages' => __("Packages", "adforest-rest-api"),
                'my_ads' => __("My Ads", "adforest-rest-api"),
                'inactive_ads' => __("Inactive Ads", "adforest-rest-api"),
                'featured_ads' => __("Featured Ads", "adforest-rest-api"),
                'fav_ads' => __("Fav Ads", "adforest-rest-api"),
                'rejected_ads' => __("Rejected Ads", "adforest-rest-api"),
                'most_visited_ads' => __("Most Visited Ads", "adforest-rest-api"),
                'expire_sold_ads' => __("Expire/Sold Ads", "adforest-rest-api"),
                'shop' => __("Shop", "adforest-rest-api"),
                'sellers' => __("Sellers", "adforest-rest-api"),
                'pages' => __("Pages", "adforest-rest-api"),
                'others' => __("Others", "adforest-rest-api"),
                'blog' => __("Blog", "adforest-rest-api"),
                'app_settings' => __("Settings", "adforest-rest-api"),
                'logout' => __("Logout", "adforest-rest-api"),
                'login' => __("Login", "adforest-rest-api"),
                'register' => __("Register", "adforest-rest-api"),
                'wpml_menu_text' => __("WPML Menu Text", "adforest-rest-api"),
                'top_location_text' => __("Site Location Text", "adforest-rest-api"),
            )
        ),
        array(
            'id' => 'api-menu-message-count',
            'type' => 'switch',
            'title' => __('Show Message Count', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Turn on/off Show Message Count in menu.', 'adforest-rest-api'),
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Hide From Menu', "adforest-rest-api"),
    'id' => 'api_menu_settings_hide',
    'desc' => __('You can hide the below items from the menu.', "adforest-rest-api"),
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'api-menu-hide-message-menu',
            'type' => 'switch',
            'title' => __('Messages', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'api-menu-hide-package-menu',
            'type' => 'switch',
            'title' => __('Package', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'api-menu-hide-blog-menu',
            'type' => 'switch',
            'title' => __('Blog', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sellers-show-menu',
            'type' => 'switch',
            'title' => __('Sellers Menu', 'adforest-rest-api'),
            'subtitle' => __('Show Sellers In Menu', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'settings-show-menu',
            'type' => 'switch',
            'title' => __('Settings Menu', 'adforest-rest-api'),
            'subtitle' => __('Show Settings In Menu', 'adforest-rest-api'),
            'default' => false,
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Home Screen', "adforest-rest-api"),
    'id' => 'api_home_screen',
    'desc' => '',
    'icon' => 'el el-home',
    'fields' => array(
        array(
            'id' => 'sb_home_screen_title',
            'type' => 'text',
            'title' => __('Screen Title', 'adforest-rest-api'),
            'default' => __('Home Screen', 'adforest-rest-api'),
            'desc' => __('Set the title for homescreen', 'adforest-rest-api'),
        ),






        array(
            'id' => 'sb_homescreen_style',
            'type' => 'button_set',
            'title' => __('Home Style', 'adforest-rest-api'),
            'options' => array(
                'home1' => __('Home 1', 'adforest-rest-api'),
                'home2' => __('Home 2', 'adforest-rest-api'),
                'home3' => __('Home 3', 'adforest-rest-api'),
            ),
            'desc' => __('Home 1 Layout is old layout.', 'adforest-rest-api'),
            'default' => 'home1'
        ),   


        array(
            'id' => 'sb_homescreen_layout',
            'type' => 'button_set',
            'title' => __('Home Screen Layout', 'adforest-rest-api'),
            'options' => array(
                'default' => __('Default', 'adforest-rest-api'),
                'horizental' => __('Horizental', 'adforest-rest-api'),
                'vertical' => __('Vertical', 'adforest-rest-api'),
            ),
            'default' => 'default',
            'required' => array( 'sb_homescreen_style',  '=',  'home1' ),
        ),  



    )
));

Redux::setSection($opt_name, array(
    'title' => __("Sortable Settings", "adforest-rest-api"),
    'id' => 'api_home_is_sortable',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'home-notice-info0',
            'type' => 'info',
            'style' => 'info',
            'title' => __('Sort Home Screen Options', 'adforest-rest-api'),
        ),
        array(
            'id' => 'home-screen-sortable-enable',
            'type' => 'switch',
            'title' => __('Home Sortable', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Sort home sections here', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'home-screen-sortable-enable',
                '=',
                true
            ),
            'id' => 'home-screen-sortable',
            'type' => 'sortable',
            'mode' => 'checkbox',
            'title' => __('Sortable Sections', 'adforest-rest-api'),
            'desc' => __('Sort section layouts on homescreen', 'adforest-rest-api'),
            'options' => array(
                'cat_icons' => __('Category Icons', 'adforest-rest-api'),
                'featured_ads' => __('Featured Slider', 'adforest-rest-api'),
                'latest_ads' => __('Latest Ads', 'adforest-rest-api'),
                'sliders' => __('Category Slider Ads', 'adforest-rest-api'),
                'cat_locations' => __('Locations Icons', 'adforest-rest-api'),
                'nearby' => __('Nearby Ads', 'adforest-rest-api'),
                'blogNews' => __('Blog/News', 'adforest-rest-api'),
                'crousel' => __('Crousel panel', 'adforest-rest-api'),
            ),
            'default' => array(
                'cat_icons' => __('Category Icons', 'adforest-rest-api'),
                'sliders' => __('Simple Ads', 'adforest-rest-api'),
            )
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Home Search Section", "adforest-rest-api"),
    'id' => 'api_home_search_section',
    'desc' => __("No, sorting option is available. If you turn it on it will always show on top.", "adforest-rest-api"),
    'subsection' => true,
    'fields' => array(
        /* featured ads */
        array(
            'id' => 'search_section_show',
            'type' => 'switch',
            'title' => __('Show Search Section', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show search section on homescreen at top.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'search_section_show_title',
            'required' => array(
                'search_section_show',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Search Main Text', 'adforest-rest-api'),
            'default' => __('Search From Thousands Of Ads', 'adforest-rest-api'),
        ),
        array(
            'id' => 'search_section_show_subtitle',
            'required' => array(
                'search_section_show',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Search Sub Text', 'adforest-rest-api'),
            'default' => __('Here You can search', 'adforest-rest-api'),
        ),
        array(
            'id' => 'search_section_show_placeholder',
            'required' => array(
                'search_section_show',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Input Placehoder', 'adforest-rest-api'),
            'default' => __('Search with keywords...', 'adforest-rest-api'),
        ),
        array(
            'id' => 'search_section_show_bg_image',
            'required' => array(
                'search_section_show',
                '=',
                true
            ),
            'type' => 'media',
            'url' => true,
            'title' => __('Background Image', 'adforest-rest-api'),
            'compiler' => 'true',
            'desc' => __('You must select the section background image here', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/search-bg-img.png"
            ),
        ),
        array(
            'id' => 'search_section_loc_show',
            'type' => 'switch',
            'title' => __('Show location Section', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show search location section on homescreen at top.', 'adforest-rest-api'),
            'required' => array(
                'search_section_show',
                '=',
                true
            ),
        ),
        array(
            'id' => 'search_section_loc_show_placeholder',
            'required' => array(
                'search_section_loc_show',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('location Input Placehoder', 'adforest-rest-api'),
            'default' => __('Search with locations...', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Categories Icons", "adforest-rest-api"),
    'id' => 'api_home_ads_cat_icons',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'api_cat_columns',
            'type' => 'button_set',
            'title' => __('Categories Columns', 'adforest-rest-api'),
            'desc' => __('Select number of info columns', 'adforest-rest-api'),
            'options' => array(
                '3' => __('3 Column', 'adforest-rest-api'),
                '4' => __('4 Columns', 'adforest-rest-api'),
            ),
            'default' => '3'
        ),
        array(
            'id' => 'adforest-api-ad-cats-multi',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => 'ad_cats',
                'hide_empty' => false,
            /* 'taxonomies' => array( 'ad_cats' ), */
            ),
            'ajax'=>true,
            'multi' => true,
            'sortable' => true,
            'title' => __('Select Categories', 'adforest-rest-api'),
            'desc' => __('Select categories to show on home screen as icons.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'adforest-api-ad-cats-default-icon',
            'type' => 'media',
            'url' => true,
            'title' => __('Default Category Icon', 'adforest-rest-api'),
            'compiler' => 'true',
            'desc' => __('Default Icon For categories on homepage. You can leave it empty if you want to show default for app.', 'adforest-rest-api'),
            'subtitle' => __('Dimensions: 128 X 128', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/placeholder.png"
            ),
        ),
        array(
            'id' => 'adforest-api-ad-cats-show-btn',
            'type' => 'switch',
            'title' => __('Show Button', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show view all button under categories icons section', 'adforest-rest-api'),
        ),
        array(
            'id' => 'adforest-api-ad-cats-show-text',
            'required' => array('adforest-api-ad-cats-show-btn', '=', true),
            'type' => 'text',
            'title' => __('Button Text', 'adforest-rest-api'),
            'default' => __('View All Categories', 'adforest-rest-api'),
        ),
        array(
            'id' => 'adforest-api-ad-cats-show-count',
            'type' => 'switch',
            'title' => __('Show Count', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show count with titles', 'adforest-rest-api'),
            'required' => array('adforest-api-ad-cats-show-btn', '=', true),
        ),
        array(
            'id' => 'home_cat_icons_setion_title',
            'type' => 'text',
            'title' => __('Section Title', 'adforest-rest-api'),
            'default' => '',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("Categories Slider", "adforest-rest-api"),
    'id' => 'api_home_ads_cat_slider',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'adforest-api-ad-cats-slider',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => 'ad_cats',
                'hide_empty' => true,
            /* 'taxonomies' => array( 'ad_cats' ), */
            ),
            'multi' => true,
            'sortable' => true,
            'title' => __('Select Slider Categories', 'adforest-rest-api'),
        ),
        array(
            'id' => 'slider_ad_limit',
            'type' => 'slider',
            'title' => __('Slider Posts Limit', 'adforest-rest-api'),
            'subtitle' => __('On homepage', 'adforest-rest-api'),
            'desc' => __('Select Number of slider posts', 'adforest-rest-api'),
            'default' => 5,
            'min' => 1,
            'step' => 1,
            'max' => 10,
            'display_value' => 'label'
        ),
        array(
            'id' => 'cat_slider_screen_layout',
            'type' => 'button_set',
            'title' => __('Category Slider Layout', 'adforest-rest-api'),
            'options' => array(
                'default' => __('Default', 'adforest-rest-api'),
                'horizental' => __('horizontal', 'adforest-rest-api'),
                'vertical' => __('Vertical', 'adforest-rest-api'),
            ),
            'default' => 'default',
        ),
    )
));





/* Redux::setSection( $opt_name, array(
  'title'      => __( "Featured Ads", "adforest-rest-api" ),
  'id'         => 'api_home_latest',
  'desc'       => '',
  'subsection' => true,
  'fields'     => array(
  /*latets ads *-/

  )
  ) ); */

Redux::setSection($opt_name, array(
    'title' => __("Crousel Slider", "adforest-rest-api"),
    'id' => 'api_crousel_slider',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        /* featured ads */
        array(
             'id'          => 'crousel_slides',
             'type'        => 'slides',
             'title'       => __( 'Slider on Home page', 'adforest-rest-api' ),
             'subtitle'    => __( 'You can add multiple slides and these will show on home screen .', 'adforest-rest-api' ),
             'desc'        => __( 'You can drag and drop to re-arrange', 'adforest-rest-api' ),                         
                              'placeholder' => array(
                                    'title'       => 'adforest-rest-api',
                                    'description' =>  'You need to  add web , category , or profile here , in case of web click on image will redirect to the given url, category will redirect to search page to given category id below,and profile will redirect to the user profile ',
                                    'url'=>'Desired url in case of web ,category id for category option and user id will be required in case of profile',
                                ),
            'show' => array(
            'title' => true,
            'description' => true,
            'url' => true,
                        ),
                            ),

        array(
            'id' => 'sb_enable_carousel_slider_scroll',
            'type' => 'switch',
            'title' => __('Enable Scroll On Featured Ads', 'adforest-rest-api'),
            'desc' => __('Turn on/off auto scroll on the home page for carousel', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_enable_carousel_slider_duration',
            'type' => 'text',
            'title' => __('carousel Scroll Speed', 'adforest-rest-api'),
            'default' => 3000,
            'required' => array(
                'sb_enable_carousel_slider_scroll',
                '=',
                true
            ),
            'text_hint' => array(
                'title' => __('Alert', 'adforest-rest-api'),
                'content' => __('Minimum value should be 3000', 'adforest-rest-api'),
            ),
            'desc' => __('Minimum value should be 3000. Enter value in milisecons 1000 is 1 second. ', 'adforest-rest-api'),
        ),
        
    )));


Redux::setSection($opt_name, array(
    'title' => __("Featured Ads", "adforest-rest-api"),
    'id' => 'api_home_ads_featured',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        /* featured ads */
        array(
            'id' => 'feature_on_home',
            'type' => 'switch',
            'title' => __('Featured Ads', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show featured ads slider', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_home_ads_title',
            'required' => array(
                'feature_on_home',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Featured Ads Section Title', 'adforest-rest-api'),
            'default' => 'Featured Ads',
        ),
        array(
            'id' => 'home_related_posts_count',
            'required' => array(
                'feature_on_home',
                '=',
                true
            ),
            'type' => 'slider',
            'title' => __('Featured Posts', 'adforest-rest-api'),
            'subtitle' => __('On homepage', 'adforest-rest-api'),
            'desc' => __('Select Number of featured posts', 'adforest-rest-api'),
            'default' => 5,
            'min' => 1,
            'step' => 1,
            'max' => 150,
            'display_value' => 'label'
        ),
        array(
            'id' => 'home_featured_position',
            'type' => 'button_set',
            'title' => __('Featured Ads Position', 'adforest-rest-api'),
            'options' => array(
                '1' => __('Top', 'adforest-rest-api'),
                '2' => __('Middle', 'adforest-rest-api'),
                '3' => __('Bottom', 'adforest-rest-api'),
            ),
            'default' => '1',
            'required' => array(
                'feature_on_home',
                '=',
                true
            ),
        ),
        array(
            'id' => 'fetaured_screen_layout',
            'type' => 'button_set',
            'title' => __('Featured Screen Layout', 'adforest-rest-api'),
            'options' => array(
                'default' => __('Default', 'adforest-rest-api'),
                'horizental' => __('horizontal', 'adforest-rest-api'),
                'vertical' => __('Vertical', 'adforest-rest-api'),
            ),
            'default' => 'default',
            'required' => array(
                'feature_on_home',
                '=',
                true
            ),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Latest Ads", "adforest-rest-api"),
    'id' => 'api_home_latest',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        /* latets ads */

        array(
            'id' => 'home-notice-info2',
            'type' => 'info',
            'style' => 'info',
            'title' => __('Latest Ads Section', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'home-screen-sortable-enable',
                '=',
                true
            ),
            'id' => 'latest_on_home',
            'type' => 'switch',
            'title' => __('Latest Ads', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show latest ads slider', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_home_latest_ads_title',
            'required' => array(
                'latest_on_home',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Latest Ads Section Title', 'adforest-rest-api'),
            'default' => 'Latest Ads',
        ),
        array(
            'id' => 'home_latest_posts_count',
            'required' => array(
                'latest_on_home',
                '=',
                true
            ),
            'type' => 'slider',
            'title' => __('Latest Ads', 'adforest-rest-api'),
            'subtitle' => __('On homepage', 'adforest-rest-api'),
            'desc' => __('Select Number of latest ads', 'adforest-rest-api'),
            'default' => 5,
            'min' => 1,
            'step' => 1,
            'max' => 150,
            'display_value' => 'label'
        ),
        array(
            'id' => 'latest_screen_layout',
            'type' => 'button_set',
            'title' => __('Latest Screen Layout', 'adforest-rest-api'),
            'options' => array(
                'default' => __('Default', 'adforest-rest-api'),
                'horizental' => __('Horizontal', 'adforest-rest-api'),
                'vertical' => __('Vertical', 'adforest-rest-api'),
            ),
            'default' => 'default',
            'required' => array(
                'latest_on_home',
                '=',
                true
            ),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Near By Ads", "adforest-rest-api"),
    'id' => 'api_home_nearby',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        /* latets ads */

        array(
            'id' => 'home-notice-nearby-info2',
            'type' => 'info',
            'style' => 'info',
            'title' => __('Near By Ads Section', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'home-screen-sortable-enable',
                '=',
                true
            ),
            'id' => 'nearby_on_home',
            'type' => 'switch',
            'title' => __('Nearby Ads', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show near by ads slider', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_home_nearby_ads_title',
            'required' => array(
                'nearby_on_home',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Nearby Ads Section Title', 'adforest-rest-api'),
            'default' => 'Near By Ads',
        ),
        array(
            'id' => 'home_nearby_posts_count',
            'required' => array(
                'nearby_on_home',
                '=',
                true
            ),
            'type' => 'slider',
            'title' => __('Nearby Ads', 'adforest-rest-api'),
            'subtitle' => __('On homepage', 'adforest-rest-api'),
            'desc' => __('Select max number of nearby ads', 'adforest-rest-api'),
            'default' => 5,
            'min' => 1,
            'step' => 1,
            'max' => 150,
            'display_value' => 'label'
        ),
         array(
            'id' => 'nearby_screen_layout',
            'type' => 'button_set',
            'title' => __('Nearby Screen Layout', 'adforest-rest-api'),
            'options' => array(
                'default' => __('Default', 'adforest-rest-api'),
                'horizental' => __('horizontal', 'adforest-rest-api'),
                'vertical' => __('Vertical', 'adforest-rest-api'),
            ),
            'default' => 'default',
            'required' => array(
                'nearby_on_home',
                '=',
                true
            ),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Ads Locations", "adforest-rest-api"),
    'id' => 'api_home_locations',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        /* locations */
        array(
            'id' => 'home-notice-info5',
            'type' => 'info',
            'style' => 'info',
            'title' => __('Locations icons Section', 'adforest-rest-api'),
        ),
        array(
            'id' => 'api_location_title',
            'required' => array(
                'home-screen-sortable-enable',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Location Section Title', 'adforest-rest-api'),
            'default' => __('Locations', 'adforest-rest-api'),
        ),
        /* array(
          'required' 		=> array( 'home-screen-sortable-enable', '=', true ),
          'id'       => 'api_location_columns',
          'type'     => 'button_set',
          'title'    => __( 'Locations Columns', 'adforest-rest-api' ),
          'desc'     => __( 'Select number of info columns', 'adforest-rest-api' ),
          'options'  => array(
          '1' => __( '1 Column', 'adforest-rest-api' ),
          '2' => __( '2 Columns', 'adforest-rest-api' ),
          ),
          'default'  => '2'
          ), */
        array(
            'required' => array(
                'home-screen-sortable-enable',
                '=',
                true
            ),
            'id' => 'adforest-api-ad-loc-multi',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => 'ad_country',
                'hide_empty' => false,
            ),
            'multi' => true,
            'sortable' => true,
            'title' => __('Select Locations Categories', 'adforest-rest-api'),
            'desc' => __('Select locations you want to show', 'adforest-rest-api'),
        ),
        array(
            'id' => 'adforest-api-ad-location-default-icon',
            'type' => 'media',
            'url' => true,
            'title' => __('Default Location Icon', 'adforest-rest-api'),
            'compiler' => 'true',
            'desc' => __('Default Icon For Location on homepage.  You can leave it empty if you want to show default for app.', 'adforest-rest-api'),
            'subtitle' => __('Dimensions: 230 x 230', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/placeholder-location.png"
            ),
        ),
        array(
            'id' => 'adforest-api-ad-location-show-btn',
            'type' => 'switch',
            'title' => __('Show Button', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show view all button under categories icons section', 'adforest-rest-api'),
        ),
        array(
            'id' => 'adforest-api-ad-location-show-text',
            'required' => array('adforest-api-ad-location-show-btn', '=', true),
            'type' => 'text',
            'title' => __('Button Text', 'adforest-rest-api'),
            'default' => __('View All Location', 'adforest-rest-api'),
        ),
        
         array(
            'id' => 'adforest-api-ad-location-count-btn',
            'type' => 'switch',
            'title' => __('Show count Button', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show count on ad location', 'adforest-rest-api'),
        ),
        array(
            'id' => 'adlocation_style',
            'type' => 'button_set',
            'title' => __('Ad Location Style', 'adforest-rest-api'),
            'options' => array(
                'style1' => __('Style 1', 'adforest-rest-api'),
                'style2' => __('Style 2', 'adforest-rest-api'),
            ),
            'default' => 'style1',
        ),     
        array(
            'id' => 'location_screen_layout',
            'type' => 'button_set',
            'title' => __('Location Screen Layout', 'adforest-rest-api'),
            'options' => array(
                
                'horizental' => __('Horizontal', 'adforest-rest-api'),
                'vertical' => __('Vertical', 'adforest-rest-api'),
            ),
            'default' => 'horizental', 
            'desc' => __('This option is only valid for Home 1.', 'adforest-rest-api'),      
        ),  
        
        
    /* array(
      'id' => 'adforest-api-ad-location-show-count',
      'type' => 'switch',
      'title' => __('Show Count', 'adforest-rest-api') ,
      'default' => false,
      'desc' => __('Show count with titles', 'adforest-rest-api') ,
      'required' => array( 'adforest-api-ad-location-show-btn',  '=',  true  ) ,
      ) , */
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Blog/News", "adforest-rest-api"),
    'id' => 'api_home_blogNews',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        /* locations */
        array(
            'id' => 'home-notice-info7',
            'type' => 'info',
            'style' => 'info',
            'title' => __('Blog and news section. (You must on Home Sortable to show blogs)', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'home-screen-sortable-enable',
                '=',
                true
            ),
            'id' => 'posts_blogNews_home',
            'type' => 'switch',
            'title' => __('News/Blog', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show News/Blog ads slider', 'adforest-rest-api'),
        ),
        array(
            'id' => 'api_blogNews_title',
            'required' => array(
                'posts_blogNews_home',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Blog/News Setion Title', 'adforest-rest-api'),
            'default' => __('Blog/News', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'posts_blogNews_home',
                '=',
                true
            ),
            'id' => 'adforest-api-blogNews-multi',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => 'category',
                'hide_empty' => false,
            ),
            'multi' => true,
            'sortable' => true,
            'title' => __('Select Categories', 'adforest-rest-api'),
            'desc' => __('Select categories to show in the blog/news section. Leave empty if you want to show from all.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'home_blogNews_posts_count',
            'required' => array(
                'posts_blogNews_home',
                '=',
                true
            ),
            'type' => 'slider',
            'title' => __('Number of Posts', 'adforest-rest-api'),
            'subtitle' => __('On homepage', 'adforest-rest-api'),
            'desc' => __('Select max number of Posts to show', 'adforest-rest-api'),
            'default' => 5,
            'min' => 1,
            'step' => 1,
            'max' => 150,
            'display_value' => 'label'
        ),
    /* array(
      'required' 		=> array( 'home-screen-sortable-enable', '=', true ),
      'id'       => 'api_location_columns',
      'type'     => 'button_set',
      'title'    => __( 'Locations Columns', 'adforest-rest-api' ),
      'desc'     => __( 'Select number of info columns', 'adforest-rest-api' ),
      'options'  => array(
      '1' => __( '1 Column', 'adforest-rest-api' ),
      '2' => __( '2 Columns', 'adforest-rest-api' ),
      ),
      'default'  => '2'
      ),
     */
    )
));

/* Home Complete Ends Here */
$payments_gateways = array();
if (class_exists('woocommerce')) {
    $gateways = get_option('woocommerce_gateway_order');

    if (isset($gateways) && is_array($gateways) && count($gateways) > 0) {
        foreach ($gateways as $key => $val) {
            $payments_gateways[$key] = $key;
        }
    }
}
Redux::setSection($opt_name, array(
    'title' => __("Ad's General Settings", "adforest-rest-api"),
    'id' => 'api_ad_posts',
    'desc' => '',
    'icon' => 'el el-adjust-alt',
    'fields' => array(
        array(
            'id' => 'sb_location_allowed',
            'type' => 'switch',
            'title' => __('Allowed all countries', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_list_allowed_country',
            'type' => 'select',
            'options' => adforestAPI_get_all_countries(),
            'multi' => false,
            'title' => __('Select Countries', 'adforest-rest-api'),
            'required' => array( 'sb_location_allowed', '=',  array(  '0' ) ),
            'desc' => __('You can select only 1 country.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'communication_mode',
            'type' => 'button_set',
            'title' => __('Communications Mode', 'adforest-rest-api'),
            'options' => array(
                'phone' => __('Phone', 'adforest-rest-api'),
                'message' => __('Messages', 'adforest-rest-api'),
                'both' => __('Both', 'adforest-rest-api'),
            ),
            'default' => 'both'
        ),
        array(
            'id' => 'sb_order_auto_approve',
            'type' => 'switch',
            'title' => __('Package order auto approval', 'adforest-rest-api'),
            'subtitle' => __('after payment', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_order_auto_approve_disable',
            'type' => 'select',
            'options' => $payments_gateways,
            'multi' => true,
            'title' => __('Select Payments Gateways', 'adforest'),
            'required' => array('sb_order_auto_approve', '=', '1'),
            'subtitle' => __('Disable Payments auto approval', 'adforest'),
            'desc' => __('Selected payments gateway order will not be auto approved.', 'adforest'),
        ),
        array(
            'id' => 'sb_send_email_on_ad_post',
            'type' => 'switch',
            'title' => __('Send email on Ad Post', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'ad_post_email_value',
            'type' => 'text',
            'title' => __('Email for notification.', 'adforest-rest-api'),
            'required' => array(
                'sb_send_email_on_ad_post',
                '=',
                '1'
            ),
            'default' => get_option('admin_email'),
        ),
        array(
            'id' => 'sb_send_email_on_message',
            'type' => 'switch',
            'title' => __('Send email on message', 'adforest-rest-api'),
            'desc' => __('When someone drop a message on ad then email send to concern user.', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_currency',
            'type' => 'text',
            'title' => __('Currency', 'adforest-rest-api'),
            'desc' => adforestAPI_make_link('http://htmlarrows.com/currency/', __('List of Currency', 'adforest-rest-api')) . " " . esc_attr__('You can use HTML code or text as well like USD etc', 'adforest-rest-api'),
            'default' => '$',
        ),
        array(
            'id' => 'sb_price_direction',
            'type' => 'select',
            'options' => array(
                'left' => 'Left',
                'right' => 'Right'
            ),
            'title' => __('Price direction', 'adforest-rest-api'),
            'default' => 'left',
        ),
        array(
            'id' => 'sb_price_separator',
            'type' => 'text',
            'title' => __('Thousands Separator', 'adforest-rest-api'),
            'default' => ',',
        ),
        array(
            'id' => 'sb_price_decimals',
            'type' => 'text',
            'title' => __('Decimals', 'adforest-rest-api'),
            'desc' => __('It should be 0 for no decimals.', 'adforest-rest-api'),
            'default' => '2',
        ),
        array(
            'id' => 'sb_price_decimals_separator',
            'type' => 'text',
            'title' => __('Decimals Separator', 'adforest-rest-api'),
            'default' => '.',
        ),
        array(
            'id' => 'sb_ad_approval',
            'type' => 'select',
            'options' => array(
                'auto' => 'Auto Approved',
                'manual' => 'Admin manual approval'
            ),
            'title' => __('Ad Approval', 'adforest-rest-api'),
            'default' => 'auto',
        ),
        array(
            'id' => 'sb_update_approval',
            'type' => 'select',
            'options' => array(
                'auto' => 'Auto Approved',
                'manual' => 'Admin manual approval'
            ),
            'title' => __('Ad Update Approval', 'adforest-rest-api'),
            'default' => 'auto',
        ),
        array(
            'id' => 'email_on_ad_approval',
            'type' => 'switch',
            'title' => __('Email to Ad owner on approval', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'report_options',
            'type' => 'text',
            'title' => __('Report ad Options', 'adforest-rest-api'),
            'default' => 'Spam|Offensive|Duplicated|Fake',
        ),
        array(
            'id' => 'report_limit',
            'type' => 'text',
            'title' => __('Ad Report Limit', 'adforest-rest-api'),
            'desc' => __('Only integer value without spaces.', 'adforest-rest-api'),
            'default' => 10,
        ),
        array(
            'id' => 'report_action',
            'type' => 'select',
            'title' => __('Action on Ad Report Limit', 'adforest-rest-api'),
            'options' => array(
                1 => 'Auto Inactive',
                2 => 'Email to Admin'
            ),
            'default' => 1,
        ),
        array(
            'id' => 'report_email',
            'type' => 'text',
            'title' => __('Email', 'adforest-rest-api'),
            'desc' => __('Email where you want to get notify.', 'adforest-rest-api'),
            'required' => array(
                'report_action',
                '=',
                array(
                    2
                )
            ),
            'default' => get_option('admin_email'),
        ),
        array(
            'id' => 'default_related_image',
            'type' => 'media',
            'url' => true,
            'title' => __('Default Image', 'adforest-rest-api'),
            'compiler' => 'true',
            'desc' => __('If there is no image of ad then this will be show.', 'adforest-rest-api'),
            'subtitle' => __('Dimensions: 300 x 225', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/default-img.png"
            ),
        ),
        array(
            'id' => 'ads_images_sizes',
            'type' => 'button_set',
            'title' => __('Set Image Sizes for listings', 'adforest-rest-api'),
            'options' => array(
                'default' => __('Default', 'adforest-rest-api'),
                'size2' => __('Size 2', 'adforest-rest-api'),
                'size3' => __('Size 3', 'adforest-rest-api'),
                'size4' => __('Size 4', 'adforest-rest-api'),
                'size5' => __('Size 5', 'adforest-rest-api'),
            ),
            'default' => 'default',
            'desc' => __('Change with caution we only recommend default.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'ads_images_sizes_adDetils',
            'type' => 'button_set',
            'title' => __('Set Image Sizes for Ad Details', 'adforest-rest-api'),
            'options' => array(
                'default' => __('Default', 'adforest-rest-api'),
                'size2' => __('Size 2', 'adforest-rest-api'),
            ),
            'default' => 'default',
            'desc' => __('Change with caution we only recommend default.', 'adforest-rest-api'),
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Ad Post Settings", "adforest-rest-api"),
    'id' => 'api_ad_post_settings',
    'desc' => '',
    'icon' => 'el el-home',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'adpost_cat_template',
            'type' => 'switch',
            'title' => __('Turn On Category Template', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_default_img_required',
            'type' => 'switch',
            'title' => __('Images Required', 'adforest'),
            'subtitle' => __('Enable/Disable Images Required for ad post default template.', 'adforest'),
            'default' => false,
            'required' => array('adpost_cat_template', '=', array(false)),
        ),
        array(
            'id' => 'restrict_phone_show',
            'type' => 'button_set',
            'title' => __('Restrict Phone Number', 'adforest'),
            'desc' => __('Restrict phone number to show all or to login users only.', 'adforest'),
            'options' => array(
                'all' => __('All', 'adforest'),
                'login_only' => __('Login Only', 'adforest'),
            ),
            'default' => 'all'
        ),
        array(
            'id' => 'ad_post_restriction',
            'type' => 'button_set',
            'title' => __('Posting Allowed', 'adforest'),
            'subtitle' => __('Restrict Users to ad post with phone verication or not.', 'adforest'),
            'desc' => __('<b>Note:</b> In case of <b>"Phone verified users"</b> Please enable the <b>"Phone verfication"</b> in <b>Dashboard >> Apps API Options >> Users >> Phone verfication</b>', 'adforest'),
            'options' => array(
                'all' => __('All Users', 'adforest'),
                'phn_verify' => __('Phone Verified Users', 'adforest'),
            ),
            'default' => 'all',
        ),
        array(
            'id' => 'admin_allow_unlimited_ads',
            'type' => 'switch',
            'title' => __('Post unlimited free ads', 'adforest-rest-api'),
            'subtitle' => __('For Administrator', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_standard_images_size',
            'type' => 'switch',
            'title' => __('Strict image mode', 'adforest-rest-api'),
            'subtitle' => __('Not allowed less than 760x410', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_allow_ads',
            'type' => 'switch',
            'title' => __('Free Ads', 'adforest-rest-api'),
            'subtitle' => __('For new user', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_free_ads_limit',
            'type' => 'text',
            'title' => __('Free Ads limit', 'adforest-rest-api'),
            'required' => array(
                'sb_allow_ads',
                '=',
                array(
                    true
                )
            ),
            'subtitle' => __('For new user', 'adforest-rest-api'),
            'desc' => __('It must be an inter value, -1 means unlimited.', 'adforest-rest-api'),
            'default' => - 1,
        ),
        array(
            'id' => 'sb_allow_featured_ads',
            'type' => 'switch',
            'title' => __('Free Featured Ads', 'adforest-rest-api'),
            'subtitle' => __('For new user', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_featured_ads_limit',
            'type' => 'text',
            'title' => __('Featured Ads limit', 'adforest-rest-api'),
            'subtitle' => __('For new user', 'adforest-rest-api'),
            'required' => array(
                'sb_allow_featured_ads',
                '=',
                array(
                    true
                )
            ),
            'desc' => __('It must be an inter value, -1 means unlimited.', 'adforest-rest-api'),
            'default' => 1,
        ),
        array(
            'id' => 'sb_allow_bump_ads',
            'type' => 'switch',
            'title' => __('Free Bump Ads', 'adforest-rest-api'),
            'subtitle' => __('For new user', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_bump_ads_limit',
            'type' => 'text',
            'title' => __('Bump Ads limit', 'adforest-rest-api'),
            'subtitle' => __('For new user', 'adforest-rest-api'),
            'required' => array(
                'sb_allow_bump_ads',
                '=',
                array(
                    true
                )
            ),
            'desc' => __('It must be an inter value, -1 means unlimited.', 'adforest-rest-api'),
            'default' => 1,
        ),
        array(
            'id' => 'sb_allow_free_bump_up',
            'type' => 'switch',
            'title' => __('Free Bump Ads for all users', 'adforest-rest-api'),
            'subtitle' => __('witout any package/restriction.', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_package_validity',
            'type' => 'text',
            'title' => __('Free package validity', 'adforest-rest-api'),
            'subtitle' => __('In days for new user', 'adforest-rest-api'),
            'required' => array(
                'sb_allow_ads',
                '=',
                array(
                    true
                )
            ),
            'desc' => __('It must be an inter value, -1 means never expired.', 'adforest-rest-api'),
            'default' => - 1,
        ),
        array(
            'id' => 'simple_ad_removal',
            'type' => 'text',
            'title' => __('Simple ad remove after', 'adforest-rest-api'),
            'subtitle' => __('In DAYS', 'adforest-rest-api'),
            'desc' => __('Only integer value without spaces -1 means never expired.', 'adforest-rest-api'),
            'default' => - 1,
        ),


        array(
            'id' => 'after_expired_ads',
            'type' => 'button_set',
            'title' => __('After Removal Ads Should be', 'adforest-rest-api'),
            'options' => array(
                'trashed' => __('Trashed', 'adforest-rest-api'),
                'expired' => __('Expired', 'adforest-rest-api'),
            ),
            'default' => 'trashed'
        ),
        
        array(
            'id' => 'featured_expiry',
            'type' => 'text',
            'title' => __('Feature Ad Expired', 'adforest-rest-api'),
            'subtitle' => __('In DAYS', 'adforest-rest-api'),
            'desc' => __('Only integer value without spaces -1 means never expired.', 'adforest-rest-api'),
            'default' => 7,
        ),
        array(
            'id' => 'sb_upload_limit',
            'type' => 'select',
            'title' => __('Ad image set limit', 'adforest-rest-api'),
            'options' => array(
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9,
                10 => 10,
                11 => 11,
                12 => 12,
                13 => 13,
                14 => 14,
                15 => 15,
                16 => 16,
                17 => 17,
                18 => 18,
                19 => 19,
                20 => 20,
                21 => 21,
                22 => 22,
                23 => 23,
                24 => 24,
                25 => 25
            ),
            'default' => 5,
        ),
        array(
            'id' => 'sb_upload_limit_per',
            'type' => 'text',
            'title' => __('Per Image Upload Limit', 'adforest-rest-api'),
            'subtitle' => __('Must be equals or less than (Ad image set limit)', 'adforest-rest-api'),
            'default' => 5,
        ),
        array(
            'id' => 'sb_upload_size',
            'type' => 'select',
            'title' => __('Ad image max size', 'adforest-rest-api'),
            'options' => array(
                '307200-300kb' => '300kb',
                '614400-600kb' => '600kb',
                '819200-800kb' => '800kb',
                '1048576-1MB' => '1MB',
                '2097152-2MB' => '2MB',
                '3145728-3MB' => '3MB',
                '4194304-4MB' => '4MB',
                '5242880-5MB' => '5MB',
                '6291456-6MB' => '6MB',
                '7340032-7MB' => '7MB',
                '8388608-8MB' => '8MB',
                '9437184-9MB' => '9MB',
                '10485760-10MB' => '10MB',
                '11534336-11MB' => '11MB',
                '12582912-12MB' => '12MB',
                '13631488-13MB' => '13MB',
                '14680064-14MB' => '14MB',
                '15728640-15MB' => '15MB',
                '20971520-20MB' => '20MB',
                '26214400-25MB' => '25MB'
            ),
            'default' => '2097152-2MB',
        ),
        array(
            'id' => 'allow_tax_condition',
            'type' => 'switch',
            'title' => __('Display Condition Taxonomy', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'allow_tax_warranty',
            'type' => 'switch',
            'title' => __('Display Warranty Taxonomy', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'map-seting-section-start',
            'type' => 'section',
            'title' => __('Map Settings', 'adforest-rest-api'),
            'indent' => true
        ),
        array(
            'id' => 'allow_lat_lon',
            'type' => 'switch',
            'title' => __('Latitude & Longitude', 'adforest-rest-api'),
            'desc' => __('This will be display on ad post page for pin point map', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'app_map_style',
            'type' => 'button_set',
            'title' => __('Map Style', 'adforest-rest-api'),
            'options' => array(
                'google_map' => __('Google Map', 'adforest-rest-api'),
                'map_box' => __('Map Box', 'adforest-rest-api'),
            ),
            'default' => 'google_map',
            'required' => array(
                'allow_lat_lon',
                '=',
                true
            ),
        ),
        array(
            'id' => 'sb_default_lat',
            'type' => 'text',
            'title' => __('Latitude', 'adforest-rest-api'),
            'subtitle' => __('for default map.', 'adforest-rest-api'),
            'required' => array(
                'allow_lat_lon',
                '=',
                true
            ),
            'default' => '40.7127837',
        ),
        array(
            'id' => 'sb_default_long',
            'type' => 'text',
            'title' => __('Longitude', 'adforest-rest-api'),
            'subtitle' => __('for default map.', 'adforest-rest-api'),
            'required' => array(
                'allow_lat_lon',
                '=',
                true
            ),
            'default' => '-74.00594130000002',
        ),
        array(
            'id' => 'map-seting-section-end',
            'type' => 'section',
            'indent' => false,
        ),
        array(
            'id' => 'allow_allow_address',
            'type' => 'switch',
            'title' => __('Make adress field required', 'adforest-rest-api'),
            'desc' => __('is Addres field on ad post page required', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'allow_price_type',
            'type' => 'switch',
            'title' => __('Price Type', 'adforest-rest-api'),
            'desc' => __('Display Price type option.', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_price_types',
            'type' => 'select',
            'options' => array(
                'Fixed' => __('Fixed', 'adforest-rest-api'),
                'Negotiable' => __('Negotiable', 'adforest-rest-api'),
                'on_call' => __('Price on call', 'adforest-rest-api'),
                'auction' => __('Auction', 'adforest-rest-api'),
                'free' => __('Free', 'adforest-rest-api'),
                'no_price' => __('No price', 'adforest-rest-api'),
            ),
            'multi' => true,
            'sortable' => true,
            'title' => __('Price Types', 'adforest-rest-api'),
            'default' => array(),
        ),
        array(
            'id' => 'sb_price_types_more',
            'type' => 'text',
            'title' => __('Custom Price Type', 'adforest-rest-api'),
            'desc' => __('Separated by | like option 1|option 2', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'sb_ad_update_notice',
            'type' => 'text',
            'title' => __('Update Ad Notice', 'adforest-rest-api'),
            'default' => 'Hey, be careful you are updating this AD.',
        ),
        array(
            'id' => 'allow_featured_on_ad',
            'type' => 'switch',
            'title' => __('Allow make featured ad', 'adforest-rest-api'),
            'subtitle' => __('on ad post.', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_feature_desc',
            'type' => 'textarea',
            'title' => __('Featured ad description', 'adforest-rest-api'),
            'subtitle' => __('on ad post.', 'adforest-rest-api'),
            'required' => array(
                'allow_featured_on_ad',
                '=',
                true
            ),
            'default' => 'Featured AD has more attention as compare to simple ad.',
        ),
        array(
            'id' => 'bad_words_filter',
            'type' => 'textarea',
            'title' => __('Bad Words Filter', 'adforest-rest-api'),
            'subtitle' => __('comma separated', 'adforest-rest-api'),
            'placeholder' => __('word1,word2', 'adforest-rest-api'),
            'desc' => __('This words will be removed from AD Title and Description', 'adforest-rest-api'),
            'default' => '',
        ),
        array(
            'id' => 'bad_words_replace',
            'type' => 'text',
            'title' => __('Bad Words Replace Word', 'adforest-rest-api'),
            'desc' => __('This words will be replace with above bad words list from AD Title and Description', 'adforest-rest-api'),
            'default' => '',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("Ad View Settings", "adforest-rest-api"),
    'id' => 'api_ad_view_settings',
    'desc' => '',
    'icon' => 'el el-home',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'api_ad_details_info_column',
            'type' => 'button_set',
            'title' => __('Info Columns', 'adforest-rest-api'),
            'subtitle' => __('On ad details page', 'adforest-rest-api'),
            'desc' => __('Select number of info columns', 'adforest-rest-api'),
            'options' => array(
                '1' => __('1 Column', 'adforest-rest-api'),
                '2' => __('2 Columns', 'adforest-rest-api'),
            ),
            'default' => '2'
        ),
        array(
            'id' => 'api_ad_details_style',
            'type' => 'button_set',
            'title' => __('Ad Detail Style', 'adforest-rest-api'),
            'options' => array(
                'style1' => __('Style 1', 'adforest-rest-api'),
                'style2' => __('Style 2', 'adforest-rest-api'),
            ),
            'default' => 'style1'
        ),
        array(
            'id' => 'api_ad_details_info_link_text',
            'type' => 'text',
            'title' => __('Link Text', 'adforest-rest-api'),
            'subtitle' => __('Link text on ad details screen.', 'adforest-rest-api'),
            'default' => __('View Link', 'adforest-rest-api'),
            'desc' => __('Will be used when there is any link input in custom categories templates.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'related_ads_on',
            'type' => 'switch',
            'title' => __('Related Ads', 'adforest-rest-api'),
            'default' => true,
        ),

        array(
            'id' => 'contact_with_seller_email',
            'type' => 'switch',
            'title' => __('Contact with seller', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('By turning this on this will add a form on single ad to send email to the ad owner.', 'adforest-rest-api'),
        ),

        array(
            'id' => 'contact_with_seller_email_title',
            'required' => array( 'contact_with_seller_email', '=', true ),
            'type' => 'text',
            'title' => __('Contact With Seller Title', 'adforest-rest-api'),
            'default' => __('Contact With Seller', 'adforest-rest-api'),
        ),
        array(
            'id' => 'contact_with_seller_email_subtitle',
            'required' => array( 'contact_with_seller_email', '=', true ),
            'type' => 'text',
            'title' => __('Contact With Seller Subtitle', 'adforest-rest-api'),
            'default' => __('Send email to seller', 'adforest-rest-api'),
        ),

        array(
            'id' => 'sb_related_ads_title',
            'required' => array(
                'related_ads_on',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Related Ads Section Title', 'adforest-rest-api'),
            'default' => __('Similiar Ads', 'adforest-rest-api'),
        ),
        array(
            'id' => 'api_ad_details_related_posts',
            'required' => array(
                'related_ads_on',
                '=',
                true
            ),
            'type' => 'slider',
            'title' => __('Related Posts', 'adforest-rest-api'),
            'subtitle' => __('On ad details page', 'adforest-rest-api'),
            'desc' => __('Select Number of related posts', 'adforest-rest-api'),
            'default' => 5,
            'min' => 1,
            'step' => 1,
            'max' => 150,
            'display_value' => 'label'
        ),
        array(
            'id' => 'api_owner_deal_text',
            'type' => 'editor',
            'subtitle' => __('will apply after ad description', 'adforest-rest-api'),
            'title' => __('Ad Owner Text', 'adforest'),
            'default' => '',
            'args' => array(
                'wpautop' => false,
                'media_buttons' => false,
                'textarea_rows' => 5,
                'teeny' => false,
                'quicktags' => false,
            )
        ),
       array(
            'id' => 'sb_ad_whatsapp_chat',
            'type' => 'switch',
            'title' => esc_html__('Allow Whatsapp chat on ad detail page', 'adforest'),
            'default' => false,
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("Ad Search Settings", "adforest-rest-api"),
    'id' => 'api_ad_search_settings',
    'desc' => '',
    'icon' => 'el el-home',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'feature_on_search',
            'type' => 'switch',
            'title' => __('Featured Ads', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'places_search_switch',
            'type' => 'switch',
            'title' => __('Places Switch for search', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_search_ads_title',
            'required' => array(
                'feature_on_search',
                '=',
                true
            ),
            'type' => 'text',
            'title' => __('Featured Ads Section Title', 'adforest-rest-api'),
            'default' => __('Featured Ads', 'adforest-rest-api'),
        ),
        array(
            'id' => 'search_related_posts_count',
            'required' => array(
                'feature_on_search',
                '=',
                true
            ),
            'type' => 'slider',
            'title' => __('Featured Posts', 'adforest-rest-api'),
            'subtitle' => __('On ad details page', 'adforest-rest-api'),
            'desc' => __('Select Number of featured posts', 'adforest-rest-api'),
            'default' => 5,
            'min' => 1,
            'step' => 1,
            'max' => 150,
            'display_value' => 'label'
        ),
        array(
            'id' => 'search_popup_cat_disable',
            'type' => 'switch',
            'title' => __('Disable All Categories', 'adforest'),
            'subtitle' => __('Seach filter Categories', 'adforest'),
            'desc' => __('Enable this option to display only those categories who have atleat 1 ad in categories search popup.', 'adforest'),
            'default' => false,
        ),
        array(
            'id' => 'search_popup_loc_disable',
            'type' => 'switch',
            'title' => __('Disable All Locations', 'adforest'),
            'subtitle' => __('Seach filter Locations', 'adforest'),
            'desc' => __('Enable this option to display only those locations who have atleat 1 ad in locations search popup.', 'adforest'),
            'default' => false,
        ),
        array(
            'id' => 'featured_first',
            'type' => 'switch',
            'title' => __('Fearured First', 'adforest'),
            'subtitle' => __('Dispaly ads in search page.', 'adforest'),
            'desc' => __('Enable this option to display featured ads at the first.', 'adforest'),
            'default' => false,
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __('Ad Rating Settings', 'adforest-rest-api'),
    'id' => 'sb_ad_rating_settings',
    'desc' => '',
    'icon' => 'el el-cogs',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_ad_rating',
            'type' => 'switch',
            'title' => __('Rating on ad', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_update_rating',
            'type' => 'switch',
            'title' => __('Allow update the rating', 'adforest-rest-api'),
            'required' => array(
                'sb_ad_rating',
                '=',
                array(
                    true
                )
            ),
            'default' => false,
        ),
        array(
            'id' => 'sb_ad_rating_title',
            'type' => 'text',
            'title' => __('Rating section title', 'adforest-rest-api'),
            'required' => array(
                'sb_ad_rating',
                '=',
                array(
                    true
                )
            ),
            'default' => 'Rating & Reviews',
        ),
        array(
            'id' => 'sb_rating_email_author',
            'type' => 'switch',
            'title' => __('Email to Author on rating', 'adforest-rest-api'),
            'required' => array( 'sb_ad_rating', '=', array( true ) ),
            'default' => false,
        ),
        array(
            'id' => 'sb_rating_reply_email',
            'type' => 'switch',
            'title' => __('Author reply email to rator', 'adforest-rest-api'),
            'required' => array( 'sb_ad_rating', '=', array( true ) ),
            'default' => false,
        ),
        array(
            'id' => 'sb_rating_max',
            'type' => 'spinner',
            'title' => __('Rating show at most', 'adforest-rest-api'),
            'required' => array(
                'sb_ad_rating',
                '=',
                array(
                    true
                )
            ),
            'default' => '5',
            'min' => '1',
            'step' => '1',
            'max' => '50',
        ),
        array(
            'id' => 'adforest_listing_review_enable_emoji',
            'type' => 'switch',
            'title' => __('Ads Rating Emojies', 'adforest-rest-api'),
            'required' => array('sb_ad_rating', '=', array(true)),
            'default' => false,
        ),
    )
));



Redux::setSection($opt_name, array(
    'title' => __('Messaging Setting', 'adforest-rest-api'),
    'id' => 'sb_ad_messaging_settings',
    'desc' => '',
    'icon' => 'el el-cogs',
    'subsection' => true,
    'fields' => array(
        
       
        array(
            'id' => 'allow_media_upload_messaging',
            'type' => 'switch',
            'title' => __('Media upload on messaging', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_media_upload_messaging_type',
            'required' => array( 'allow_media_upload_messaging', '=', array( '1' )),
            'type' => 'button_set',
            'title' => __('Media type on messaging', 'adforest-rest-api'),
            'options' => array(
             'images' => __('Images', 'adforest-rest-api'),
             'attachments' => __('Attachment', 'adforest-rest-api'),    
             'both'  =>   __('both','adforest-rest-api')
              ),
            'default' => 'both',
        ),
       
        
        
         array(
            'required' => array( 'allow_media_upload_messaging', '=', array( '1' ) ),
            'required' => array( 'sb_media_upload_messaging_type', '=', array( 'images','both' ) ),
            'id' => 'sb_media_image_size',
            'type' => 'select',
            'title' => esc_html__('Images size', 'adforest-rest-api'),
            'options' => array('307200-300kb' => '300kb', '614400-600kb' => '600kb', '819200-800kb' => '800kb', '1048576-1MB' => '1MB', '2097152-2MB' => '2MB', '3145728-3MB' => '3MB', '4194304-4MB' => '4MB', 
                '5242880-5MB' => '5MB' ,'6291456-6Mb'=>'6Mb','7340032-7Mb'=>'7Mb','8388608-8Mb'=>'8Mb',
                '9437184-9Mb'=>'9Mb','10485760-10Mb'=>'10Mb','10485760-10Mb'=>'10Mb','10485760-10Mb'=>'10Mb',
                '12582912 -12Mb'=>'12Mb' ,'12582912-12Mb'=>'12Mb' ,'15728640-15Mb'=>'15Mb' ,'20971520-20Mb'=>'20Mb' ),
            'default' => '819200-800kb',
        ),     
        
         array(
            'required' => array( 'allow_media_upload_messaging', '=', array( '1' ) ),
            'required' => array( 'sb_media_upload_messaging_type', '=', array( 'attachments','both' ) ),
            'id' => 'sb_media_attachment_size',
            'type' => 'select',
            'title' => esc_html__('Attachment size', 'adforest-rest-api'),
             'options' => array('307200-300kb' => '300kb', '614400-600kb' => '600kb', '819200-800kb' => '800kb', '1048576-1MB' => '1MB', '2097152-2MB' => '2MB', '3145728-3MB' => '3MB', '4194304-4MB' => '4MB', 
                '5242880-5MB' => '5MB' ,'6291456-6Mb'=>'6Mb','7340032-7Mb'=>'7Mb','8388608-8Mb'=>'8Mb',
                '9437184-9Mb'=>'9Mb','10485760-10Mb'=>'10Mb','10485760-10Mb'=>'10Mb','10485760-10Mb'=>'10Mb',
                '12582912 -12Mb'=>'12Mb' ,'12582912-12Mb'=>'12Mb' ,'15728640-15Mb'=>'15Mb' ,'20971520-20Mb'=>'20Mb' ),
             
            'default' => '1048576-1MB',
        ),       
        
        $fields = array(
         'required' => array( 'sb_media_upload_messaging_type', '=', array( 'attachments','both' ) ),
         'id' => 'sb_message_attach_formats',
         'type' => 'button_set',
         'title' => __('Select attachments formats', 'adforest-rest-api'),
         'desc' => __('You can select multiple file formats', 'adforest-rest-api'),
         'multi' => true,
    //Must provide key => value pairs for options
         'options' => array(
            'pdf' => __('Pdf', 'adforest-rest-api'),
            'doc' => __('Doc', 'adforest-rest-api'),
            'docx' => __('Docx', 'adforest-rest-api'),
            'txt' => __('Txt', 'adforest-rest-api'),
            'zip' => __('zip','adforest-rest-api')
            ),
    'default' => array('pdf', 'doc'),
        ),
        )
         )
        );




/* Only show if woocommerce plugin activated */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    $cat_pack_type = array();

    $adforest_theme = wp_get_theme();
    if ($adforest_theme->get('Name') != 'adforest' && $adforest_theme->get('Name') != 'adforest child') {

        $cat_pack_type = array(
            'id' => 'cat_pkg_type',
            'type' => 'button_set',
            'title' => __('Category Package Type', 'adforest'),
            'desc' => __('<b>Parent : </b> In Parent Selection, if you buy a parent category in a package then you can also post in child categories of the paid parent category.<br /><br /><b>Child : </b>In Child Selection you have to buy each paid category whether it is a child or parent.', 'adforest'),
            'options' => array(
                'parent' => __('Parent Category', 'adforest'),
                'child' => __('Child Category', 'adforest'),
            ),
            'default' => 'parent',
        );
    } else {
        $cat_pack_type = array(
            'id' => 'info_warning',
            'type' => 'info',
            'title' => __('Category Package Type !', 'adforest-rest-api'),
            'style' => 'warning',
            'desc' => __('To Change Category Package Type you have to go to <b>Dashboard >> Appearence >> Theme option >> Category Package Type</b>', 'adforest-rest-api')
        );
    }


    Redux::setSection($opt_name, array(
        'title' => __("Woo Products", "adforest-rest-api"),
        'id' => 'api_woo_products_settings',
        'desc' => '',
        'icon' => 'el el-list-alt',
        'fields' => array(
            array(
                'id' => 'api_woo_products_multi',
                'type' => 'select',
                'data' => 'post',
                'args' => array(
                    'post_type' => array(
                        'product'
                    ),
                    'posts_per_page' => - 1
                ),
                'multi' => true,
                'sortable' => true,
                'title' => __('Select Products', 'adforest-rest-api'),
            ),
            array(
                'id' => 'opt-info-select',
                'type' => 'info',
                'desc' => __('Select Payment Packages', 'adforest-rest-api'),
            ),
            $cat_pack_type,
            array(
                'required' => array(
                    'api-is-buy-android-app',
                    '=',
                    true
                ),
                'id' => 'api-payment-packages',
                'type' => 'select',
                'multi' => true,
                'sortable' => true,
                'title' => __('Payment Methods For Android App', 'adforest-rest-api'),
                'desc' => __('Select the payment methods you want to add.', 'adforest-rest-api'),
                'options' => adforestAPI_payment_types(),
                'default' => array(
                    'stripe'
                )
            ),
            array(
                'required' => array(
                    'api-is-buy-ios-app',
                    '=',
                    true
                ),
                'id' => 'api-payment-packages-ios',
                'type' => 'select',
                'multi' => true,
                'sortable' => true,
                'title' => __('Payment Methods IOS App', 'adforest-rest-api'),
                'desc' => __('Note ios only uses InApp Purchase', 'adforest-rest-api'),
                'options' => adforestAPI_payment_types('', 'ios'),
                'default' => array(
                    'app_inapp'
                )
            ),
            array(
                'id' => 'package_expiry_notification',
                'type' => 'switch',
                'title' => __('Package Expiry Notification', 'adforest'),
                'desc' => __('<b> Note : </b> This functionality works hiddenly notify the users before package expiry.This option takes a lot of load so any one who wishes to choose this option must have a good server that can support haevy load.', 'adforest'),
                'default' => false,
            ),
            array(
                'id' => 'package_expire_notify_before',
                'type' => 'text',
                'title' => __('Package Expiry Notification before', 'adforest'),
                'subtitle' => __('add the number of days before package expiry notification', 'adforest'),
                'default' => 3,
                'desc' => __('should be integer value. <b>( Days )</b>', 'adforest'),
                'required' => array('package_expiry_notification', '=', array(true)),
            ),
        )
    ));
}

/* Shop Settings Starts From Here */
Redux::setSection($opt_name, array(
    'title' => __('Shop Settings', 'adforest-rest-api'),
    'id' => 'shop_settings',
    'desc' => '',
    'icon' => 'el el-shopping-cart',
    'fields' => array(
        array(
            'id' => 'shop-turn-on-info00',
            'type' => 'info',
            'style' => 'info',
            'title' => __('Info', 'adforest-rest-api'),
            'desc' => __('Works best with (AdForest WordPress Theme) As we areusing webview for the shop.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'shop-turn-on-info1',
            'type' => 'info',
            'style' => 'info',
            'required' => array('shop-turn-on', '=', '0'),
            'title' => __('Info', 'adforest-rest-api'),
            'desc' => __('If you want to turn on shop you need to first update the package in the packages.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'shop-turn-on',
            'type' => 'switch',
            'title' => __('Turn On Shop.', 'adforest-rest-api'),
            'subtitle' => __('Add shop in Theme', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('If you want to turn on shop you need to first update the package in the woo-commerce.', 'adforest-rest-api'),
        ),
        array(
            'required' => array('shop-turn-on', '=', '1'),
            'id' => 'shop-show-menu',
            'type' => 'switch',
            'title' => __('Shop Menu', 'adforest-rest-api'),
            'subtitle' => __('Show Shop In Menu', 'adforest-rest-api'),
            'default' => false,
        ),
    /*
      array(

      'id'            => 'shop-number-of-products',
      'type'          => 'slider',
      'title'         => __( 'No.of Products', 'adforest-rest-api' ),
      'subtitle'      => __( 'No.of Products Per Page', 'adforest-rest-api' ),
      'desc'          => __( 'the number of products you wanna show per page.', 'adforest-rest-api' ),
      'default'       => 12,
      'min'           => 1,
      'step'          => 1,
      'max'           => 500,
      'display_value' => 'text',
      'required' => array( 'shop-turn-on', '=', '1' ),
      ),

      array(
      'id'       => 'shop-number-page-title',
      'type'     => 'text',
      'title'    => __( 'Shop Category Page Title', 'adforest-rest-api' ),
      'subtitle' => '',
      'desc'     => '',
      'default'  => __( 'Shop', 'adforest-rest-api' ),

      ),


      array(
      'id'    => 'shop-turn-on-info2',
      'type'  => 'info',
      'style' => 'info',
      'required' => array( 'shop-turn-on', '=', true ),
      'title' => __( 'Single Page Settings', 'adforest-rest-api' ),
      'desc'  => __( 'Single page settings starts from below.', 'adforest-rest-api' ),
      ),


      array(
      'required' => array( 'shop-turn-on', '=', '1' ),
      'id'       => 'shop-related-single-on',
      'type'     => 'switch',
      'title'    => __( 'Turn On Related Product', 'adforest-rest-api' ),
      'subtitle'      => __( 'On Single Page', 'adforest-rest-api' ),
      'default'  => false,
      'desc'       => __( 'Turn on related products on single page.', 'adforest-rest-api' ),
      ),

      array(
      'required' => array( 'shop-related-single-on', '=', true ),
      'id'       => 'shop-related-single-title',
      'type'     => 'text',
      'title'    => __( 'Related Products Title', 'adforest-rest-api' ),
      'subtitle' => '',
      'desc'     => '',
      'default'  => __( 'Related Products', 'adforest-rest-api' ),


      ),

      array(
      'id'       => 'sb_multi_currency_default',
      'type'     => 'select',
      'data'     => 'terms',
      'args' => array( 'taxonomies'=>'product_cat', 'hide_empty' => false,  ),
      'title'    => __( 'Default selected currency', 'adforest-rest-api' ),
      'subtitle'    => __( 'While posting ad in multi-currency', 'adforest-rest-api' ),
      'default'  => '',
      ),

      array(

      'id'            => 'shop-number-of-related-products-single',
      'type'          => 'slider',
      'title'         => __( 'No.of Related Products', 'adforest-rest-api' ),
      'subtitle'      => __( 'No.of Related Products Per Page', 'adforest-rest-api' ),
      'desc'          => __( 'the number of products you wanna show on single page.', 'adforest-rest-api' ),
      'default'       => 12,
      'min'           => 0,
      'step'          => 1,
      'max'           => 500,
      'display_value' => 'text',
      'required' => array( 'shop-related-single-on', '=', true ),
      ), */

    /* Shop Ends */
    )
));


Redux::setSection($opt_name, array(
    'title' => __('Users', "adforest-rest-api"),
    'id' => 'api_users_screen',
    'desc' => '',
    'icon' => 'el el-user',
    'fields' => array(
        array(
            'id' => 'sb_phone_verification',
            'type' => 'switch',
            'title' => __('Phone verfication', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('If phone verification is on then system put verified batch to ad details on number so other can see this number is verified.', 'adforest-rest-api'),
        ),
      
        array(
            'id' => 'sb_resend_code',
            'type' => 'text',
            'title' => __('Resend security code', 'adforest-rest-api'),
            'subtitle' => __('In seconds', 'adforest-rest-api'),
            'desc' => __('Only integer value without spaces, 30 means 30-seconds', 'adforest-rest-api'),
            'required' => array(
                'sb_phone_verification',
                '=',
                array(
                    '1'
                )
            ),
            'default' => 30,
        ),
        array(
            'id' => 'sb_change_ph',
            'type' => 'switch',
            'title' => __('Change phone number while ad posting.', 'adforest-rest-api'),
            'desc' => __('If off then only user profile number will be display and can not be changeable.', 'adforest-rest-api'),
            'default' => true,
        ),
        array(
            'id' => 'sb_new_user_email_to_admin',
            'type' => 'switch',
            'title' => __('New User Email to Admin', 'adforest-rest-api'),
            'default' => true
        ),
        array(
            'id' => 'sb_new_user_email_to_user',
            'type' => 'switch',
            'title' => __('Welcome Email to User', 'adforest-rest-api'),
            'default' => true
        ),
        array(
            'id' => 'sb_new_user_email_verification',
            'type' => 'switch',
            'title' => __('New user email verification', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('If verfication on then please update your new user email template by verification link.', 'adforest-rest-api'),
        ),

        array(
            'id' => 'admin_contact_page',
            'type' => 'select',
            'data' => 'pages',
            'multi' => false,
            'title' => __('Contact to Admin', 'adforest-rest-api'),
            'required' => array('sb_new_user_email_verification', '=', array('1')),
            'desc' => __('Select the page if verification email is not sent to new user.', 'adforest-rest-api'),
        ),
        
        array(
            'id' => 'sb_new_user_register_policy',
            'type' => 'select',
            'data' => 'pages',
            'multi' => false,
            'sortable' => false,
            'title' => __('Select Page', 'adforest-rest-api'),
            'subtitle' => __('Terms and Conditions', 'adforest-rest-api'),
            'desc' => __('Specially for General Data Protection Regulation (GDPR)', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_new_user_register_checkbox_text',
            'type' => 'text',
            'title' => __('Term and Condition Text', 'adforest-rest-api'),
            'default' => '',
            'desc' => __('Terms and Condition text next to checkbox. Leave empty if you want to show default text', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_new_user_delete_option',
            'type' => 'switch',
            'title' => __('Show Delete button', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Show delete button on user profile. Due to General Data Protection Regulation (GDPR) policy. Note: This will delete the entire data from the database and can not be recover again.', 'adforest-rest-api'),
        ),
        array(
            'required' => array(
                'sb_new_user_delete_option',
                '=',
                true
            ),
            'id' => 'sb_new_user_delete_option_text',
            'type' => 'text',
            'title' => __('Delete Popup Text', 'adforest-rest-api'),
            'default' => 'Are you sure you want to delete the account.',
            'desc' => __('Popup text after delete link clicked.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_user_allow_block',
            'type' => 'switch',
            'title' => __('Block User', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Allow users to block anyone and stop seeing his ads.', 'adforest-rest-api'),
        ),
        array(
            'required' => array('sb_phone_verification', '=', array('1')),
            'id' => 'sb_select_sms_gateway',
            'type' => 'button_set',
            'title' => __('SMS Gateway', 'adforest-rest-api'),
            'options' => array(
                'twilio' => __('Twilio', 'adforest-rest-api'),
                'iletimerkezi' => __('Iletimerkezi SMS', 'adforest-rest-api'),
                'firebase'=>__('Firebase','adforest-rest-api'),
            ),
            'default' => 'twilio'
        ),
        array(
            'required' => array('sb_phone_verification', '=', array('1')),
            'id' => 'sb_new_user_sms_verified_can',
            'type' => 'switch',
            'title' => __('Verify Users', 'adforest-rest-api'),
            'default' => false,
            'desc' => __('Only profile sms verified users can send message to other users.', 'adforest-rest-api'),
        ),

       array(
            'id' => 'sb_register_user_txt',
            'type' => 'text',
            'title' => __('User name while Registering with otp', 'adforest'),
            'default' => __('User', 'adforest'),          
            'desc'=>  __('This username will be added as user name when user will be registered with otp','adforest'),
        ),
        
        array(
            'id' => 'subscribe_on_user_register',
            'type' => 'switch',
            'title' => __('Subscribe Users On Registration', 'adforest-rest-api'),
            'subtitle' => __('MailChimp List ID', 'adforest-rest-api'),
            'default' => false
        ),
        array(
            'id' => 'subscribe_on_user_register_listid',
            'type' => 'text',
            'title' => __('MailChimp List ID', 'adforest-rest-api'),
            'required' => array('subscribe_on_user_register', '=', true),
            'default' => '',
            'desc' => __('How to Find it', 'adforest-rest-api') . " => "  . "http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id",
        ),

        array(
            'required' => array('subscribe_on_user_register', '=', true),
            'id' => 'subscriber_checkbox_on_register',
            'type' => 'switch',
            'title' => __('Show Confirmation Checkbox', 'adforest-rest-api'),
            'desc' => __('show confirmation checkbox on registraction form', 'adforest-rest-api'),
            'default' => false
        ),
        array(
            'id' => 'subscriber_checkbox_on_register_text',
            'type' => 'text',
            'title' => __('Confimation checkbox text', 'adforest-rest-api'),
            'required' => array('subscriber_checkbox_on_register', '=', true),
            'default' => __('subscribe for latest news and updates', 'adforest-rest-api'),
        ),   


    )
));

/* ------------------Comment/Bidding Settings ----------------------- */
Redux::setSection($opt_name, array(
    'title' => __('Bidding Settings', 'adforest-rest-api'),
    'id' => 'sb_comments_settings',
    'desc' => '',
    'icon' => 'el el-cogs',
    'fields' => array(
        array(
            'id' => 'sb_enable_comments_offer',
            'type' => 'switch',
            'title' => __('Enable Bidding', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_enable_comments_offer_user',
            'type' => 'switch',
            'title' => __('Give bidding option to user', 'adforest-rest-api'),
            'required' => array(
                'sb_enable_comments_offer',
                '=',
                '1'
            ),
            'default' => false,
        ),
        array(
            'id' => 'bidding_timer',
            'type' => 'switch',
            'title' => __('Bidding Timer', 'adforest-rest-api'),
            'required' => array(
                array('sb_enable_comments_offer', '=', '1'),
                array('sb_enable_comments_offer_user', '=', '1'),
            ),
            'default' => false,
        ),
        array(
            'id' => 'sb_make_bid_categorised',
            'type' => 'switch',
            'title' => __('Make Bidding Categorised', 'adforest'),
            'subtitle' => __('Enable this to make bidding categorised', 'adforest'),
            'default' => true,
            'required' =>
            array(
                array('sb_enable_comments_offer', '=', '1'),
                array('sb_enable_comments_offer_user', '=', '1'),
            ),
        ),
        array(
            'id' => 'bid_categorised_type',
            'type' => 'button_set',
            'title' => __('Bidding Category Type', 'adforest'),
            'desc' => __('For selective case you have to enable checkbox in category term meta. <b> ( dashboard >> Classified Ads >> Categories ) </b>', 'adforest'),
            'options' => array(
                'all' => __('All', 'adforest'),
                'selective' => __('Selective', 'adforest'),
            ),
            'required' => array('sb_make_bid_categorised', '=', '1'),
            'default' => 'all'
        ),
        array(
            'id' => 'top_bidder_limit',
            'type' => 'select',
            'title' => __('Top bidder limit', 'adforest-rest-api'),
            'required' => array(
                'sb_enable_comments_offer',
                '=',
                '1'
            ),
            'options' => range(0, 10),
            'default' => 3,
            'desc' => __('If you select 0 it will hide the tab.', 'adforest-rest-api'),
        ),
        array(
            'id' => 'sb_enable_comments_offer_user_title',
            'type' => 'text',
            'title' => __('User Section Title', 'adforest-rest-api'),
            'required' => array(
                'sb_enable_comments_offer_user',
                '=',
                '1'
            ),
            'default' => "Bidding",
        ),
        array(
            'id' => 'sb_email_on_new_bid_on',
            'type' => 'switch',
            'title' => __('Email to Ad author', 'adforest-rest-api'),
            'subtitle' => __('on bid', 'adforest-rest-api'),
            'required' => array(
                'sb_enable_comments_offer',
                '=',
                '1'
            ),
            'default' => false,
        ),
        array(
            'id' => 'sb_email_to_bid_winner',
            'type' => 'switch',
            'title' => __('Email to Bid winner', 'adforest'),
            'subtitle' => __('after closing bids', 'adforest'),
            'default' => false,
            'required' => array(
                'sb_enable_comments_offer',
                '=',
                '1'
            ),
        ),
        array(
            'id' => 'sb_comments_section_title',
            'type' => 'text',
            'title' => __('Section Title', 'adforest-rest-api'),
            'required' => array(
                'sb_enable_comments_offer',
                '=',
                '1'
            ),
            'default' => "Bids",
        ),
        array(
            'id' => 'sb_comments_section_note',
            'type' => 'text',
            'title' => __('Disclaimer note', 'adforest-rest-api'),
            'required' => array(
                'sb_enable_comments_offer',
                '=',
                '1'
            ),
            'default' => "*Your phone number will be show to post author",
        ),
    )
));

/* ------------------Email Templates Settings ----------------------- */
Redux::setSection($opt_name, array(
    'title' => __('Email Templates', 'adforest-rest-api'),
    'id' => 'sb_email_templates',
    'desc' => '',
    'icon' => 'el el-pencil',
    'fields' => array()
));

Redux::setSection($opt_name, array(
    'title' => __("New Ad Email", "adforest-rest-api"),
    'id' => 'sb_email_templates1',
    'desc' => __("Send email to admin when ther is any new ad on the website", "adforest-rest-api"),
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_msg_subject_on_new_ad',
            'type' => 'text',
            'title' => __('New Ad email subject', 'adforest-rest-api'),
            'desc' => __('%site_name% , %ad_owner% , %ad_title% will be translated accordingly.', 'adforest-rest-api'),
            'default' => 'You have new Ad - Adforest',
        ),
        array(
            'id' => 'sb_msg_from_on_new_ad',
            'type' => 'text',
            'title' => __('New Ad FROM', 'adforest-rest-api'),
            'desc' => __('FROM: NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_msg_on_new_ad',
            'type' => 'editor',
            'title' => __('New Ad Posted Message', 'adforest-rest-api'),
            'desc' => __('%site_name% , %ad_owner% , %ad_title% , %ad_link% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"><br/>A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>Admin,</b></span></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">You\'ve new AD;</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Title: %ad_title%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Link: <a href="%ad_link%">%ad_title%</a></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Poster: %ad_owner%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title' => __("New Message On Ad", "adforest-rest-api"),
    'id' => 'sb_email_templates2',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_message_subject_on_new_ad',
            'type' => 'text',
            'title' => __('New Message email subject', 'adforest-rest-api'),
            'desc' => __('%site_name% , %ad_title% will be translated accordingly.', 'adforest-rest-api'),
            'default' => 'You have new message - Adforest',
        ),
        array(
            'id' => 'sb_message_from_on_new_ad',
            'type' => 'text',
            'title' => __('New Message FROM', 'adforest-rest-api'),
            'desc' => __('FROM: NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_message_on_new_ad',
            'type' => 'editor',
            'title' => __('New Message template', 'adforest-rest-api'),
            'desc' => __('%site_name% , %message% , %sender_name%, %ad_title% , %ad_link% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"><br/>A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>Admin,</b></span></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">You\'ve new Message;</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Title: %ad_title%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Link: <a href="%ad_link%">%ad_title%</a></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Sender: %sender_name%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Message: %message%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("Ad Report Email", "adforest-rest-api"),
    'id' => 'sb_email_templates3',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_report_ad_subject',
            'type' => 'text',
            'title' => __('Ad report email subject', 'adforest-rest-api'),
            'desc' => __('%site_name% , %ad_title% will be translated accordingly.', 'adforest-rest-api'),
            'default' => 'Ad Reported - Adforest',
        ),
        array(
            'id' => 'sb_report_ad_from',
            'type' => 'text',
            'title' => __('Ad report email FROM', 'adforest-rest-api'),
            'desc' => __('FROM: NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_report_ad_message',
            'type' => 'editor',
            'title' => __('Ad Report template', 'adforest-rest-api'),
            'desc' => __('%site_name% , %ad_owner% , %ad_title% , %ad_link%, %ad_report_option% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>Admin,</b></span></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Below Ad is reported.</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Title: %ad_title%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Link: <a href="%ad_link%">%ad_title%</a></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Ad Poster: %ad_owner%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("Reset Password Email", "adforest-rest-api"),
    'id' => 'sb_email_templates4',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_forgot_password_subject',
            'type' => 'text',
            'title' => __('Reset Password email subject', 'adforest-rest-api'),
            'desc' => __('%site_name% will be translated accordingly.', 'adforest-rest-api'),
            'default' => 'Reset Password - Adforest',
        ),
        array(
            'id' => 'sb_forgot_password_from',
            'type' => 'text',
            'title' => __('Reset Password email FROM', 'adforest-rest-api'),
            'desc' => __('FROM: NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_forgot_password_message',
            'type' => 'editor',
            'title' => __('Reset Password template', 'adforest-rest-api'),
            'desc' => __('%site_name% , %user% , %reset_link% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %user%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>Please use this below link to reset your password.<br/>%reset_link%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("New Rating Email", "adforest-rest-api"),
    'id' => 'sb_email_templates5',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_new_rating_subject',
            'type' => 'text',
            'title' => __('Rating email subject', 'adforest-rest-api'),
            'desc' => __('%site_name% will be translated accordingly.', 'adforest-rest-api'),
            'default' => 'New Rating - Adforest',
        ),
        array(
            'id' => 'sb_new_rating_from',
            'type' => 'text',
            'title' => __('New rating email FROM', 'adforest-rest-api'),
            'desc' => __('FROM: NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_new_rating_message',
            'type' => 'editor',
            'title' => __('New rating template', 'adforest-rest-api'),
            'desc' => __('%site_name% , %receiver% , %rator% , %rating% , %comments% , %rating_link% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %receiver%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>You got new rating;User who rated: %rator%Stars: %rating%Link: %rating_link%Comments: %comments%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("New Bid Email", "adforest-rest-api"),
    'id' => 'sb_email_templates6',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_new_bid_subject',
            'type' => 'text',
            'title' => __('Bid email subject', 'adforest-rest-api'),
            'desc' => __('%site_name% will be translated accordingly.', 'adforest-rest-api'),
            'default' => 'New Bid - Adforest',
        ),
        array(
            'id' => 'sb_new_bid_from',
            'type' => 'text',
            'title' => __('Bid email FROM', 'adforest-rest-api'),
            'desc' => __('FROM: NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_new_bid_message',
            'type' => 'editor',
            'title' => __('Bid email template', 'adforest-rest-api'),
            'desc' => __('%site_name% , %receiver% , %bidder% , %bid% , %comments% , %bid_link% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %receiver%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>You got new Bid;Bidder: %bidder%Bid: %bid%Link: %bid_link%Comments: %comments%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("New User Registration Email", "adforest-rest-api"),
    'id' => 'sb_email_templates7',
    'desc' => __("Send email to the admin when someone registred on the site", "adforest-rest-api"),
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_new_user_admin_message_subject',
            'type' => 'text',
            'title' => __('New user email template subject for Admin', 'adforest-rest-api'),
            'default' => 'New User Registration',
        ),
        array(
            'id' => 'sb_new_user_admin_message_from',
            'type' => 'text',
            'title' => __('New user email FROM for Admin', 'adforest-rest-api'),
            'desc' => __('NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_new_user_admin_message',
            'type' => 'editor',
            'title' => __('New user email template for Admin', 'adforest-rest-api'),
            'desc' => __('%site_name% , %display_name%, %email% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello Admin</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>New user has registered on your site %site_name%;Name: %display_name%Email: %email%&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("User Welcome / Confirmation Email", "adforest-rest-api"),
    'id' => 'sb_email_templates8',
    'desc' => __("Send welcome or account confirmation email to the user when someone registred", "adforest-rest-api"),
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_new_user_message_subject',
            'type' => 'text',
            'title' => __('New user email template subject', 'adforest-rest-api'),
            'default' => 'New User Registration',
        ),
        array(
            'id' => 'sb_new_user_message_from',
            'type' => 'text',
            'title' => __('New user email FROM', 'adforest-rest-api'),
            'desc' => __('NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_new_user_message',
            'type' => 'editor',
            'title' => __('New user email template', 'adforest-rest-api'),
            'desc' => __('%site_name% , %user_name% %display_name% %verification_link% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %display_name%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>Welcome to %site_name%.<br/>Your details are below;<br/>Username: %user_name%<br/>&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("Ad Activation Email", "adforest-rest-api"),
    'id' => 'sb_email_templates9',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_active_ad_email_subject',
            'type' => 'text',
            'title' => __('Ad activation subject', 'adforest-rest-api'),
            'default' => 'You Ad has been activated.',
        ),
        array(
            'id' => 'sb_active_ad_email_from',
            'type' => 'text',
            'title' => __('Ad activation FROM', 'adforest-rest-api'),
            'desc' => __('NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_active_ad_email_message',
            'type' => 'editor',
            'title' => __('Ad activation message', 'adforest-rest-api'),
            'desc' => __('%site_name% , %user_name%, %ad_title% ,  %ad_link% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %user_name%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p><br/>You ad has been activated.<br/>Details are below;<br/>Ad Title: %ad_title%<br/>Ad Link: %ad_link%<br/>&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("New Rating Received Email", "adforest-rest-api"),
    'id' => 'sb_email_templates10',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'ad_rating_email_subject',
            'type' => 'text',
            'title' => __('Rating email subject', 'adforest-rest-api'),
            'default' => 'You have a new rating',
        ),
        array(
            'id' => 'ad_rating_email_from',
            'type' => 'text',
            'title' => __('Rating FROM', 'adforest-rest-api'),
            'desc' => __('NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'ad_rating_email_message',
            'type' => 'editor',
            'title' => __('Rating message', 'adforest-rest-api'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false,
            ),
            'desc' => '%site_name%, %ad_title%, %ad_link%, %rating, %rating_comments%, %author_name%' . __('will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody> <tr> <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td> <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"> <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"> <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"> <tbody> <tr> <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"> <table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"> <tbody> <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"> <td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"> A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %author_name%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p> <br />You have new rating, details are below; <br /> Rating: %rating% <br />Comments: %rating_comments% <br /> Ad Title: %ad_title% <br /> Ad Link: %ad_link% <br />&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp; </div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __("Rating Reply Email", "adforest-rest-api"),
    'id' => 'sb_email_templates11',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'ad_rating_reply_email_subject',
            'type' => 'text',
            'title' => __('Rating reply email subject', 'adforest-rest-api'),
            'default' => 'You got a reply on your rating',
        ),
        array(
            'id' => 'ad_rating_reply_email_from',
            'type' => 'text',
            'title' => __('Rating reply FROM', 'adforest-rest-api'),
            'desc' => __('NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'ad_rating_reply_email_message',
            'type' => 'editor',
            'title' => __('Rating reply message', 'adforest-rest-api'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false,
            ),
            'desc' => '%site_name%, %ad_title%, %ad_link%, %rating%, %rating_comments%, %author_name%, %author_reply% ' . __('will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"> <tbody> <tr> <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td> <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"> <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"> <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"> <tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"> <tbody> <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"> <td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"> A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"> <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello,</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p> <br /> You have reply on your rating, details are below; <br /> Ad Title: %ad_title% <br />Ad Link: %ad_link% <br /> Ad Author: %author_name% <br />Author reply: %author_reply% <br />Your given rating: %rating% <br />Your comments: %rating_comments% <br />&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',
        ),
    )
));


Redux::setSection($opt_name, array(
    'title' => __('Contact Sellet', 'adforest-rest-api'),
    'id' => 'sb_email_template_seller_widget',
    'desc' => __('With and without login', 'adforest-rest-api'),
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'sb_email_template_seller_widget_subject',
            'type' => 'text',
            'title' => __('Seller Email Subject', 'adforest-rest-api'),
            'desc' => __('%site_name% , %ad_owner% , %ad_title% will be translated accordingly.', 'adforest-rest-api'),
            'default' => '%ad_owner% You Received A New Message - %site_name%',
        ),
        array(
            'id' => 'sb_email_template_seller_widget_from',
            'type' => 'text',
            'title' => __('New Ad FROM', 'adforest-rest-api'),
            'desc' => __('FROM: NAME valid@email.com is compulsory as we gave in default.', 'adforest-rest-api'),
            'default' => 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        ),
        array(
            'id' => 'sb_email_template_seller_widget_desc',
            'type' => 'editor',
            'title' => __('New Ad Posted Message', 'adforest-rest-api'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false,
            ),
            'desc' => "%receiver_name%, %sender_name%, %sender_email%, %sender_phone%, %sender_message%, %ad_title%, %ad_link%, %ad_owner%". " ".__('Will be translated accordingly.', 'adforest-rest-api'),
            'default' => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"> </td>
<td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;">
<div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;">
<table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;">
<tbody>
<tr>
<td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
<table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"><br />A Designing and development company</td>
</tr>
<tr>
<td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello </span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>%receiver_name%,</b></span></p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">You\'ve received a new message on your ad.;</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Title: %ad_title%</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Link: <a href="%ad_link%">%ad_title%</a></p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Sender Name: %sender_name%,</p>
<p>Sender Email: %sender_email%</p>
<p>Sender Phone Number: %sender_phone%</p>
<p>Sender Message:</p>
<p>%sender_message%</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;">
<table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td>
</tr>
</tbody>
</table>
</div>
</div>
</td>
<td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"> </td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>',
        ),
    )
));


do_action('adforest_commom_email_templates', $opt_name);

$load_languages = array();
$load_languages = apply_filters('AdftiorestAPI_load_active_languages', $load_languages);
$desc = __('Please select your desire languages used in app', 'adforest-rest-api');
if (empty($load_languages)) {
    $desc = __('Please configure wpml and translate language to use this functionality', 'adforest-rest-api');
}
Redux::setSection($opt_name, array(
    'title' => __('WPML Settings', "adforest-rest-api"),
    'id' => 'api_wpml_settings',
    'desc' => '',
    'icon' => 'el el-cogs',
    'fields' => array(
        array(
            'id' => 'sb_api_wpml_anable',
            'type' => 'switch',
            'title' => __('Enable WPML', 'adforest-rest-api'),
            'default' => false,
        ),
        array(
            'id' => 'sb_load_languages',
            'type' => 'select',
            'multi' => true,
            'options' => $load_languages,
            'title' => __('Languages', 'adforest-rest-api'),
            'default' => 'en',
            'desc' => $desc,
        ),
        array(
            'id' => 'sb_duplicate_post_app',
            'type' => 'switch',
            'title' => __('Duplicate Posts ( While Ad Posting )', 'adforest'),
            'default' => false,
            'subtitle' => __('Enable this option to duplicate posts in all others languages after successfully publish.', 'adforest'),
            'desc' => __('<b>Note : </b> Disable means the posts publish only in the current language.', 'adforest'),
        ),
        array(
            'id' => 'sb_show_posts_app',
            'type' => 'switch',
            'title' => __('Display Posts', 'adforest'),
            'default' => false,
            'subtitle' => __('Enable this option to display all others languages posts in current language.', 'adforest'),
            'desc' => __('<b>Note : </b> Disable means to display only current language posts.', 'adforest'),
        ),
        array(
            'id' => 'app_wpml_logo',
            'type' => 'media',
            'url' => true,
            'title' => __('WPML Logo', 'adforest-rest-api'),
            'compiler' => 'true',
            'desc' => __('Site Logo image for the site.', 'adforest-rest-api'),
            'subtitle' => __('Dimensions: 230 x 40', 'adforest-rest-api'),
            'default' => array(
                'url' => ADFOREST_API_PLUGIN_URL . "images/logo.png"
            ),
        ),
        array(
            'id' => 'wpml_header_title1',
            'type' => 'text',
            'title' => __('Wpml header Title 1', 'adforest-rest-api'),
            'default' => 'Pick your',
        ),
        array(
            'id' => 'wpml_header_title2',
            'type' => 'text',
            'title' => __('Wpml header Title 2', 'adforest-rest-api'),
            'default' => 'Language',
        ),
        array(
            'id' => 'wpml_menu_text',
            'type' => 'text',
            'title' => __('Wpml Menu Text', 'adforest-rest-api'),
            'default' => 'Languages',
        ),
    )
));


$home_layout_value = 'dafault'; // value in json can be horizental,vertical
$home_layout_default = TRUE;  // default value
$home_layout_horizental = FALSE;
$home_layout_vertical = FALSE;


if ($home_layout_value == 'horizental') {

    $home_layout_horizental = TRUE;
    $home_layout_vertical = FALSE;
    $home_layout_default = FALSE;
}

if ($home_layout_value == 'vertical') {

    $home_layout_vertical = TRUE;
    $home_layout_horizental = FALSE;
    $home_layout_default = FALSE;
}