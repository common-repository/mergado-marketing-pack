<script>
  document.addEventListener('DOMContentLoaded', function () {
    gtag('event', 'begin_checkout', {
      "currency": "<?php echo $currency ?>",
      "checkout_step": <?php echo $checkoutStep ?>,
      "items": <?php echo $items ?>,
      "coupon": "<?php echo $coupon ?>",
      "send_to": "<?php echo $sendTo ?>",
    });
  });
</script>
