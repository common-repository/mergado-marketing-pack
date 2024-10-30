<script>
  document.addEventListener('DOMContentLoaded', function () {
    glami('track', 'ViewContent', {
      content_type: '<?php echo $contentType ?>',
      item_ids: [<?php echo $itemIds ?>],
      product_names: [<?php echo $productNames ?>],
      category_id: '<?php echo $categoryId ?>',
      category_text: '<?php echo $categoryText; ?>',
      consent: <?php echo $consent ?>,
    });
  });
</script>
