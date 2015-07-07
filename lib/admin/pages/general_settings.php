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
	<h2><?php echo WFG_Common_Helper::translate('General Settings') ?></h2>
	<form class="wfg-general-settings" method="post" action="">
		<?php wp_nonce_field('wfg_general_settings','_wfg_general_nonce'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="popup_overlay"><?php echo WFG_Common_Helper::translate('Popup Overlay') ?></label>
					</th>
					<td>
						<?php
							$checked = '';
							$overlay = WFG_Settings_Helper::get('popup_overlay', true, 'global_options');
							if( $overlay ) {
								$checked = 'checked';
							}
						?>
					  <label class="switch switch-green">
					    <input type="checkbox" class="checkbox switch-input"  name="_wfg_popup_overlay" id="popup_overlay" <?php echo $checked ?>>
					    <span class="switch-label" data-on="On" data-off="Off"></span>
					    <span class="switch-handle"></span>
					  </label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="popup_heading"><?php echo WFG_Common_Helper::translate('Popup Heading Text') ?></label>
					</th>
					<td>
						<?php
							$heading = WFG_Settings_Helper::get('popup_heading', false, 'global_options');
							if( $heading === false ) {
								$heading = WFG_Common_Helper::translate('Choose your free gift');
							}
						?>
						<input type="text" name="_wfg_popup_heading" id="popup_heading" class="regular-text" value="<?php echo $heading ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="popup_add_gift_text"><?php echo WFG_Common_Helper::translate('Add Gift Text') ?></label>
					</th>
					<td>
						<?php
							$add_gift_text = WFG_Settings_Helper::get('popup_add_gift_text', false, 'global_options');
							if( $add_gift_text === false ) {
								$add_gift_text = WFG_Common_Helper::translate('Add Gifts');
							}
						?>
						<input type="text" name="_wfg_popup_add_gift_text" id="popup_add_gift_text" class="regular-text" value="<?php echo $add_gift_text ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="popup_cancel_text"><?php echo WFG_Common_Helper::translate('Cancel Text') ?></label>
					</th>
					<td>
						<?php
							$cancel_text = WFG_Settings_Helper::get('popup_cancel_text', false, 'global_options');
							if( $cancel_text === false ) {
								$cancel_text = WFG_Common_Helper::translate('No Thanks');
							}
						?>
						<input type="text" name="_wfg_popup_cancel_text" id="popup_cancel_text" class="regular-text" value="<?php echo $cancel_text ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="invalid_condition"><?php echo WFG_Common_Helper::translate('Invalid Gift Condition Text') ?></label>
					</th>
					<td>
						<?php
							$invalidText = WFG_Settings_Helper::get('invalid_condition_text', false, 'global_options');
							if( $invalidText === false ) {
								$invalidText = WFG_Common_Helper::translate('Gift items removed as gift criteria isn\'t fulfilled');
							}
						?>
						<input type="text" name="_wfg_invalid_condition_text" id="invalid_condition" class="regular-text"
							value="<?php echo $invalidText ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="hidden" name="_wfg_general_settings_submitted" value="Y" class="button button-primary" />
			<input type="submit" value="<?php echo WFG_Common_Helper::translate('Save Changes') ?>" class="button button-primary" />
		</p>
	</form>
</div>