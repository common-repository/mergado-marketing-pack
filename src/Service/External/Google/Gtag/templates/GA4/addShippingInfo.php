<script>
  mmp.ga4 = {};
  mmp.ga4.sendShipping = 0;

  document.addEventListener("DOMContentLoaded", function () {
    var $ = jQuery;

    $('body').on('click', 'input[type="radio"][name*="shipping_method"]', function () {
      mmp.ga4.sendShipping += 1;
    });

    document.body.addEventListener('mergado_shipping_updated', sendShippingInfo);
    document.body.addEventListener('mergado_updated_checkout', sendShippingInfo);

    const isCheckout = document.querySelector('body.woocommerce-checkout');

    if (isCheckout !== null) {
      mmp.ga4.sendShipping += 1;
      sendShippingInfo();
    }
  });

  function sendShippingInfo() {
    if (mmp.ga4.sendShipping !== 0) {
      const ga4_shipping_methods = <?php echo json_encode($shipping_methods) ?>;
      const input = document.querySelector('input[type="radio"][name*="shipping_method"]:checked');

      if (input) {
          let val = input.value;

          if (typeof ga4_shipping_methods[val] !== "undefined") {
            val = ga4_shipping_methods[val];
          }

          let eventObject = {...window.mmp.ga4_cart_data.cart_data};
          eventObject['coupon'] = window.mmp.ga4_cart_data.coupon;
          eventObject['shipping_tier'] = val;

          gtag('event', 'add_shipping_info', eventObject);
          mmp.ga4.sendShipping = 0;
      }
    }
  }
</script>
