<div style="display: none;" data-mergado-gtm-cart-data='<?php echo json_encode($products, JSON_NUMERIC_CHECK) ?>'></div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var mergadoProductsData = JSON.parse(jQuery('[data-mergado-gtm-cart-data]').attr('data-mergado-gtm-cart-data'));

    jQuery('body').on('click', '.product-remove a.remove',  function () {
      var href = jQuery(this).attr('href');

      dataLayer.push({
        'event': 'removeFromCart',
        'ecommerce': {
          'currencyCode': '<?php echo $currency ?>',
          'remove': {
            'products': mergadoProductsData[href]
          }
        }
      });
    });
  });
</script>
