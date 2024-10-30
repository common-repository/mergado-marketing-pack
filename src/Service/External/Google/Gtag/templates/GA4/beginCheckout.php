<script>
  document.addEventListener('DOMContentLoaded', function () {
    var eventObject = {...window.mmp.ga4_cart_data.cart_data};
    eventObject['coupon'] = window.mmp.ga4_cart_data.coupon;

    gtag('event', 'begin_checkout', eventObject);
  });
</script>
