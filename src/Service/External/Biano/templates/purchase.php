<script>
    bianoTrack('track', 'purchase', {
        id: '<?php echo $orderId; ?>',
        order_price: <?php echo $order_price; ?>,
        currency: '<?php echo $currency; ?>',
        items: <?php echo $items; ?>,
        <?php if ($bianoStarShouldBeSent): ?>
            customer_email: '<?php echo $email; ?>',
            shipping_date: '<?php echo $shippingDate; ?>'
        <?php endif; ?>
    });
</script>
