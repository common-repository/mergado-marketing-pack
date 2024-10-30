<script>
  fbq('track', 'Search', {
    search_string: '<?php echo $searchQuery ?>',
    content_ids: [<?php echo (isset($productIds) ? implode(',', $productIds) : '') ?>],
    content_type: '<?php echo $contentType;?>',
  });
</script>
