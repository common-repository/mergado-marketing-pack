<?php
use Mergado\Service\CookieService;

$cookieService = CookieService::getInstance();

if ($IS_INLINE): ?>
    <?php if ($cookieService->functionalEnabled()): ?>
        <script src="https://apis.google.com/js/platform.js" async defer></script>
    <?php else: ?>
        <script>
          window.mmp.cookies.sections.functional.functions.googleReviewsBadge = function () {
            jQuery('body').append('<script src="https://apis.google.com/js/platform.js" async defer><\/script>');
          };
        </script>
    <?php endif; ?>
<?php else: ?>
    <!-- BEGIN GCR Badge Code -->
    <?php if ($cookieService->functionalEnabled()): ?>
        <script src="https://apis.google.com/js/platform.js?onload=renderBadge"
                async defer>
        </script>
    <?php else: ?>
        <script>
          window.mmp.cookies.sections.functional.functions.googleReviewsBadge = function () {
            jQuery('body').append('<script src="https://apis.google.com/js/platform.js?onload=renderBadge" async defer <\/script>');
          };
        </script>
    <?php endif; ?>

    <script>
      window.renderBadge = function () {
        var ratingBadgeContainer = document.createElement("div");
        document.body.appendChild(ratingBadgeContainer);
        window.gapi.load('ratingbadge', function () {
          window.gapi.ratingbadge.render(
              ratingBadgeContainer, {
                "merchant_id": <?php echo $MERCHANT_ID ?>,
                "position": "<?php echo $POSITION ?>"
              });
        });
      }
    </script>
    <!-- END GCR Badge Code -->
<?php endif ?>

<?php if ($LANGUAGE !== 'automatically'): ?>
    <!-- BEGIN GCR Language Code -->
    <script>
      window.___gcfg = {
        lang: "<?php echo $LANGUAGE ?>"
      };
    </script>
    <!-- END GCR Language Code -->
<?php endif ?>
