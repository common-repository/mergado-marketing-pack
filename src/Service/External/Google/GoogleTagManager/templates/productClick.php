<script>
  dataLayer.push({
    'event': 'productClick',
    'ecommerce': {
      'currencyCode': '<?php echo $currency ?>',
      'click': {
        'products': <?php echo $products ?>
      }
    }
  });
</script>
