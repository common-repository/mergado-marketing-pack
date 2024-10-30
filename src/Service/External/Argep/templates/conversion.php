<div id="adwordsConversions">
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        gtag('event', 'conversion', {
          "value": <?php echo $value ?>,
          "currency": "<?php echo $currency ?>",
          "transaction_id": "<?php echo $transactionId ?>",
          "send_to": "<?php echo $sendTo ?>",
        });
      });
    </script>
</div>
