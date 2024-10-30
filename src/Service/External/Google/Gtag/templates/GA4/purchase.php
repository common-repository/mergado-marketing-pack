<script>
  document.addEventListener('DOMContentLoaded', function () {
    gtag('event', 'purchase', <?php echo json_encode($eventObject) ?>);
  });
</script>
