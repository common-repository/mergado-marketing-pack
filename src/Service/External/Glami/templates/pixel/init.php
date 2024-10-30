<script>
  <?php if(MERGADO_DEBUG): ?>
      window.localStorage.setItem('glamiPixelDebug', 1);
  <?php endif; ?>

  (function ($) {
    'use strict';

    (function (f, a, s, h, i, o, n) {
      f['GlamiTrackerObject'] = i;
      f[i] = f[i] || function () {
        (f[i].q = f[i].q || []).push(arguments)
      };
      o = a.createElement(s),
          n = a.getElementsByTagName(s)[0];
      o.async = 1;
      o.src = h;
      n.parentNode.insertBefore(o, n)
    })(window, document, 'script', '//www.glami.cz/js/compiled/pt.js', 'glami');

    document.addEventListener("DOMContentLoaded", function () {
      glami('create',
          '<?php echo $glamiCode ?>',
          '<?php echo $lang ?>',
          {
            consent: <?php echo $consent ?>,
          }
      );
      glami('track',
          'PageView',
          {
            consent: <?php echo $consent ?>,
          }
      );
    });
  })(jQuery);
</script>
