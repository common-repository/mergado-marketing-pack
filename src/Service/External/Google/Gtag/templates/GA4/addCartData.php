<script>
  window.mmp.ga4_cart_data = {'cart_data': <?php echo json_encode($cartData) ?>, 'coupon': '<?php echo $coupon ?>'};

  const shipping_updated_event = new CustomEvent('mergado_shipping_updated');
  const payment_method_selected_event = new CustomEvent('mergado_payment_method_selected');
  const updated_checkout_event = new CustomEvent('mergado_updated_checkout');

  jQuery( document.body ).on( 'wc_fragments_refreshed updated_checkout updated_shipping_method payment_method_selected', function(e) {
    jQuery.ajax({
      type: "POST",
      url: window.woocommerce_params.ajax_url,
      data: {
        action: 'get_ga4_cart_data',
      },
      success: function (data) {
        if (!data.error) {
          window.mmp.ga4_cart_data = data.data;

          switch (e.type) {
            case "updated_shipping_method":
              document.body.dispatchEvent(shipping_updated_event);
              break;
            case "updated_checkout":
              document.body.dispatchEvent(shipping_updated_event);
              break;
            case "payment_method_selected":
              document.body.dispatchEvent(payment_method_selected_event);
              break;
          }
        }
      },
    });
  });
</script>
