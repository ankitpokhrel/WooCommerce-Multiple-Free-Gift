<?php
/**
 * Admin page
 *
 * @package woocommerce-free-gift
 * @subpackage lib/admin
 * 
 * @author Ankit Pokhrel <info@ankitpokhrel.com.np, @ankitpokhrel>
 */
class WFG_Admin
{
	/**
	 * Constructor
	 *
	 * @access public
	 * @since 0.0.0
	 *
	 * @see	 add_action()
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array($this, 'main_menu') );

		//enqueue necessary scripts and styles
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );
	}

	/**
	 * Add main menu page
	 *
	 * @access public
	 * @see  add_menu_page()
	 * 
	 * @return void
	 */
	public function main_menu()
	{
		add_object_page(
			WFG_Common_Helper::translate('Woocommerce Free Gift'),
			WFG_Common_Helper::translate('Woo Free Gift'),
			'manage_options',
			'woocommerce-free-gift',
			array($this, 'main_menu_template'),
			'dashicons-cart'
		);

		add_submenu_page(
			'woocommerce-free-gift',
			WFG_Common_Helper::translate('Gift Criteria - Woocommerce Free Gift'),
			WFG_Common_Helper::translate('Gift Criteria'),
			'manage_options',
			'woocommerce-free-gift-criteria',
			array($this, 'wfg_criteria_template')
		);

		add_submenu_page(
			'woocommerce-free-gift',
			WFG_Common_Helper::translate('General Settings - Woocommerce Free Gift'),
			WFG_Common_Helper::translate('General Settings'),
			'manage_options',
			'woocommerce-free-gift-settings',
			array($this, 'wfg_general_settings')
		);
	}

	/**
	 * Enqueue required styles and scirpts for admin
	 *
	 * @access public
	 * @since  0.0.0
	 *
	 * @see  wp_enqueue_style()
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts()
	{
		//enqueue styles
		wp_enqueue_style( 'wfg-admin-styles', plugins_url( '/admin/css/wfg-admin-styles.css', dirname(__FILE__) ) );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		//enqueue scripts
		wp_enqueue_script( 'wfg-admin-scripts', plugins_url( '/admin/js/wfg-admin-scripts.js', dirname(__FILE__) ), array('jquery', 'jquery-ui-dialog') );
		wp_enqueue_script( 'jquery-ui-dialog', false, array('jquery') );
		wp_enqueue_script( 'jquery-ui-sortable', false, array('jquery') );
	}

	/**
	 * Main settings page template
	 *
	 * @access public
	 * @since 0.0.0
	 * 
	 * @return void
	 */
	public function main_menu_template()
	{
		if( ( isset($_POST['_wfg_global_hidden']) && $_POST['_wfg_global_hidden'] == 'Y' )
				&& wp_verify_nonce( $_POST['_wfg_global_nonce'], 'wfg_global_settings') ) {

			$wfg_globally_enabled = isset( $_POST['wfg_globally_enabled'] ) ? true : false;
			$enabled = update_option('_wfg_global_enabled', $wfg_globally_enabled);

			if( isset($_POST['_wfg_criteria']) ) {
				$user_criteria = $_POST['_wfg_criteria'];

				//remove extra fields
				unset( $user_criteria['_wfg_global_nonce'] );
				unset( $user_criteria['_wp_http_referer'] );
				unset( $user_criteria['_wfg_global_hidden'] );

				$user_criteria['criteria-1']['condition'] = '';
				$conditionSaved = update_option('_wfg_global_settings', $user_criteria);
				if( $enabled || $conditionSaved ) {
					WFG_Common_Helper::success_notice(
							WFG_Common_Helper::translate(
									'Gift conditions saved successfully'
								)
						);

					WFG_Settings_Helper::force_init();
				} else {
					WFG_Common_Helper::error_notice(
						WFG_Common_Helper::translate(
								'There was a problem. Please try again.'
							)
					);
				}
			} else {
				if( get_option('_wfg_global_settings') !== false ) {
					if( delete_option('_wfg_global_settings') ) {
						WFG_Common_Helper::success_notice(
							WFG_Common_Helper::translate(
									'Gift conditions emptied successfully'
								)
						);
					}
				} else {
					WFG_Common_Helper::error_notice(
						WFG_Common_Helper::translate(
								'No gift conditions to save. You can add conditions by clicking <em>Add new gift condition</em> button'
							)
					);
				}
			}

			//update settings
			WFG_Settings_Helper::force_init();
		}

		include "pages/main_menu_page.php";
	}

	public function wfg_criteria_template()
	{
		include "pages/gift_criteria.php";
	}

	public function wfg_general_settings()
	{
		if( ( isset($_POST['_wfg_general_settings_submitted']) && $_POST['_wfg_general_settings_submitted'] == 'Y' )
				&& wp_verify_nonce( $_POST['_wfg_general_nonce'], 'wfg_general_settings') ) {

			$popup_overlay = isset( $_POST['_wfg_popup_overlay'] ) ? 1 : 0;
			$popup_heading = isset( $_POST['_wfg_popup_heading'] )  ? $_POST['_wfg_popup_heading'] : WFG_Common_Helper::translate('Choose your free gift');
			$invalid_text = isset( $_POST['_wfg_invalid_condition_text'] )  ? $_POST['_wfg_invalid_condition_text'] : WFG_Common_Helper::translate('Gift items removed as gift criteria isn\'t fulfilled');
			$add_gift_text = isset( $_POST['_wfg_popup_add_gift_text'] ) ? $_POST['_wfg_popup_add_gift_text'] : WFG_Common_Helper::translate('Add Gifts');
			$cancel_text = isset( $_POST['_wfg_popup_cancel_text'] ) ? $_POST['_wfg_popup_cancel_text'] : WFG_Common_Helper::translate('No Thanks');

			$overlay = update_option('_wfg_popup_overlay', $popup_overlay);
			$heading = update_option('_wfg_popup_heading', $popup_heading);
			$invalid = update_option('_wfg_invalid_condition_text', $invalid_text);
			$add_gift = update_option('_wfg_popup_add_gift_text', $add_gift_text);
			$cancel_text = update_option('_wfg_popup_cancel_text', $cancel_text);

			if( $overlay || $heading || $invalid || $add_gift || $cancel_text ) {
				WFG_Common_Helper::success_notice(
							WFG_Common_Helper::translate(
									'Settings saved successfully'
								)
						);

				//update settings
				WFG_Settings_Helper::force_init();
			} else {
				WFG_Common_Helper::error_notice(
						WFG_Common_Helper::translate(
								'No changes to save.'
							)
					);
			}
		}

		include "pages/general_settings.php";
	}
}

/** Initialize */
new WFG_Admin();
