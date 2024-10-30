<script>
  document.addEventListener("DOMContentLoaded", function() {
    var $ = jQuery;

    //Payment
    $('body').on('payment_method_selected', function () {
      // $('body').on('click', 'input[type="radio"][name="payment_method"]', function () {
      var step;
      var val = $('input[type="radio"][name="payment_method"]:checked').val();

      if ($('body').hasClass('woocommerce-checkout')) {
        step = 1;
      } else {
        step = 0;
      }

      dataLayer.push({
        'event': 'checkoutOption',
        'ecommerce': {
          'checkout_option': {
            'actionField': {'step': step, 'option': val}
          }
        }
      });
    });

    //Delivery
    // $('body').on('updated_shipping_method', function () {
    $('body').on('click', 'input[type="radio"][name*="shipping_method"]', function () {
      var step;
      var val = $('input[type="radio"][name*="shipping_method"]:checked').val();

      if ($('body').hasClass('woocommerce-checkout')) {
        step = 1;
      } else {
        step = 0;
      }

      dataLayer.push({
        'event': 'checkoutOption',
        'ecommerce': {
          'checkout_option': {
            'actionField': {'step': step, 'option': val}
          }
        }
      });
    });
  });
</script>
