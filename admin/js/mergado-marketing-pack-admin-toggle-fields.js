(function ($) {
  'use strict';

  $(function () {
    // Enable/disable fields on adsys page
    toggleFields();

    $('[data-mmp-check-main]').on('click', function () {
      toggleFields();
    });

    function toggleFields() {
      $('[data-mmp-check-main]:not([data-mmp-check-field])').each(function (i, item) {
        checkInnerFields(item);
      });
    }

    function isChecked(element) {
      if ($(element).is(':checked')) {
        return true;
      } else {
        return false;
      }
    }

    function checkInnerFields(item, parentStatus = null) {
      const $item = $(item);
      const mainItem = $item.attr('data-mmp-check-main');
      const status = isChecked(item);

      if(parentStatus !== null) {
        if (parentStatus) {
          $item.prop("disabled", false)
        } else {
          $item.prop("disabled", true)
        }
      }

      if (parentStatus === false) {
        var statusForChildren = false;
      } else {
        var statusForChildren = status;
      }

      const fields = $('[data-mmp-check-field="' + mainItem + '"]');

      if(fields.length > 0) {
        fields.each(function (i, item) {
          checkInnerFields(item, statusForChildren);
        });
      }
    }
  });
})(jQuery)
