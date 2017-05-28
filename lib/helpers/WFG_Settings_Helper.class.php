<?php

/**
 * Get all user settings and cache it for future use.
 *
 * @package  woocommerce-multiple-free-gift
 * @subpackage lib
 * @author Ankit Pokhrel <info@ankitpokhrel.com.np, @ankitpokhrel>
 * @version 0.0.0
 * @static
 */
class WFG_Settings_Helper
{
    /* Option key prefix */
    const PREFIX = '_wfg_';

    /* Flag to check if the setting is already fetched */
    private static $__initialized = false;

    /* Hold all settings */
    protected static $_settings = [];

    /**
     * Prevent the instantiation of class using
     * private constructor
     */
    private function __construct()
    {
    }

    /**
     * Fetch settings from database if not already fetched.
     *
     * @access protected
     * @static
     * @see  get_option()
     *
     * @return void
     */
    protected static function __init()
    {
        if ( self::$__initialized ) {
            return;
        }

        //fetch settings
        $settings['global_settings']                                           = get_option( self::PREFIX . 'global_settings' );
        $settings['global_options'][ self::PREFIX . 'global_enabled' ]         = get_option( self::PREFIX . 'global_enabled' );
        $settings['global_options'][ self::PREFIX . 'popup_overlay' ]          = get_option( self::PREFIX . 'popup_overlay' );
        $settings['global_options'][ self::PREFIX . 'popup_heading' ]          = get_option( self::PREFIX . 'popup_heading' );
        $settings['global_options'][ self::PREFIX . 'invalid_condition_text' ] = get_option( self::PREFIX . 'invalid_condition_text' );
        $settings['global_options'][ self::PREFIX . 'popup_add_gift_text' ]    = get_option( self::PREFIX . 'popup_add_gift_text' );
        $settings['global_options'][ self::PREFIX . 'popup_cancel_text' ]      = get_option( self::PREFIX . 'popup_cancel_text' );
        $settings['criteria']                                                  = get_option( self::PREFIX . 'criteria' );

        if ( ! empty( $settings ) ) {
            self::$_settings = $settings;
        }

        self::$__initialized = true;
    }

    /**
     * Forcefully reinitialze settings
     *
     * @access public
     * @static
     *
     * @return void
     */
    public static function force_init()
    {
        self::$__initialized = false;
    }

    /**
     * Check if setting is available.
     *
     * @access public
     * @static
     *
     * @return boolean
     */
    public static function has_settings()
    {
        self::__init();

        return ! empty( self::$_settings );
    }

    /**
     * Check settings array and return setting if available.
     *
     * @access public
     * @static
     *
     * @return string|boolean
     */
    public static function get( $key, $bool = false, $type = 'global_settings', $prefix = true )
    {
        self::__init();

        if ( $prefix ) {
            $key = self::PREFIX . $key;
        }

        if ( empty( $key ) && isset( self::$_settings[ $type ] ) ) {
            return self::$_settings[ $type ];
        }

        if ( isset( self::$_settings[ $type ][ $key ] ) ) {
            return $bool ? (bool) self::$_settings[ $type ][ $key ] : self::$_settings[ $type ][ $key ];
        }

        return false;
    }

}
