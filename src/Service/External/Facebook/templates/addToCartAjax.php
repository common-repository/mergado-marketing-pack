<script>
  document.addEventListener("DOMContentLoaded", function() {
    var $ = jQuery;

    var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button';

    if (typeof window.xoo_wsc_params !== 'undefined') {
      mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button, body.single-product .single_add_to_cart_button';
    }

    $(mmpSelector).on('click', function(e) {
      if(!$(this).hasClass('product_type_variable')) {
        if($('[data-metadata-product-list]').length > 0) {
          // Classical loop
          let prodElement = jQuery(this).closest('.product');

          // Gutenberg block
          if (prodElement.length === 0) {
            prodElement = jQuery(this).closest('.wc-block-grid__product');
          }

          var prodData = JSON.parse(prodElement.find('[data-metadata-product-list]').attr('data-metadata-product-list'));

          var $_currency = prodData['currency'];
          var $_id = prodData['full_id'];
          var $_name = prodData['name'];
          var $_price = prodData['price'];
          var $_qty = 1;
        } else {
          var $_currency = $('#mergadoSetup').attr('data-currency');
          var $_id = $(this).closest('li.product').find('[data-product_id]').attr('data-product_id');
          var $_name = $(this).closest('li.product').find('.woocommerce-loop-product__title').text();
          var $_qty = $(this).closest('li.product').find('[name="quantity"]').val();
          var $_priceClone = $(this).closest('li.product').clone();
          $_priceClone.find('del').remove();
          $_priceClone.find('.woocommerce-Price-currencySymbol').remove();
          var $_price = $_priceClone.find('.woocommerce-Price-amount.amount').text();
        }

        fbq('track', 'AddToCart', {
          content_name: $_name,
          // content_category: 'Apparel & Accessories > Shoes',
          content_ids: [$_id],
          contents: [{'id':$_id, 'quantity':$_qty}],
          content_type: 'product',
          value: Number($_price.toString().replace(' ', '')),
          currency: $_currency
        });
      }
    });
  });
</script>
