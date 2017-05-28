<?php

/**
 * Handle all frontend operation
 *
 * @package  woocommerce-multiple-free-gift
 * @subpackage lib
 * @author Ankit Pokhrel <info@ankitpokhrel.com.np, @ankitpokhrel>
 * @version 0.0.0
 */
class WFG_Frontend
{
    /** @var boolean Denote if WFG is enabled */
    protected $_wfg_enabled;

    /** @var integer Number of gift items allowed */
    protected $_wfg_gifts_allowed;

    /** @var array Gift products */
    protected $_wfg_products;

    /** @var integer Minimum number of items in cart for gift */
    protected $_minimum_qty;

    /** @var string Free gift type */
    protected $_wfg_type;

    /** @var boolean Denotes if there is a valid criteria */
    protected $_wfg_criteria;

    /**
     * Constructor
     *
     * @see  get_option()
     * @since  0.0.0
     */
    public function __construct()
    {
        $this->_wfg_type    = 'global';
        $this->_minimum_qty = 1;

        $this->_wfg_enabled       = WFG_Settings_Helper::get( $this->_wfg_type . '_enabled', true, 'global_options' );
        $this->_wfg_criteria      = false;
        $this->_wfg_gifts_allowed = 1;
        $this->_wfg_products      = [];

        //Add hooks and filters
        self::__init();
    }


    /**
     * Add require hooks and filters
     *
     * @see  add_action()
     * @since  0.0.0
     * @access private
     */
    private function __init()
    {
        /*  Add free gifts ajax callback */
        add_action( 'wp_ajax_wfg_add_gifts', [ $this, 'wfg_ajax_add_free_gifts' ] );
        add_action( 'wp_ajax_nopriv_wfg_add_gifts', [ $this, 'wfg_ajax_add_free_gifts' ] );

        /* Display gifts in frontend */
        add_action( 'wp_head', [ $this, 'validate_gifts' ] );
        add_action( 'wp_head', [ $this, 'display_gifts' ] );

        /* Do not allow user to update quantity of gift items */
        add_filter( 'woocommerce_is_sold_individually', [ $this, 'wfg_disallow_qty_update' ], 10, 2 );

        /* Remove gifts when main item is removed */
        add_action( 'woocommerce_cart_item_removed', [ $this, 'wfg_item_removed' ], 10, 2 );

        /* Final cart gift validation as last step when checking cart items ( as other check process could have
         * removed products from the cart ) */
        add_action( 'woocommerce_check_cart_items', [ $this, 'check_cart_items' ], 99 );

    }

    /**
     * Overwrite default settings with actual settings
     *
     * @since  0.0.0
     * @access private
     *
     * @return void
     */
    private function __get_actual_settings()
    {
        //single gift
        $post_id = $this->__get_post_id();
        if ( empty( $post_id ) ) {
            return;
        }

        $wfg_enabled = get_post_meta( $post_id, '_wfg_single_gift_enabled', true );
        if ( (bool) $wfg_enabled ) {
            $this->_wfg_type          = 'single_gift';
            $this->_wfg_enabled       = $wfg_enabled;
            $this->_wfg_criteria      = true;
            $this->_wfg_gifts_allowed = get_post_meta( $post_id, '_wfg_single_gift_allowed', true );
            $this->_wfg_products      = get_post_meta( $post_id, '_wfg_single_gift_products', true );

            return;
        }

        return $this->__hook_global_settings();
    }

    /**
     * Fetch actual product id
     *
     * @since  1.1.0
     * @access private
     *
     * @return integer|null
     */
    private function __get_post_id()
    {
        $post_id = null;
        foreach ( WC()->cart->cart_contents as $key => $content ) {

            // If there are bundles in the cart, exclude bundled products
            if ( isset( $content['bundled_by'] ) ) {
                continue;
            }

            $is_gift_product = ! empty( $content['variation_id'] ) && (bool) get_post_meta( $content['variation_id'],
                    '_wfg_gift_product' );
            if ( ! $is_gift_product ) {
                return $content['product_id'];
            }
        }

        return $post_id;
    }

    /**
     * Hook global settings to actual settings
     *
     * @since  1.1.0
     * @access private
     *
     * @return void
     */
    private function __hook_global_settings()
    {
        //look for global settings
        $wfg_global_settings = WFG_Settings_Helper::get( '', false, 'global_settings', false );
        if ( empty( $wfg_global_settings ) ) {
            return;
        }

        foreach ( $wfg_global_settings as $setting ) {
            $gift_criteria = $setting['condition'];
            $criteria      = WFG_Criteria_Helper::parse_criteria( $gift_criteria );
            if ( $criteria ) {
                $this->__set_actual_values( $setting );

                return;
            }
        }
    }

    /**
     * Set required values
     *
     * @since  1.1.0
     * @access private
     *
     * @return void
     */
    private function __set_actual_values( $setting )
    {
        $this->_wfg_criteria      = true;
        $this->_wfg_gifts_allowed = $setting['num_allowed'];
        $this->_wfg_products      = ! empty( $setting['items'] ) ? array_unique( $setting['items'] ) : [];
    }

    /**
     * Add free item to cart.
     *
     * @since  0.0.0
     * @access public
     *
     * @return void
     */
    public function wfg_ajax_add_free_gifts()
    {
        if ( empty( $_POST ) || ! wp_verify_nonce( $_POST['_wfg_nonce'], 'wfg_add_free_gifts' ) ) {
            return;
        }

        if ( empty( $_POST['wfg_free_items'] ) ) {
            return;
        }

        //check if gift item is valid
        self::__get_actual_settings();
        if ( ! WFG_Product_Helper::crosscheck_gift_items( $_POST['wfg_free_items'], $this->_wfg_products ) ) {
            return;
        }

        foreach ( $_POST['wfg_free_items'] as $item ) {
            $free_product = WFG_Product_Helper::create_gift_variation( $item );
            WFG_Product_Helper::add_free_product_to_cart( $item, $free_product );
        }

        wp_die();
    }

    /**
     * Disallow qty update in gift products.
     *
     * @since  0.0.0
     * @access public
     *
     * @param  boolean $return Is return product
     * @param  object $product Product object
     *
     * @return integer|void
     */
    public function wfg_disallow_qty_update( $return, $product )
    {
        $variation_id = $product->get_id();

        if ( $variation_id ) {
            $is_wfg_variation = get_post_meta( $variation_id, '_wfg_gift_product', true );
            if ( (bool) $is_wfg_variation ) {
                return 1;
            }
        }

        return $return;
    }

    /**
     * Remove all gifts when main item is removed.
     *
     * @since  0.0.0
     * @access public
     *
     * @param  string $cart_item_key Removed item key
     * @param  object $cart Cart object
     *
     * @return void
     */
    public function wfg_item_removed( $cart_item_key, $cart )
    {
        //no need to process further if qty is zero
        if ( empty( $cart->cart_contents ) ) {
            return;
        }

        //check if removed item is a variation or main product
        $removed_item = $cart->removed_cart_contents[ $cart_item_key ];
        if ( ! empty( $removed_item['variation_id'] ) ) {
            return;
        }

        if ( 'global' == $this->_wfg_type && 0 == WFG_Product_Helper::get_main_product_count() ) {
            foreach ( $cart->cart_contents as $key => $content ) {
                WC()->cart->remove_cart_item( $key );
            }
        }
    }

    /**
     * Remove gifts if the criteria is invalid.
     *
     * @since  0.0.0
     * @access public
     *
     * @return null|boolean
     */
    public function validate_gifts()
    {
        if ( ! is_cart() ) {
            return false;
        }

        if ( ! $this->__gift_item_in_cart() ) {
            return false;
        }

        self::__get_actual_settings();
        self::_validate_single_gift_condition();

        $cart_items = WFG_Product_Helper::get_gift_products_in_cart();
        if ( ! $this->_wfg_criteria || ! WFG_Product_Helper::crosscheck_gift_items( $cart_items,
                $this->_wfg_products )
        ) {
            //remove gift products
            if ( $this->__remove_gift_products() ) {
                $this->__set_notice_text();
            }
        }

    }

    /**
     * Checks cart items, emitting an error notice if gift criteria is not met
     *
     * @since  0.0.0
     * @access public
     *
     * @return void
     */
    public function check_cart_items()
    {
        if ( ! is_cart() && ! is_checkout() ) {
            return;
        }

        if ( ! $this->__gift_item_in_cart() ) {
            return;
        }

        self::__get_actual_settings();
        self::_validate_single_gift_condition();

        $cart_items = WFG_Product_Helper::get_gift_products_in_cart();
        if ( ! $this->_wfg_criteria || ! WFG_Product_Helper::crosscheck_gift_items( $cart_items,
                $this->_wfg_products, $this->_wfg_type )
        ) {
            // Generate error notice to abort any checkout transaction in process
            wc_add_notice( WFG_Common_Helper::translate( 'The cart contains gift items that are going to be removed, as gift criteria isn\'t fulfilled. Please reload the page.' ),
                'error' );
        }

    }

    /**
     * Set notice text.
     *
     * @since  1.1.0
     * @access private
     *
     * @return void
     */
    private function __set_notice_text()
    {
        $noticeText = WFG_Settings_Helper::get( 'invalid_condition_text', false, 'global_options' );
        if ( false === $noticeText ) {
            $noticeText = WFG_Common_Helper::translate( 'Gift items removed as gift criteria isn\'t fulfilled' );
        }

        WFG_Common_Helper::fixed_notice( $noticeText );
    }

    /**
     * Validate single gift condition.
     *
     * @since  1.1.0
     * @access protected
     *
     * @return boolean
     */
    protected function _validate_single_gift_condition()
    {
        if ( 'single_gift' !== $this->_wfg_type ) {
            return false;
        }

        $total_items_in_cart = WFG_Product_Helper::get_main_product_count();
        if ( 1 !== $total_items_in_cart ) {
            return false;
        }

        return $this->__remove_gift_products();
    }

    /**
     * Remove gifts products.
     *
     * @since  1.1.0
     * @access private
     *
     * @return boolean
     */
    private function __remove_gift_products()
    {
        $removed = false;
        foreach ( WC()->cart->cart_contents as $key => $content ) {
            $is_gift_product = ! empty( $content['variation_id'] ) && (bool) get_post_meta( $content['variation_id'],
                    '_wfg_gift_product' );
            if ( $is_gift_product && ! in_array( $content['product_id'], $this->_wfg_products ) ) {
                WC()->cart->remove_cart_item( $key );
                $removed = true;
            }
        }

        return $removed;
    }

    /**
     * Display gift popup in frontend.
     *
     * @since  0.0.0
     * @access public
     *
     * @return void
     */
    public function display_gifts()
    {
        if ( ! is_cart() ) {
            return;
        }

        if ( $this->__gift_item_in_cart() ) {
            return;
        }

        self::__get_actual_settings();

        //check gift criteria
        if ( ! $this->_check_global_gift_criteria() ) {
            return;
        }

        //enqueue required styles for this page
        wp_enqueue_style( 'wfg-core-styles', plugins_url( '/css/wfg-styles.css', dirname( __FILE__ ) ) );
        wp_enqueue_style( 'wfg-template-styles',
            plugins_url( '/templates/default/wfg-default.css', dirname( __FILE__ ) ) );

        $items = WFG_Product_Helper::get_cart_products();
        if ( $items['count'] >= $this->_minimum_qty ) {
            $this->_show_gifts();
        }
    }

    /**
     * Display gifts.
     *
     * @since 1.1.0
     * @access public
     *
     * @return void
     */
    protected function _show_gifts()
    {
        if ( ! $this->_wfg_enabled ) {
            return;
        }

        if ( empty( $this->_wfg_products ) ) {
            return;
        }

        $wfg_free_products = [];
        foreach ( $this->_wfg_products as $product ) {
            $wfg_free_products[] = WFG_Product_Helper::get_product_details( $product );
        }

        $localize = [
            'gifts_allowed' => ( false !== $this->_wfg_gifts_allowed ) ? $this->_wfg_gifts_allowed : 1,
        ];

        echo '<script>';
        echo '/* ' . '<![CDATA[ */';
        echo 'var WFG_SPECIFIC =' . json_encode( $localize );
        echo '/* ]]> */';
        echo '</script>';

        include( PLUGIN_DIR . 'templates/default/template-default.php' );
    }

    /**
     * Check if global gift condition is satisfied.
     *
     * @since 1.1.0
     * @access public
     *
     * @return boolean
     */
    protected function _check_global_gift_criteria()
    {
        if ( 'single_gift' === $this->_wfg_type ) {
            return true;
        }

        $gift_criteria = WFG_Settings_Helper::get( 'global_gift_criteria' );
        if ( empty( $gift_criteria ) ) {
            return true;
        }

        return WFG_Criteria_Helper::parse_criteria( $gift_criteria );
    }

    /**
     * Check if there is already gift item in the cart
     *
     * @since  0.0.0
     * @access private
     *
     * @return boolean
     */
    private function __gift_item_in_cart()
    {
        $cart = WC()->cart->get_cart();
        if ( count( $cart ) < 0 ) {
            return false;
        }

        foreach ( $cart as $cart_item_key => $values ) {
            $product      = $values['data'];
            $variation_id = $product->get_id();
            if ( $variation_id ) {
                $is_wfg_variation = get_post_meta( $variation_id, '_wfg_gift_product', true );
                if ( (bool) $is_wfg_variation ) {
                    return true;
                }
            }
        }

        return false;
    }

}

/* initialize */
new WFG_Frontend();
