<script>
  document.addEventListener("DOMContentLoaded", function () {
    dataLayer.push({
      'event' : 'addToCart',
      'ecommerce' : {
        'currencyCode': '<?php echo $currency ?>',
        'add' : {
          'products': [{
            'name': "<?php echo $name ?>",
            'id': "<?php echo $id ?>",
            'price': '<?php echo $price ?>',
            'quantity': <?php echo $quantity ?>,
            'category': "<?php echo $category ?>"
          }]
        }
      }
    })
  });
</script>
