<script>
  dataLayer.push({
    'transactionId': "<?php echo $transactionId ?>",
    'transactionAffiliation': "<?php echo $transactionAffiliation ?>",
    'transactionTotal': <?php echo $transactionTotal ?>,
    'transactionTax': <?php echo $transactionTax ?>,
    'transactionShipping': <?php echo $transactionShipping ?>,
    'transactionProducts': <?php echo json_encode($transactionProducts, JSON_NUMERIC_CHECK) ?>
  });
</script>
