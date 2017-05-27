<div id="wfg_setting" class="wrap">
    <div class="header clearfix">
        <div class="left">
            <?php
            echo '<img src="' . plugins_url( 'img/wfg-logo.png', dirname( __FILE__ ) ) . '" class="wfg-logo" />';
            ?>
        </div>
        <div class="left">
            <p class="title"><?php echo WFG_Common_Helper::translate( 'WooCommerce Multiple Free Gift' ) ?></p>
        </div>
    </div>
    <div class="options_group margin-top-20">
        <p class="switcher">
            <?php echo WFG_Common_Helper::translate( 'Create Criteria' ) ?>
        </p>
    </div>
    <div id="wfg_free_gift_global_settings">
        <form name="wfg_main_menu_form" method="post" action="">
            <h2></h2>
            <?php wp_nonce_field( 'wfg_criteria_settings', '_wfg_criteria_nonce' ); ?>
            <div class="_wfg-criteria-wrap">
                <div class="wfg-criteria">
                    <?php
                    $all_criteria = WFG_Settings_Helper::get( '', false, 'criteria', false );

                    $criteria = $condition = [];
                    if ( $all_criteria ) {
                        $criteria  = $all_criteria['criteria-1'];
                        $condition = $criteria['condition'];
                    }
                    ?>
                    <div class="wfg-criteria-item shadow" data-id='1'>
                        <input type="text" name="_wfg_criteria[criteria-1][name]"
                               placeholder="<?php echo WFG_Common_Helper::translate( 'Name this criteria' ) ?>"
                               required class="wfg-criteria-name wfg-input-full"
                               value="<?php echo isset( $criteria['name'] ) ? $criteria['name'] : '' ?>"/>
                        <div class="wfg-criteria-options-wrap" data-id='1'>
                            <select name="_wfg_criteria[criteria-1][condition][]" class="wfg-condition-selector">
                                <option value="num_products" <?php echo ( ! empty( $condition ) && $condition[0] == 'num_products' ) ? 'selected' : '' ?> >
                                    <?php echo WFG_Common_Helper::translate( 'Total number of item/s in cart' ) ?>
                                </option>
                                <option value="total_price" <?php echo ( ! empty( $condition ) && $condition[0] == 'total_price' ) ? 'selected' : '' ?>>
                                    <?php echo WFG_Common_Helper::translate( 'Cart total price' ) ?>
                                </option>
                            </select>
                            <select name="_wfg_criteria[criteria-1][condition][]" class="wfg-comparison">
                                <option value=">" <?php echo ( ! empty( $condition ) && $condition[1] == '>' ) ? 'selected' : '' ?>>
                                    <?php echo WFG_Common_Helper::translate( 'is greater than' ) ?>
                                </option>
                                <option value="<" <?php echo ( ! empty( $condition ) && $condition[1] == '<' ) ? 'selected' : '' ?>>
                                    <?php echo WFG_Common_Helper::translate( 'is less than' ) ?>
                                </option>
                                <option value="==" <?php echo ( ! empty( $condition ) && $condition[1] == '==' ) ? 'selected' : '' ?>>
                                    <?php echo WFG_Common_Helper::translate( 'is equal to' ) ?>
                                </option>
                                <option value="!=" <?php echo ( ! empty( $condition ) && $condition[1] == '!=' ) ? 'selected' : '' ?>>
                                    <?php echo WFG_Common_Helper::translate( 'is not equal to' ) ?>
                                </option>
                            </select>
                            <input type="text" name="_wfg_criteria[criteria-1][condition][]"
                                   value="<?php echo isset( $condition[2] ) ? $condition[2] : '' ?>"
                                   class="wfg-input-small wfg-adjust-position wfg-condition-value" required/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="options_group">
                <p>
                    <input type="hidden" name="_wfg_criteria_hidden" value="Y"/>
                    <button class="button-primary"
                            type="submit"><?php echo WFG_Common_Helper::translate( 'Save' ) ?></button>
                </p>
            </div>
        </form>
    </div>
</div>
<?php echo WFG_Common_Helper::translate( 'You can add multiple gift criteria in premium version.' ) ?>&nbsp;
<a href="<?php echo PRO_URL ?>"
   title="Buy WooCommerce Free Gift PRO"><?php echo WFG_Common_Helper::translate( 'Learn more...' ) ?></a>
