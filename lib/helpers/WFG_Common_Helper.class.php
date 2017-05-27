<?php

/**
 * Common Helper class: Contain globally required modules
 *
 * @static
 * @package  woocommerce-multiple-free-gift
 * @subpackage lib/helpers
 * @author Ankit Pokhrel <info@ankitpokhrel.com.np, @ankitpokhrel>
 * @version 0.0.0
 */
class WFG_Common_Helper
{
    /** Current version of the plugin */
    const VERSION = '1.2.3';

    /** @var string Plugin text domain */
    public static $textDomain = 'woocommerce-multiple-free-gift';

    /**
     * Localize text strings
     *
     * @since  0.0.0
     * @see  __()
     *
     * @return string
     */
    public static function translate( $string )
    {
        return __( $string, self::$textDomain );
    }

    /**
     * Displays error message with WordPress default theme.
     *
     * @since  0.0.0
     *
     * @param  string $message Message to display
     *
     * @return void
     */
    public static function error_notice( $message )
    {
        echo '<div class="error wfg-error">';
        echo '<p>' . $message . '</p>';
        echo '</div>';
    }

    /**
     * Displays success message with WordPress default theme.
     *
     * @since  0.0.0
     *
     * @param  string $message Message to display
     *
     * @return void
     */
    public static function success_notice( $message )
    {
        echo '<div class="updated wfg-updated">';
        echo '<p>' . $message . '</p>';
        echo '</div>';
    }

    /**
     * Displays fixed notice at the top of screen in frontend.
     *
     * @since  0.0.0
     *
     * @param  string $message Message to display
     *
     * @return void
     */
    public static function fixed_notice( $message )
    {
        echo '<div class="wfg-fixed-notice">';
        echo '<p>' . $message . '<a class="wfg-fixed-notice-remove" href="javascript:void(0)">x</a></p>';
        echo '</div>';
    }

}
