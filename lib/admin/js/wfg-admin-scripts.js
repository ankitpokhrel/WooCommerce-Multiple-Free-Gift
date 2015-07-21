/**
 * @file wfg-admin-scripts.js
 *
 * Script for Woocommerce Free Gift plugin.
 *
 * Copyright (c) 2015, Ankit Pokhrel <info@ankitpokhrel.com.np, http://ankitpokhrel.com.np>
 */
jQuery(document).ready(function($) {
	//Triggers when user click 'Add new gift' in single gift settings
	$(document).on('click', '.wfg_single_product_add', function() {
		var lastGift = $('.wfg-inputs:last'),
			productList = lastGift.clone(),
			removeGiftHtml = '<a class="wfg-remove-gift-product dashicons dashicons-no" href="javascript:void(0)"></a>';

		productList.find('.wfg-remove-gift-product').remove();
		productList.append(removeGiftHtml);
		lastGift.after(productList);
	});

	//Triggers when user click 'Add new gift' in main settings page
	$(document).on('click', '.wfg_product_add', function() {
		var lastGift = $(this).closest('.wfg-settings-repeater').find('.wfg-inputs:last'),
			productList = lastGift.clone();

		if (!productList.find('.wfg-remove-condition-criteria').length) {
			productList.find('select').after('<a class="wfg-remove-condition-criteria dashicons dashicons-no" href="javascript:void(0)"></a>');
		}

		lastGift.after(productList);
	});

	//Triggers when gift item is removed in single gift settings
	$('._wfg-repeat').on('click', '.wfg-remove-gift-product', function() {
		$(this).closest('.wfg-inputs').fadeOut(300, function() {
			$(this).remove();
		});
	});

	//Triggers when condition criteria is removed in main settings page
	$(document).on('click', '.wfg-remove-condition-criteria', function() {
		$(this).closest('.wfg-criteria-options-wrap').fadeOut(300, function() {
			$(this).remove();
		});
	});

});
