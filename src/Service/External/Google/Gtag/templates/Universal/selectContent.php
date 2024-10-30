<script>
  document.addEventListener('DOMContentLoaded', function () {
    gtag('event', 'select_content', {
      "currency": "<?php echo $currency ?>",
      "content_type": "<?php echo $contentType ?>",
      "items": <?php echo $items ?>,
      "send_to": "<?php echo $sendTo ?>",
    });
  });
</script>
