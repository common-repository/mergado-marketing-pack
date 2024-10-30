<script>
  document.addEventListener("DOMContentLoaded", function () {
    fbq('track', 'InitiateCheckout', {
      content_ids: [<?php echo implode(',', $contentIds); ?>],
      contents: [<?php echo implode(',', $contents); ?>],
      content_type: '<?php echo $contentType ?>',
      value: <?php echo $value ?>,
      currency: '<?php echo $currency ?>',
      num_items: <?php echo $numItems ?>
    });
  });
</script>
