<div style="display: none;" data-mergado-ga4-cart-data='<?php echo json_encode($eventObject['items']) ?>'></div>

<?php
unset($eventObject['items']) // Unset unnecessary data
?>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var checkit = window.check_var_ga4;

    if (checkit === undefined) {
      window.check_var_ga4 = 1;
      var mergadoProductsData = JSON.parse(jQuery('[data-mergado-ga4-cart-data]').attr('data-mergado-ga4-cart-data'));

      jQuery('body').on('click', '.product-remove a.remove', function () {
        var href = jQuery(this).attr('href');
        var eventObject = <?php echo json_encode($eventObject) ?>;

        var item = mergadoProductsData[href];

        eventObject['items'] = item;
        eventObject['value'] = (item['price'] * item['quantity']) - (item['discount'] * item['quantity']);

        gtag('event', 'remove_from_cart', eventObject);
      });
    }
  });
</script>
