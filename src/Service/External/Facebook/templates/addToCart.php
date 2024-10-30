<script>
  document.addEventListener("DOMContentLoaded", function() {
    fbq('track', 'AddToCart', {
      product_name: ['<?php echo $productName; ?>'],
      content_ids: ['<?php echo $contentIds; ?>'],
      contents: [{'id':'<?php echo $id; ?>', 'quantity':'<?php echo $quantity; ?>'}],
      content_type: '<?php echo $contentType ?>',
      value: <?php echo $value; ?>,
      currency: '<?php echo $currency ?>'
    });
  });
</script>
