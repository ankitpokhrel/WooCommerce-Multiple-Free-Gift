<?php

/**
 * Admin page
 *
 * @package woocommerce-multiple-free-gift
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
     * @see     add_action()
     */
    public function __construct()
    {
        add_action( 'admin_menu', [ $this, 'main_menu' ] );

        //enqueue necessary scripts and styles
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

        //register ajax call to fetch products
        add_action( 'wp_ajax_product_list_callback', [ $this, 'ajax_product_list_callback' ] );
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
        add_menu_page(
            WFG_Common_Helper::translate( 'WooCommerce Multiple Free Gift' ),
            WFG_Common_Helper::translate( 'Woo Free Gift' ),
            'manage_options',
            'woocommerce-multiple-free-gift',
            [ $this, 'main_menu_template' ],
            'dashicons-cart'
        );

        add_submenu_page(
            'woocommerce-multiple-free-gift',
            WFG_Common_Helper::translate( 'Gift Criteria' ) . ' - ' .
            WFG_Common_Helper::translate( 'WooCommerce Multiple Free Gift' ),
            WFG_Common_Helper::translate( 'Gift Criteria' ),
            'manage_options',
            'woocommerce-multiple-free-gift-criteria',
            [ $this, 'wfg_criteria_template' ]
        );

        add_submenu_page(
            'woocommerce-multiple-free-gift',
            WFG_Common_Helper::translate( 'General Settings' ) . ' - ' .
            WFG_Common_Helper::translate( 'WooCommerce Multiple Free Gift' ),
            WFG_Common_Helper::translate( 'General Settings' ),
            'manage_options',
            'woocommerce-multiple-free-gift-settings',
            [ $this, 'wfg_general_settings' ]
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
        wp_enqueue_style( 'wmfg-admin-styles', plugins_url( '/admin/css/wfg-admin-styles.css', dirname( __FILE__ ) ) );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style(
            'wfg-selectize',
            plugins_url( '/admin/js/plugins/selectize/selectize.css', dirname( __FILE__ ) )
        );

        //enqueue scripts
        wp_enqueue_script(
            'wmfg-admin-scripts',
            plugins_url( '/admin/js/wfg-admin-scripts.js', dirname( __FILE__ ) ),
            [ 'jquery', 'jquery-ui-dialog' ]
        );

        wp_enqueue_script(
            'wfg-selectize-lib',
            plugins_url( '/admin/js/plugins/selectize/selectize.min.js', dirname( __FILE__ ) ),
            [ 'jquery' ]
        );

        wp_enqueue_script( 'jquery-ui-dialog', false, [ 'jquery' ] );
        wp_enqueue_script( 'jquery-ui-sortable', false, [ 'jquery' ] );

        wp_localize_script(
            'wmfg-admin-scripts',
            'WMFG_SPECIFIC',
            [
                'loading_url' => plugins_url( '/admin/img/loading.gif', dirname( __FILE__ ) ),
            ]
        );
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
        if ( ( isset( $_POST['_wfg_global_hidden'] ) && 'Y' == $_POST['_wfg_global_hidden'] )
             && wp_verify_nonce( $_POST['_wfg_global_nonce'], 'wfg_global_settings' )
        ) {

            $wfg_globally_enabled = isset( $_POST['wfg_globally_enabled'] ) ? true : false;
            $enabled              = update_option( '_wfg_global_enabled', $wfg_globally_enabled );

            if ( isset( $_POST['_wfg_criteria'] ) ) {
                $user_criteria = $_POST['_wfg_criteria'];

                //remove extra fields
                unset( $user_criteria['_wfg_global_nonce'] );
                unset( $user_criteria['_wp_http_referer'] );
                unset( $user_criteria['_wfg_global_hidden'] );

                $conditionSaved = update_option( '_wfg_global_settings', $user_criteria );
                if ( $enabled || $conditionSaved ) {
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
                if ( get_option( '_wfg_global_settings' ) !== false ) {
                    if ( delete_option( '_wfg_global_settings' ) ) {
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

        include 'pages/main_menu_page.php';
    }

    public function wfg_criteria_template()
    {
        if ( ( isset( $_POST['_wfg_criteria_hidden'] ) && $_POST['_wfg_criteria_hidden'] == 'Y' )
             && wp_verify_nonce( $_POST['_wfg_criteria_nonce'], 'wfg_criteria_settings' )
        ) {

            if ( isset( $_POST['_wfg_criteria'] ) ) {
                $user_criteria = $_POST['_wfg_criteria'];
                foreach ( $user_criteria as &$criteria ) {
                    $criteria['slug'] = sanitize_title( $criteria['name'] );
                }

                if ( update_option( '_wfg_criteria', $user_criteria ) ) {
                    WFG_Common_Helper::success_notice(
                        WFG_Common_Helper::translate(
                            'Criteria saved successfully'
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
                if ( get_option( '_wfg_criteria' ) !== false ) {
                    if ( delete_option( '_wfg_criteria' ) ) {
                        WFG_Common_Helper::success_notice(
                            WFG_Common_Helper::translate(
                                'Criteria saved successfully'
                            )
                        );
                    }
                } else {
                    WFG_Common_Helper::error_notice(
                        WFG_Common_Helper::translate(
                            'No criteria to save. You can add criteria by clicking <em>Add New Criteria</em> button'
                        )
                    );
                }
            }

            //update settings
            WFG_Settings_Helper::force_init();
        }

        include 'pages/gift_criteria.php';
    }

    public function wfg_general_settings()
    {
        if ( ( isset( $_POST['_wfg_general_settings_submitted'] ) && $_POST['_wfg_general_settings_submitted'] == 'Y' )
             && wp_verify_nonce( $_POST['_wfg_general_nonce'], 'wfg_general_settings' )
        ) {

            $popup_overlay = isset( $_POST['_wfg_popup_overlay'] ) ? 1 : 0;
            $popup_heading = isset( $_POST['_wfg_popup_heading'] ) ? $_POST['_wfg_popup_heading'] : WFG_Common_Helper::translate( 'Choose your free gift' );
            $invalid_text  = isset( $_POST['_wfg_invalid_condition_text'] ) ? $_POST['_wfg_invalid_condition_text'] : WFG_Common_Helper::translate( 'Gift items removed as gift criteria isn\'t fulfilled' );
            $add_gift_text = isset( $_POST['_wfg_popup_add_gift_text'] ) ? $_POST['_wfg_popup_add_gift_text'] : WFG_Common_Helper::translate( 'Add Gifts' );
            $cancel_text   = isset( $_POST['_wfg_popup_cancel_text'] ) ? $_POST['_wfg_popup_cancel_text'] : WFG_Common_Helper::translate( 'No Thanks' );

            $overlay     = update_option( '_wfg_popup_overlay', $popup_overlay );
            $heading     = update_option( '_wfg_popup_heading', $popup_heading );
            $invalid     = update_option( '_wfg_invalid_condition_text', $invalid_text );
            $add_gift    = update_option( '_wfg_popup_add_gift_text', $add_gift_text );
            $cancel_text = update_option( '_wfg_popup_cancel_text', $cancel_text );

            if ( $overlay || $heading || $invalid || $add_gift || $cancel_text ) {
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

        include 'pages/general_settings.php';
    }

    public function ajax_product_list_callback()
    {
        $q = isset( $_GET['q'] ) ? $_GET['q'] : '';
        if ( ! $q ) {
            return 0;
        }

        $products = WFG_Product_Helper::get_products( [ 's' => $q, 'posts_per_page' => 15 ] );
        $list     = [];
        if ( ! empty( $products ) && ! empty( $products->posts ) ) {
            foreach ( $products->posts as $product ) {
                $list[] = [ 'id' => $product->ID, 'text' => $product->post_title ];
            }
        }

        echo json_encode( [ 'options' => $list ] );
        wp_die();
    }
}

/** Initialize */
new WFG_Admin();
