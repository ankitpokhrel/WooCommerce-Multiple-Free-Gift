<?php
$overlay = WFG_Settings_Helper::get( 'popup_overlay', true, 'global_options' );
if ( $overlay ):
    ?>
    <div class="wfg-overlay" style="display:none"></div>
<?php endif; ?>
<div class='wfg-popup' style="display:none">
    <h2 class="wfg-title">
        <?php
        $heading = WFG_Settings_Helper::get( 'popup_heading', false, 'global_options' );
        if ( false !== $heading ) {
            echo $heading;
        } else {
            echo WFG_Common_Helper::translate( 'Choose your free gift' );
        }
        ?>
    </h2>
    <div class="wfg-gifts">
        <form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post">
            <input type="hidden" name="action" value="wfg_add_gifts"/>
            <?php
            wp_nonce_field( 'wfg_add_free_gifts', '_wfg_nonce' );
            if ( ! empty( $wfg_free_products ) ):
                foreach ( $wfg_free_products as $product ):
                    if ( empty( $product->detail ) ) {
                        continue;
                    }
                    ?>
                    <div class="wfg-gift-item">
                        <div class="wfg-heading">
                            <input type="checkbox" class="wfg-checkbox" name="wfg_free_items[]"
                                   id="wfg-item-<?php echo $product->detail->ID ?>"
                                   value="<?php echo $product->detail->ID ?>"/>
                            <label for="wfg-item-<?php echo $product->detail->ID ?>" class="wfg-title">
                                <img src="<?php echo $product->image ?>" style="width:150px; height:150px;" alt=""/>
                            </label>

                            <h3><?php echo $product->detail->post_title ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="wfg-actions">
                    <button class="button wfg-button wfg-add-gifts">
                        <?php
                        $add_gift_text = WFG_Settings_Helper::get( 'popup_add_gift_text', false, 'global_options' );
                        if ( false !== $add_gift_text ) {
                            echo $add_gift_text;
                        } else {
                            echo WFG_Common_Helper::translate( 'Add Gifts' );
                        }
                        ?>
                    </button>
                    <button class="button wfg-button wfg-no-thanks" type="button">
                        <?php
                        $cancel_text = WFG_Settings_Helper::get( 'popup_cancel_text', false, 'global_options' );
                        if ( false !== $cancel_text ) {
                            echo $cancel_text;
                        } else {
                            echo WFG_Common_Helper::translate( 'No Thanks' );
                        }
                        ?>
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
