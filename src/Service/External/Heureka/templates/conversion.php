<script>
  var _hrq = _hrq || [];
  _hrq.push(['setKey', '<?php echo $code ?>']);
  _hrq.push(['setOrderId', <?php echo $orderId ?>]);

  <?php foreach($products as $product): ?>
    _hrq.push(['addProduct', '<?php echo $product['name'] ?>', '<?php echo $product['unitPrice'] ?>', '<?php echo $product['qty'] ?>', '<?php echo $product['id'] ?>']);
  <?php endforeach; ?>

  <?php if ($lang === 'cz'):?>
    var src = 'https://im9.cz/js/ext/1-roi-async.js';
  <?php else: ?>
    var src = 'https://im9.cz/<?php echo $lang ?>/js/ext/2-roi-async.js';
  <?php endif; ?>

  _hrq.push(['trackOrder']);
  (function () {
        var ho = document.createElement('script');
        ho.async = true;
        ho.src = src;
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ho, s);
      }
  )();
</script>
