<?php
use Mergado\Service\CookieService;

$cookieService = CookieService::getInstance();

?>

<!-- BEGIN GCR Opt-in Module Code -->

<?php if ($cookieService->functionalEnabled()): ?>
    <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
<?php else: ?>
    <script>
      window.mmp.cookies.sections.functional.functions.googleReviewsOptIn = function () {
        $('body').append('<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer><\/script>');
      };
    </script>
<?php endif; ?>


<script>
  window.renderOptIn = function () {
    window.gapi.load('surveyoptin', function () {
      window.gapi.surveyoptin.render(
          {
            "merchant_id": "<?php echo $MERCHANT_ID?>",
            "order_id": "<?php echo $ORDER['ID']?>",
            "email": "<?php echo $ORDER['CUSTOMER_EMAIL']?>",
            "delivery_country": "<?php echo $ORDER['COUNTRY_CODE']?>",
            "estimated_delivery_date": "<?php echo $ORDER['ESTIMATED_DELIVERY_DATE']?>",
              <?php if($ORDER['PRODUCTS']): ?>
            "products": <?php echo $ORDER['PRODUCTS'] ?>,
              <?php endif ?>
            "opt_in_style": "<?php echo $POSITION ?>"
          });
    });
  }
</script>
<!-- END GCR Opt-in Module Code -->

<!-- BEGIN GCR Language Code -->
<?php if ($LANGUAGE !== 'automatically'): ?>
    <script>
      window.___gcfg = {
        lang: "<?php echo $LANGUAGE ?>"
      };
    </script>
<?php endif ?>
<!-- END GCR Language Code -->
