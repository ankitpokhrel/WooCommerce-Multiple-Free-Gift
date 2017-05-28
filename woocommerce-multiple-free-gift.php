<?php
/***
 * Plugin Name: WooCommerce Multiple Free Gift
 * Plugin URI: http://wordpress.org/plugins/woocommerce-multiple-free-gift
 * Description: WooCommerce giveaway made easy.
 * Version: 1.2.3
 * Author: Ankit Pokhrel
 * Author URI: http://ankitpokhrel.com
 * Text Domain: woocommerce-multiple-free-gift
 * Domain Path: /languages
 *
 * Copyright (c) 2015 Ankit Pokhrel <info@ankitpokhrel.com.np, http://ankitpokhrel.com>.
 */

//Avoid direct calls to this file
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    die( 'Access Forbidden' );
}

define( 'PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRO_URL', 'http://ankitpokhrel.com.np/blog/downloads/woocommerce-multiple-free-gift-plugin-pro/' );

include 'lib/helpers/WFG_Common_Helper.class.php';
include 'lib/helpers/WFG_Settings_Helper.class.php';
include 'lib/helpers/WFG_Product_Helper.class.php';
include 'lib/helpers/WFG_Criteria_Helper.class.php';
include 'lib/admin/WFG_Admin.class.php';
include 'lib/admin/WFG_Single_Gift.class.php';
include 'lib/WFG_Frontend.class.php';
include 'lib/Woocommerce_Multiple_Free_Gift.class.php';

//plugin activation hook
register_activation_hook( __FILE__, array( 'Woocommerce_Multiple_Free_Gift', 'wfg_activate' ) );

/** Initialize the awesome */
new Woocommerce_Multiple_Free_Gift();
