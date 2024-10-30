<script>
    document.addEventListener("DOMContentLoaded", function () {
        var $ = jQuery;

        var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button';

        if (typeof window.xoo_wsc_params !== 'undefined') {
            mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button, body.single-product .single_add_to_cart_button';
        }

        $(mmpSelector).on('click', function (e) {
            var $_currency, $_id, $_price, $_priceClone;

            if (!$(this).hasClass('product_type_variable')) {
                if ($('[data-metadata-product-list]').length > 0) {
                    // Classical loop
                    let prodElement = jQuery(this).closest('.product');

                    // Gutenberg block
                    if (prodElement.length === 0) {
                      prodElement = jQuery(this).closest('.wc-block-grid__product');
                    }

                    var prodData = JSON.parse(prodElement.find('[data-metadata-product-list]').attr('data-metadata-product-list'));

                    $_currency = prodData['currency'];
                    $_id = prodData['full_id'];
                    $_price = prodData['price'];
                } else {
                    $_currency = $('#mergadoSetup').attr('data-currency');
                    $_id = $(this).closest('li.product').find('[data-product_id]').attr('data-product_id');
                    $_priceClone = $(this).closest('li.product').clone();
                    $_priceClone.find('del').remove();
                    $_priceClone.find('.woocommerce-Price-currencySymbol').remove();
                    $_price = $_priceClone.find('.woocommerce-Price-amount.amount').text().replace(' ', '');
                }

                bianoTrack('track', 'add_to_cart', {
                    id: $_id.toString(),
                    quantity: 1,
                    unit_price: Number($_price),
                    currency: $_currency,
                });
            }
        });
    });
</script>
