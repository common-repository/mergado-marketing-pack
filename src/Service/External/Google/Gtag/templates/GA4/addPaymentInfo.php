<script>
  document.addEventListener("DOMContentLoaded", function () {
    var $ = jQuery;

    const ga4_payment_methods = <?php echo json_encode($payment_methods) ?>;

    document.body.addEventListener('mergado_payment_method_selected', () => {
      let val = $('input[type="radio"][name="payment_method"]:checked').val();

      if (typeof ga4_payment_methods[val] !== "undefined") {
        val = ga4_payment_methods[val];
      }

      let eventObject = {...window.mmp.ga4_cart_data.cart_data};
      eventObject['coupon'] = window.mmp.ga4_cart_data.coupon;
      eventObject['payment_type'] = val;

      gtag('event', 'add_payment_info', eventObject);
    });
  });
</script>
