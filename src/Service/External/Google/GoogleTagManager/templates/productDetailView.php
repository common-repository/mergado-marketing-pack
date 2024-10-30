<script>
  dataLayer.push({
    'event': 'viewItem',
    'ecommerce': {
      'currencyCode': '<?php echo $currency ?>',
      'detail': {
        'products': <?php echo $products ?>
      }
    }
  });
</script>
