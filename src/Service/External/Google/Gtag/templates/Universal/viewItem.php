<script>
  document.addEventListener('DOMContentLoaded', function () {
    gtag('event', 'view_item', {
      "currency": "<?php echo $currency ?>",
      "items": <?php echo $items ?>,
      "send_to": "<?php echo $sendTo ?>",
    });
  });
</script>
