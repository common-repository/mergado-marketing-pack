(function ($) {
  'use strict';

  $(function() {
    // Tab control
    $('.mmp-tabs__menu li').on('click', function(e) {
      e.preventDefault();
      $('.mmp-tabs li.active').removeClass('active');
      $('.mmp-tabs__tab.active').removeClass('active');
      $(this).addClass('active');
      $('[data-mmp-tab="' + $(this).children('a').attr('data-mmp-tab-button') + '"]').addClass('active');
    });

    $('[data-mmp-tab-button]').on('click', function () {
      var urlParams = new URLSearchParams(window.location.search);
      urlParams.set('mmp-tab', $(this).attr('data-mmp-tab-button'));
      window.history.pushState('', '',  'admin.php?' + urlParams);
    });

    if (window.location.href.indexOf("page=mergado-cookies") > -1 && window.location.href.indexOf("mmp-tab=") <= -1) {
      jQuery('[data-mmp-tab-button="cookies"]').click();
    }
  });

  // Set active items on startup
  recalculateActiveCount();

  // Set active items on change
  $('input[type="checkbox"][data-mmp-activity-check-checkbox]').click(() => {
    recalculateActiveCount();
  });

  function recalculateActiveCount() {
    const tabs = $('[data-mmp-tab-button]');

    tabs.each(function () {
      const attributeName = $(this).attr('data-mmp-tab-button');
      const tabContentElement = $('[data-mmp-tab=' + attributeName + ']');

      const checkedCount = tabContentElement.find('input[type="checkbox"][data-mmp-activity-check-checkbox]:checked').length;

      const countElement = $(this).find('.mmp-tabs__active-count');

      if (checkedCount === 0) {
        countElement.attr('data-count-active', 'false');
        countElement.html('');
      } else {
        // Activate
        countElement.attr('data-count-active', 'true');
        countElement.html(checkedCount);
      }
    });
  }
})(jQuery)
