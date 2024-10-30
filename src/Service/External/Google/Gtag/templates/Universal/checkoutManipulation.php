<script>
  document.addEventListener("DOMContentLoaded", function () {
    var $ = jQuery;

    //Coupons
    //var appliedCouponsCart = '';
    //var discounts = [];
    //
    //$('[data-coupon]').each(function () {
    //  discounts.push($(this).attr('data-coupon'));
    //});
    //
    //appliedCouponsCart = discounts.join(', ');
    //
    //$('body').on('updated_cart_totals, updated_checkout', function () {
    //  var discounts = [];
    //  $('[data-coupon]').each(function () {
    //    discounts.push($(this).attr('data-coupon'));
    //  });
    //
    //  discounts = discounts.join(', ');
    //
    //  if (appliedCouponsCart !== discounts) {
    //    appliedCouponsCart = discounts;
    //    var cartData = JSON.parse($('[data-mergado-cart-data]').attr('data-mergado-cart-data'));
    //
    //    var items = [];
    //
    //    $.each(cartData, function (key, val) {
    //      items.push(val);
    //    });
    //
    //    gtag('event', 'checkout_progress', {
    //      "items": items,
    //      "coupon": discounts,
    //      "send_to": "<?php //echo $sendTo ?>//",
    //    });
    //  }
    //});

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

      gtag('event', 'set_checkout_option', {
        "checkout_step": step,
        "checkout_option": "payment method",
        "value": val,
        "send_to": "<?php echo $sendTo ?>",
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

      gtag('event', 'set_checkout_option', {
        "checkout_step": step,
        "checkout_option": "shipping_method",
        "value": val,
        "send_to": "<?php echo $sendTo ?>",
      });
    });
  });
</script>
