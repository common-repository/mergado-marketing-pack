<script>
  window.addEventListener("load", function (event) {
    glami('track', 'Purchase', {
      item_ids: [<?php echo implode(',', $itemIds); ?>],
      product_names: [<?php echo implode(',', $productNames); ?>],
      value: <?php echo $value ?>,
      currency: '<?php echo $currency; ?>',
      transaction_id: '<?php echo $transactionId; ?>',
      consent: <?php echo $consent ?>,
    });
  });
</script>
