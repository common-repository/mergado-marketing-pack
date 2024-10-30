<script>
  document.addEventListener('DOMContentLoaded', function () {
    jQuery( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
        var defaultEvent = <?php echo json_encode($eventObject) ?>;

        var productId = document.querySelector('.variations_form.cart').getAttribute('data-product_id');
        var variationId = variation['variation_id'];

        defaultEvent['item_id'] = (productId + '-' + variationId).toString();

        gtag('event', 'select_content', defaultEvent);
      });
  });
</script>
