<script>
  document.addEventListener('DOMContentLoaded', function () {
    var $ = jQuery;

    if ($('[data-metadata-product-list]').length > 0) {
      var items = {};

      var eventObject = <?php echo json_encode($eventObject) ?>;

    eventObject['items'] = [];
      $.each($('[data-metadata-product-list]'), function (key, value) {
        var values = JSON.parse($(value).attr('data-metadata-product-list'));

        eventObject['items'][key] = {
          'item_id': values['full_id'].toString(),
          'item_name': values['name'],
          'index': key,
        };

        if (values['categories_json'].length > 0) {
          values['categories_json'].forEach((item, index) => {
            var categoryKey = 'item_category';

            if (index !== 0) {
              categoryKey = 'item_category' + index;
            }

            eventObject['items'][key][categoryKey] = item;
          });
        }

        if (values['price'] && !values['full_id'].toString().includes('-')) {
          eventObject['items'][key]['price'] = <?php if($withVat): echo 'values["regular_price_with_vat"]'; else: echo 'values["regular_price_without_vat"]'; endif; ?>;
          eventObject['items'][key]['currency'] = '<?php echo $currency ?>';
        }
      });

      gtag('event', 'view_item_list', eventObject);
    }
  });
</script>
