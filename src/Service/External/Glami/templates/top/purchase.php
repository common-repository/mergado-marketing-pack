<script>
  document.addEventListener("DOMContentLoaded", function () {
    (function (f, a, s, h, i, o, n) {
      f['GlamiOrderReview'] = i;
      f[i] = f[i] || function () {(f[i].q = f[i].q || []).push(arguments);};
      o = a.createElement(s), n = a.getElementsByTagName(s)[0];
      o.async = 1; o.src = h; n.parentNode.insertBefore(o, n);
    })(window,document,'script','//www.<?php echo $domain ?>/js/compiled/or.js', 'glami_or');

    glami_or('addParameter', 'merchant_id', '<?php echo $merchantId ?>', '<?php echo $lang ?>');
    glami_or('addParameter', 'order_id', '<?php echo $orderId ?>');
    glami_or('addParameter', 'email', '<?php echo $email ?>');
    glami_or('addParameter', 'language', '<?php echo $language ?>');
    glami_or('addParameter', 'items', <?php echo $items ?>);

    glami_or('create');
  });
</script>
