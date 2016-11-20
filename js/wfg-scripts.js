/**
 * @file wfg-scripts.js
 *
 * Frontend core script for WooCommerce free gift plugin.
 *
 * Copyright (c) 2015, Ankit Pokhrel <info@ankitpokhrel.com.np, http://ankitpokhrel.com>
 */
jQuery(document).ready(function ($) {
  if($('.wfg-popup, .wfg-overlay').length) {
    setTimeout(function () {
      $('.wfg-popup, .wfg-overlay').fadeIn(1300);
    }, 700);

    $('.wfg-no-thanks').click(function (e) {
      e.preventDefault();
      $('.wfg-popup, .wfg-overlay').fadeOut(500, function () {
        $(this).remove();
      });
    });

    $('.wfg-add-gifts').click(function (e) {
      e.preventDefault();
      var form = $(this).closest('form');
      $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        success: function (response) {
          window.location.reload();
        }
      });
    });

    var wfgCheckboxes = $('.wfg-checkbox');
    wfgCheckboxes.click(function () {
      if(WFG_SPECIFIC.gifts_allowed <= 0) {
        return;
      }

      if($('.wfg-checkbox:checked').length >= WFG_SPECIFIC.gifts_allowed) {
        wfgCheckboxes.not('.wfg-checkbox:checked').attr('disabled', 'disabled').parent().addClass("opaque");
      }
      else {
        wfgCheckboxes.removeAttr('disabled').parent().removeClass("opaque");
      }
    })
  }

  $('.wfg-fixed-notice-remove').click(function () {
    $(this).closest('.wfg-fixed-notice').fadeOut(1000);
  });
});

/* use as handler for resize*/
jQuery(window).resize(wfgAdjustLayout);
/* call function in ready handler*/
jQuery(document).ready(function () {
  wfgAdjustLayout();
  /* Resize ma adjust garnay cod sabai yesma haalnay*/
})

function wfgAdjustLayout() {
  jQuery('.wfg-popup').css({
    position: 'fixed',
    left: (jQuery(window).width() - jQuery('.wfg-popup').outerWidth()) / 2,
    top: (jQuery(window).height() - jQuery('.wfg-popup').outerHeight()) / 2
  });

}
