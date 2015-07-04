<div id="wfg_setting" class="wrap">
	<div class="header clearfix">
	<div class="left">
		<?php
			echo '<img src="' . plugins_url( 'img/wfg-logo.png', dirname(__FILE__) ) . '" > ';
		?>
	</div>
	<div class="left">
		<h1><?php echo WFG_Common_Helper::translate('Woocommerce Multiple Free Gift') ?></h1>
	</div>
	<div class="right"></div>

	</div>
	<div id="wfg_free_gift_global_settings">
		<?php echo WFG_Common_Helper::translate('This feature is only available in premium version.') ?>&nbsp;
		<a href="<?php echo PRO_URL ?>" title="Buy WooCommerce Free Gift PRO"><?php echo WFG_Common_Helper::translate('Learn more...') ?></a>
	</div>
</div>
