<script>
  let id = '';

  if (document.getElementsByClassName('variation_id')[0] && document.getElementsByClassName('variation_id')[0].value != 0) {
    id = <?php echo $productId; ?> + '-' + document.getElementsByClassName('variation_id')[0].value;
  } else {
    id = <?php echo $productId; ?>;
  }

  fbq('trackCustom', 'ViewContent', {
    content_name: '<?php echo $productTitle; ?>',
    content_type: 'product',
    content_ids: [id]
  });
</script>
