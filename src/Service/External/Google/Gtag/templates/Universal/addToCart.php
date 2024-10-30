<script>
  document.addEventListener("DOMContentLoaded", function () {
    gtag('event', 'add_to_cart', {
      "currency": "<?php echo $currency ?>",
      "items": [
        {
          "id": "<?php echo $itemsId ?>",
          "name": "<?php echo $itemsName ?>",
          "category": "<?php echo $itemsCategory ?>",
          "quantity": <?php echo $itemsQuantity ?>,
          "price": "<?php echo $itemsPrice ?>",
          "google_business_vertical": "<?php echo $itemsGoogleBusinessVertical ?>"
        }
      ],
      "send_to": "<?php echo $sendTo ?>",
    });
  });
</script>
