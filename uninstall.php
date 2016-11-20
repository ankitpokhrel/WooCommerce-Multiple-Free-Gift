<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

//cleanup plugin data
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_title=%s AND post_type=%s", 'wfg_gift_product',
    'product_variation' ) );

//delete single gift options
delete_post_meta_by_key( '_wfg_single_gift_allowed' );
delete_post_meta_by_key( '_wfg_single_gift_products' );
delete_post_meta_by_key( '_wfg_gift_product' );

//delete global options
delete_option( '_wfg_global_enabled' );
delete_option( '_wfg_global_settings' );
delete_option( '_wfg_criteria' );
delete_option( '_wfg_popup_overlay' );
delete_option( '_wfg_popup_heading' );
delete_option( '_wfg_invalid_condition_text' );
delete_option( '_wfg_popup_add_gift_text' );
delete_option( '_wfg_popup_cancel_text' );
