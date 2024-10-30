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

      var eventObject = <?php echo json_encode($eventObject) ?>;

      var regularPrice = <?php if($withVat): echo 'prodData["regular_price_with_vat"]'; else: echo 'prodData["regular_price_without_vat"]'; endif ?>;
      var price = <?php if($withVat): echo 'prodData["price_with_vat"]'; else: echo 'prodData["price_without_vat"]'; endif ?>;
      var discount = <?php echo 'price !== regularPrice ? regularPrice - price : 0'; ?>;

      eventObject['value'] = price;

      var item = {
        "item_id": prodData['full_id'].toString(),
        "item_name": prodData['name'],
        "item_category": prodData['category'],
        "quantity": 1,
        "discount": discount,
        "price": price,
      };

      if (prodData['categories_json'].length > 0) {
        prodData['categories_json'].forEach((item, index) => {
          var categoryKey = 'item_category';

          if (index !== 0) {
            categoryKey = 'item_category' + index;
          }

          item[categoryKey] = item;
        });
      }

      eventObject['items'] = [item];

      gtag('event', 'add_to_cart', eventObject);
    });
  });
</script>
