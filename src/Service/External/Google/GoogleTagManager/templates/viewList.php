<script>
  document.addEventListener('DOMContentLoaded', function () {
    var $ = jQuery;
    var list_name = '<?php echo $listName ?>';

    if($('[data-metadata-product-list]').length > 0) {
      var items = {};
      var currency = '';
      var viewListCount = <?php echo $viewListItemsCount ?>;

      $.each($('[data-metadata-product-list]'), function (key, value) {
        var values = JSON.parse($(value).attr('data-metadata-product-list'));
        currency = values['currency'];

        items[key] = {};
        items[key]['id'] = values['full_id'];
        items[key]['name'] = values['name'];
        items[key]['list'] = list_name;
        items[key]['category'] = values['category'];
        items[key]['position'] = key;
        items[key]['price'] = values['price'];
        // items[]['brand'] = values[''];
        // items[]['variant'] = values[''];
        // items[]['quantity'] = values[''];

        // MAGIC! If null is set in viewListCount, it will fail everytime .. haha
        if ((key + 1) === viewListCount) {
          return false;
        }
      });

      dataLayer.push({
        'event': 'view_item_list',
        'ecommerce': {
          'currencyCode': '<?php echo $currency ?>',
          'impressions': items
        }
      });
    }
  });
</script>
