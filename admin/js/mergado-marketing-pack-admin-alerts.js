(function ($) {
  'use strict';

   document.addEventListener('DOMContentLoaded', function () {
    var $ = jQuery;

    $('[data-mmp-hide-alert]').on('click', function () {
      var alert = $(this).closest('.mmp_alert__wrapper');
      var alertData = JSON.parse(alert.attr('data-mmp-alert'));

      alert.hide();

      $.ajax({
        type: "POST",
        url: 'admin-ajax.php',
        data: {
          action: 'ajax_disable_alert',
          name: alertData.name,
          feed: alertData.feed,
          token: alertData.token,
        },
        success: function () {
        },
      });
    });

    $('[data-mmp-disable-all-notifications]').on('click', function () {
      var alert = $(this).closest('.mmp_alert__wrapper');
      var alertData = JSON.parse(alert.attr('data-mmp-alert'));

      $('.mmp_alert__wrapper').hide();

      $.ajax({
        type: "POST",
        url: 'admin-ajax.php',
        data: {
          action: 'ajax_disable_section',
          section: alertData.section,
          token: alertData.token,
        },
        success: function () {
        },
      });
    });
  });
})(jQuery);