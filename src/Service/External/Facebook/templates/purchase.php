<script>
  document.addEventListener("DOMContentLoaded", function () {
    fbq('track', 'Purchase', {
      content_ids: [<?php echo implode(',', $contentIds); ?>],
      contents: [<?php echo implode(',', $contents); ?>],
      content_type: '<?php echo $contentType ?>',
      value: <?php echo $value ?>,
      currency: '<?php echo $currency ?>'
    });
  });
</script>
