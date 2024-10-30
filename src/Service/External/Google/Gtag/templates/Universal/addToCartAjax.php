<script>
  document.addEventListener("DOMContentLoaded", function () {
    var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .ajax_add_to_cart';

    if (typeof window.xoo_wsc_params !== 'undefined') {
      mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .ajax_add_to_cart, body.single-product .single_add_to_cart_button';
    }

    jQuery(mmpSelector).on('click', function () {

      // Classical loop
      let prodElement = jQuery(this).closest('.product');

      // Gutenberg block
      if (prodElement.length === 0) {
        prodElement = jQuery(this).closest('.wc-block-grid__product');
      }

      var prodData = JSON.parse(prodElement.find('[data-metadata-product-list]').attr('data-metadata-product-list'));

        <?php if($withVat === null):?>
          const price = prodData['price'];
          <?php elseif($withVat): ?>
          const price = prodData['price_with_vat'];
            <?php else: ?>
          const price = prodData['price_without_vat'];
        <?php endif; ?>

      gtag('event', 'add_to_cart', {
        "currency": "<?php echo $currency ?>",
        "items": [
          {
            "id": prodData['full_id'],
            "name": prodData['name'],
            "category": prodData['category'],
            "quantity": 1,
            "price": price,
            "google_business_vertical": "retail"
          }
        ],
        "send_to": "<?php echo $sendTo ?>",
      });
    });
  });
</script>
