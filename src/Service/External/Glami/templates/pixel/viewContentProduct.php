<script>
  document.addEventListener('DOMContentLoaded', function () {
    glami('track', 'ViewContent', {
      content_type: '<?php echo $contentType ?>',
      item_ids: ['<?php echo $itemIds ?>'],
      product_names: ['<?php echo $productNames ?>'],
      consent: <?php echo $consent ?>,
    });
  });
</script>
