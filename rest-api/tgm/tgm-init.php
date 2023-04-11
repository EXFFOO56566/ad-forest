<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme Tameer for publication on ThemeForest
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */
require_once ADFOREST_API_PLUGIN_FRAMEWORK_PATH . '/tgm/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'sbAPI_themes_register_required_plugins' );
function sbAPI_themes_register_required_plugins() {
	$plugins = array(

		array(
			'name'               => esc_html__( 'SB Framework', 'adforest-rest-api' ), 
			'slug'               => 'sb_framework',
			'source'             => ADFOREST_API_PLUGIN_URL.'tgm/sb_framework.zip',
			/*'source'             => $plugin_link,*/
			'required'           => true, 
			'version'            => '3.4.9',
			'force_activation'   => false, 
			'force_deactivation' => false, 
			'external_url'       => '',
			'is_callable'        => '',
		),
		array(
			'name'               => esc_html__( 'Woocommerce', 'adforest-rest-api' ), 
			'slug'               => 'woocommerce',
			'source'             => '',
			'required'           => false, 
			'version'            => '',
			'force_activation'   => false, 
			'force_deactivation' => false,
			'external_url'       => esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.3.3.5.zip' ),
			'is_callable'        => '',
		),
		array(
			'name'               => esc_html__( 'SMS verification - Twillio', 'adforest-rest-api' ), 
			'slug'               => 'wp-twilio-core',
			'source'             => '',
			'required'           => false, 
			'version'            => '',
			'force_activation'   => false, 
			'force_deactivation' => false, 
			'external_url'       => esc_url( 'https://downloads.wordpress.org/plugin/wp-twilio-core.1.1.0.zip' ),
			'is_callable'        => '',
		),		
	
	);
	$config = array( 'id' => 'adforest', 'default_path' => '', 'menu' => 'tgmpa-install-plugins', 'has_notices'  => true, 'dismissable' => false, 'dismiss_msg'  => '', 'is_automatic' => false, 'message' => '', );

	adforestAPI_tgmpa_func( $plugins, $config );
}