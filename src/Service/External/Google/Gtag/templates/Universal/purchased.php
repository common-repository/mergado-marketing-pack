<script>
  document.addEventListener('DOMContentLoaded', function () {
    gtag('event', 'purchase', {
      "transaction_id": "<?php echo $transactionId ?>",
      "affiliation": "<?php echo $affiliation ?>",
      "value": <?php echo $value ?>,
      "currency": "<?php echo $currency ?>",
      "tax": <?php echo $tax ?>,
      "shipping": <?php echo $shipping ?>,
      "items": <?php echo $items ?>,
      "google_business_vertical": "<?php echo $googleBusinessVertical ?>",
      "send_to": "<?php echo $sendTo ?>",
    });
  });
</script>
