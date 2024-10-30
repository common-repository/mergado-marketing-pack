<div id="adwordsConversions">
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        <?php if($enhancedConversionsActive): ?>
            gtag('set', 'user_data', {
              "email": "<?php echo $userEmail ?>",

              <?php if($userPhone): ?>
                  "phone_number": "<?php echo $userPhone ?>",
              <?php endif; ?>
            });
        <?php endif; ?>

        gtag('event', 'conversion', {
          "value": <?php echo $value ?>,
          "currency": "<?php echo $currency ?>",
          "transaction_id": "<?php echo $transactionId ?>",
          "send_to": "<?php echo $sendTo ?>",
        });
      });
    </script>
</div>
