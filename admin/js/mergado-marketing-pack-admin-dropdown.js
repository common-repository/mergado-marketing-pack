document.addEventListener('DOMContentLoaded', function () {
  var $ = jQuery;
  $('.mmp_advancedSettingsBox__header').on('click', function () {
    $(this).parent().toggleClass('active');
  });
});