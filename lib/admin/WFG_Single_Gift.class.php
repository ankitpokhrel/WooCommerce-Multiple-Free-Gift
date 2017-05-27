<?php

/**
 * Single gift class
 *
 * @package  woocommerce-multiple-free-gift
 * @subpackage lib
 * @author Ankit Pokhrel <info@ankitpokhrel.com.np, @ankitpokhrel>
 * @version 0.0.0
 */
class WFG_Single_Gift
{
    /**
     * Constructor
     *
     * @see  add_action()
     * @since  0.0.0
     */
    public function __construct()
    {
        /* WooCommerce panel tab hooks */
        add_action( 'woocommerce_product_write_panel_tabs', [ $this, 'create_admin_free_gift_tab' ] );
        add_action( 'woocommerce_product_write_panels', [ $this, 'wfg_tab_contents' ] );
        add_action( 'woocommerce_process_product_meta', [ $this, 'process_wfg_tab' ] );
    }

    /**
     * Free gift option tab in product add/edit
     *
     * @access public
     * @since  0.0.0
     *
     * @return void
     */
    public function create_admin_free_gift_tab()
    {
        ?>
        <li class="wfg_free_gift_tab">
            <a href="#wfg_free_gift_tab">
                <?php echo WFG_Common_Helper::translate( 'Free Gift Options' ) ?>
            </a>
        </li>
        <?php
    }

    /**
     * Free gift tab contents
     *
     * @since  0.0.0
     * @access public
     *
     * @return void
     */
    public function wfg_tab_contents()
    {
        $post_id           = get_the_ID();
        $wfg_enabled       = get_post_meta( $post_id, '_wfg_single_gift_enabled', true );
        $wfg_products      = get_post_meta( $post_id, '_wfg_single_gift_products', true );
        $wfg_gifts_allowed = get_post_meta( $post_id, '_wfg_single_gift_allowed', true );
        ?>
        <div id="wfg_free_gift_tab" class="panel woocommerce_options_panel">
            <div class="options_group">
                <p class="form-field wfg_form_field">
                    <input type="checkbox" class="checkbox" style="" name="wfg_single_gift_enabled"
                           id="wfg_single_gift_enabled" <?php echo $wfg_enabled ? 'checked' : '' ?>>
                    <label for="wfg_single_gift_enabled" class="description">
                        <?php echo WFG_Common_Helper::translate( 'Enable free gift for this product.' ); ?>
                    </label>
                    <img class="help_tip" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png"
                         height="16" width="16" data-tip="
					<?php
                    echo WFG_Common_Helper::translate(
                            'Enabling single gift settings will overwrite global settings.'
                    )
                    ?>"/>
                </p>
            </div>
            <p class="wfg-adjust-form-field-gap">
                <label><?php echo WFG_Common_Helper::translate( 'Select Gift Products' ) ?></label>
                <img class="help_tip" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16"
                     width="16" data-tip="
					<?php
                echo WFG_Common_Helper::translate( 'Select single/multiple gift items you want to giveaway for free.' );
                echo '<br/><br/>';
                echo WFG_Common_Helper::translate( 'Note that duplicate items are saved only once.' );
                ?>"/>
            </p>
            <div class="_wfg-repeat">
                <?php echo self::get_ajax_product_selection_design( $wfg_products, $post_id ); ?>
            </div>

            <p class="form-field wfg_form_field">
                <label for="wfg_gifts_allowed" class="description">
                    <?php echo WFG_Common_Helper::translate( 'Number of gifts allowed' ); ?>
                </label>
                <input type="text" class="input-text input-small" name="wfg_single_gift_allowed" id="wfg_gifts_allowed"
                       value="<?php echo ( ! empty( $wfg_gifts_allowed ) && $wfg_gifts_allowed >= 0 ) ? $wfg_gifts_allowed : 1 ?>"/>
                <img class="help_tip" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16"
                     width="16" data-tip="
					<?php
                echo WFG_Common_Helper::translate(
                        'Number of items user are allowed to select as a gift.
							Value zero or less will allow unlimited selection.' )
                ?>"/>
            </p>
        </div>
        <?php
    }

    /**
     * Select box to get products using ajax
     *
     * @since  1.1.0
     * @access private
     *
     * @param array $wfg_products Products selected previously
     * @param int $post_id Post id
     *
     * @return string
     */
    private function get_ajax_product_selection_design( $wfg_products, $post_id )
    {
        $html = "<select class='wfg-ajax-select' id='wfg-select-" . uniqid() . "' name='_wfg_single_gift_products[]' multiple='multiple'>";
        if ( ! empty( $wfg_products ) ) {
            $product_list = WFG_Product_Helper::get_products( [
                    'post__in' => $wfg_products,
                    'post__not_in' => [ $post_id ],
            ], - 1 );
            $products     = $product_list->get_posts();
            if ( ! empty( $products ) ) {
                foreach ( $products as $product ) {
                    $product_id = $product->ID;
                    $selected   = in_array( $product_id, $wfg_products );
                    $html .= "<option value='" . $product_id . "' " . ( $selected ? 'selected' : '' ) . ">" . $product->post_title . "</option>";
                }
            }
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Save free gift tab contents
     *
     * @since  0.0.0
     * @access public
     *
     * @param integer $post_id Current post id
     *
     * @return void
     */
    public function process_wfg_tab( $post_id )
    {
        $wfg_enabled       = ( isset( $_POST['wfg_single_gift_enabled'] ) && $_POST['wfg_single_gift_enabled'] ) ? 1 : 0;
        $wfg_gifts_allowed = ( isset( $_POST['wfg_single_gift_allowed'] ) && $_POST['wfg_single_gift_allowed'] >= 0 ) ? $_POST['wfg_single_gift_allowed'] : 1;
        if ( ! (bool) $wfg_enabled ) {
            delete_post_meta( $post_id, '_wfg_single_gift_enabled' );
        } else {
            update_post_meta( $post_id, '_wfg_single_gift_enabled', $wfg_enabled );
        }

        update_post_meta( $post_id, '_wfg_single_gift_allowed', $wfg_gifts_allowed );
        if ( ! empty( $_POST['_wfg_single_gift_products'] ) ) {
            $products = array_unique( $_POST['_wfg_single_gift_products'] );
            update_post_meta( $post_id, '_wfg_single_gift_products', $products );
        } else {
            delete_post_meta( $post_id, '_wfg_single_gift_products' );
        }
    }

}

/* initialize */
new WFG_Single_Gift();
