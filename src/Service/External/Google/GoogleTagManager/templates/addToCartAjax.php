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

      dataLayer.push({
        'event' : 'addToCart',
        'ecommerce' : {
          'currencyCode': jQuery('#mergadoSetup').attr('data-currency'),
          'add' : {
            'products': [{
              'name': prodData['name'],
              'id': prodData['full_id'],
              'price': prodData['price'],
              'quantity': 1,
              'category': prodData['category']
            }]
          }
        }
      })
    });
  });
</script>
