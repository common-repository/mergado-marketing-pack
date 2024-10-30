<script>
  dataLayer.push({
    'event': 'checkout',
    'ecommerce': {
      'currencyCode': '<?php echo $currency ?>',
      'checkout': {
        'actionField': {'step': <?php echo $step ?>},
        'products': <?php echo $products ?>
      }
    }
  });
</script>
