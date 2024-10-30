<script>
  dataLayer.push({
    'event': 'purchase',
    'ecommerce': {
      'currencyCode' : "<?php echo $currencyCode ?>",
      'purchase': {
        'actionField': {
          'id': "<?php echo $id ?>",
          'affiliation': "<?php echo $affiliation ?>",
          'revenue': '<?php echo $revenue ?>',
          'tax': '<?php echo $tax ?>',
          'shipping': '<?php echo $shipping ?>',
          'coupon': '<?php echo $coupon ?>'
        },
        'products': <?php echo json_encode($products, JSON_NUMERIC_CHECK) ?>
      }
    }
  });
</script>
