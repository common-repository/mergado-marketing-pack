<script>
  document.addEventListener("DOMContentLoaded", function() {
    glami('track', 'AddToCart', {
      item_ids: ['<?php echo $itemIds ?>'],
      product_names: ['<?php echo $productNames ?>'],
      value: <?php echo $value ?>,
      currency: '<?php echo $currency ?>',
      consent: <?php echo $consent ?>,
    });
  });
</script>
