<?php

/**
 * Main plugin class
 *
 * @package  woocommerce-multiple-free-gift
 * @subpackage lib
 * @author Ankit Pokhrel <info@ankitpokhrel.com.np, @ankitpokhrel>
 * @version 1.2.0
 */
class Woocommerce_Multiple_Free_Gift
{
    /**
     * Constructor
     *
     * @see  add_action()
     * @since  0.0.0
     */
    public function __construct()
    {
        //check if woocommerce plugin is installed and activated
        add_action( 'plugins_loaded', [ $this, 'wfg_validate_installation' ] );

        //load plugin textdomain
        add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );

        //enqueue necessary scripts and styles
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_global_scripts' ] );

        //add action links
        add_filter( 'plugin_action_links_' . PLUGIN_BASE, [ $this, 'wfg_action_links' ] );
    }

    /**
     * Plugin activation hook
     *
     * @access  public
     * @since  0.0.0
     *
     * @see  add_option()
     *
     * @return void
     */
    public static function wfg_activate()
    {
        update_option( '_wfg_popup_overlay', 1 );
        update_option( '_wfg_popup_heading', WFG_Common_Helper::translate( 'Choose your free gift' ) );
        update_option( '_wfg_invalid_condition_text',
            WFG_Common_Helper::translate( 'Gift items removed as gift criteria isn\'t fulfilled' ) );
        update_option( '_wfg_popup_add_gift_text', WFG_Common_Helper::translate( 'Add Gifts' ) );
        update_option( '_wfg_popup_cancel_text', WFG_Common_Helper::translate( 'No Thanks' ) );
    }

    /**
     * Enqueue required global styles and scirpts
     *
     * @access public
     * @since  0.0.0
     *
     * @see  wp_enqueue_style()
     *
     * @return void
     */
    public function enqueue_global_scripts()
    {
        //enqueue styles
        wp_enqueue_style( 'wfg-styles', plugins_url( '/css/wfg-styles.css', dirname( __FILE__ ) ) );

        //enqueue scripts
        wp_enqueue_script( 'wfg-scripts', plugins_url( '/js/wfg-scripts.js', dirname( __FILE__ ) ), [ 'jquery' ] );
    }

    /**
     * Add notice if WooCommerce plugin is not activated
     *
     * @since  0.0.0
     * @access public
     *
     * @see  add_action()
     *
     * @return void
     */
    public function wfg_validate_installation()
    {
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', [ $this, 'wfg_plugin_required_notice' ] );
        }
    }

    /**
     * Error notice: WooCommerce Plugin is required for this plugin to work
     *
     * @access public
     * @since  0.0.0
     *
     * @return void
     */
    public function wfg_plugin_required_notice()
    {
        WFG_Common_Helper::error_notice(
            WFG_Common_Helper::translate(
                'WooCommerce Free Gift plugin requires
				<a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>
				plugin to work. Please make sure that WooCommerce is installed and activated.'
            )
        );
    }

    /**
     * Add premium version link
     *
     * @access public
     * @since  1.0.0
     * @action plugin_action_links
     *
     * @param  array $links Action links
     *
     * @return array
     */
    public function wfg_action_links( $links )
    {
        $wfg_links = [
            '<a href="' . PRO_URL . '" target="_blank">Upgrade to Premium</a>',
        ];

        return array_merge( $links, $wfg_links );
    }

    /**
     * Load the plugin's textdomain hooked to 'plugins_loaded'.
     *
     * @since 0.0.0
     * @access public
     *
     * @see    load_plugin_textdomain()
     * @see    plugin_basename()
     * @action    plugins_loaded
     *
     * @return    void
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            WFG_Common_Helper::$textDomain,
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/../languages/'
        );
    }

}
