<script>
  fbq('trackCustom', 'ViewCategory', {
    content_name: '<?php echo $categoryName; ?>',
    content_type: '<?php echo $contentType; ?>',
    content_ids: [<?php echo (isset($productIds) ? implode(',', $productIds) : '') ?>]
  });
</script>
