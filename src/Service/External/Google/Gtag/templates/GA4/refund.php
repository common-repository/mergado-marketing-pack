<script>
  document.addEventListener('DOMContentLoaded', function () {
    gtag('event', 'refund', <?php echo json_encode($eventObject) ?>);
    window['ga-disable-<?php echo $ga4id ?>'] = true;
  });
</script>
