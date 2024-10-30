<script>
  document.addEventListener('DOMContentLoaded', function () {
    var $ = jQuery;
    var list_name = '<?php echo $listName ?>';

    if ($('[data-metadata-product-list]').length > 0) {
      var items = {};

      $.each($('[data-metadata-product-list]'), function (key, value) {
        var values = JSON.parse($(value).attr('data-metadata-product-list'));

        <?php if($withVat === null):?>
          const price = values['price'];
        <?php elseif($withVat): ?>
          const price = values['price_with_vat'];
        <?php else: ?>
          const price = values['price_without_vat'];
        <?php endif; ?>

        items[key] = {};
        items[key]['id'] = values['full_id'];
        items[key]['name'] = values['name'];
        items[key]['list_name'] = list_name;
        items[key]['category'] = values['category'];
        items[key]['list_position'] = key;
        items[key]['price'] = price;
        items[key]['google_business_vertical'] = 'retail';
        // items[]['brand'] = values[''];
        // items[]['variant'] = values[''];
        // items[]['quantity'] = values[''];
      });

      gtag('event', 'view_item_list', {
        "currency": "<?php echo $currency ?>",
        "items": items,
        "send_to": "<?php echo $sendTo ?>",
      });
    }
  });
</script>
