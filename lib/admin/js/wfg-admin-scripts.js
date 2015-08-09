/**
 * @file wfg-admin-scripts.js
 *
 * Script for Woocommerce Free Gift plugin.
 *
 * Copyright (c) 2015, Ankit Pokhrel <info@ankitpokhrel.com.np, http://ankitpokhrel.com.np>
 */
jQuery(document).ready(function($) {

	if ($('.chosen').length) {
		$('.chosen').ajaxChosen({
			dataType: 'json',
			type: 'POST',
			url: 'admin-ajax.php',
			data: { action: 'product_list_callback' }
		},{
			loadingImg: WMFG_SPECIFIC.loading_url,
			minLength: 3
		});
	}

});
