/**
 * @file wfg-admin-scripts.js
 *
 * Script for WooCommerce Free Gift plugin.
 *
 * Copyright (c) 2015, Ankit Pokhrel <info@ankitpokhrel.com.np, http://ankitpokhrel.com>
 */
jQuery(document).ready(function ($) {

  //activate chosen
  var selectBox = $('.wfg-ajax-select');

  if (selectBox.length) {
    initialize_selectize(selectBox);
  }

});

/**
 * Initialize selectize js in a element.
 *
 * @param elm
 */
function initialize_selectize(elm) {
  elm.selectize({
    valueField: 'id',
    labelField: 'text',
    searchField: 'text',
    highlight: true,
    create: false,
    loadingClass: 'wfg-selectize-loading',
    load: function (query, callback) {
      if (!query.length) return callback();
      jQuery.ajax({
        url: 'admin-ajax.php',
        dataType: 'json',
        method: 'GET',
        data: {
          action: 'product_list_callback',
          q: query
        },
        success: function (res) {
          callback(res.options);
        }
      });
    }
  });
}
