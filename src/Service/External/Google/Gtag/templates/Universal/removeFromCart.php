<div style="display: none;"
     data-mergado-cart-data='<?php echo htmlspecialchars(json_encode($products, JSON_NUMERIC_CHECK), ENT_QUOTES) ?>'></div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var checkit = window.check_var;

    if (checkit === undefined) {
      window.check_var = 1;
      var mergadoProductsData = JSON.parse(jQuery('[data-mergado-cart-data]').attr('data-mergado-cart-data'));

      jQuery('body').on('click', '.product-remove a.remove', function () {
        var href = jQuery(this).attr('href');

        gtag('event', 'remove_from_cart', {
          "currency": "<?php echo $currency ?>",
          "items": mergadoProductsData[href],
          "send_to": "<?php echo $sendTo ?>"
        });
      });
    }
  });
</script>
