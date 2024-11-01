<?php
/*
Plugin Name: Ultimate Membership Pro - Razorpay
Plugin URI: https://store.wpindeed.com/
Description: Users may become members by purchasing one time memberships through this payment method.
Version: 1.3
Author: WPIndeed
Author URI: https://store.wpindeed.com

Text Domain: ultimate-membership-pro-razorpay
Domain Path: /languages

@package        Indeed Ultimate Membership Pro AddOn - RazorPay
@author           WPIndeed Development
*/

include plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( !defined( 'UMP_RZR_PATH' ) ){
		define( 'UMP_RZR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'UMP_RZR_URL' ) ){
		define( 'UMP_RZR_URL', plugin_dir_url( __FILE__ ) );
}

$UmpRzrSettings = new \UmpRzr\Settings();
$UmpRzrViewObject = new \UmpRzr\View();

\UmpRzr\Utilities::setSettings( $UmpRzrSettings->get() );
\UmpRzr\Utilities::setLang();
if ( !\UmpRzr\Utilities::canRun() ){
		return;
}

if ( is_admin() ){
		$UmpRzrAdmin = new \UmpRzr\Admin\Main( $UmpRzrSettings->get(), $UmpRzrViewObject );
}
$UmpRzr = new \UmpRzr\Main( $UmpRzrSettings->get(), $UmpRzrViewObject );
