<script>
    document.addEventListener("DOMContentLoaded", function () {
        bianoTrack('track', 'add_to_cart', {
            id: '<?php echo $id; ?>',
            quantity: <?php echo $quantity ?>,
            unit_price: <?php echo $unit_price; ?>,
            currency: '<?php echo $currency ?>',
        });
    });
</script>