<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="//www.googletagmanager.com/gtag/js?id=<?php echo $gtagMainCode ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }

  gtag('js', new Date());

  gtag('consent', 'default', {
    'analytics_storage': '<?php echo $analyticalStorage ?>',
    'ad_storage': '<?php echo $advertisementStorage ?>',
    'ad_user_data': '<?php echo $advertisementStorage ?>',
    'ad_personalization': '<?php echo $advertisementStorage ?>',
  });

  <?php if ($gtagAnalyticsCode): ?>
    gtag('config', '<?php echo $gtagAnalyticsCode ?>');
  <?php endif; ?>

  <?php if($gtagAnalytics4Code): ?>
    gtag('config', '<?php echo $gtagAnalytics4Code ?>'<?php if(MERGADO_DEBUG): echo ", { 'debug_mode':true }"; endif; ?>);
  <?php endif; ?>

  <?php if ($gtagAnalyticsCode || $gtagAnalytics4Code): ?>
      window.mmp.cookies.sections.analytical.functions.gtagAnalytics = function () {
        gtag('consent', 'update', {
          'analytics_storage': 'granted'
        });
      };
  <?php endif; ?>

  <?php if($googleAdsData['show']): ?>
    gtag('config', '<?php echo $googleAdsData['code']?>', <?php echo json_encode($googleAdsData['props']) ?>)
  <?php endif; ?>

  <?php if ($argepConversionCode): ?>
      <?php if($cookiesAdvertisementEnabled): ?>
        gtag('config', '<?php echo $argepConversionCode ?>', {'allow_ad_personalization_signals': false});
      <?php else: ?>
          window.mmp.cookies.sections.advertisement.functions.argep = function () {
            gtag('consent', 'update', {
              'ad_storage': 'granted',
              'ad_user_data': 'granted',
              'ad_personalization': 'granted',
            });

            gtag('config', '<?php echo $argepConversionCode ?>', {'allow_ad_personalization_signals': true});
          };
      <?php endif; ?>
  <?php endif; ?>


  <?php if ($googleAdsConversionCode): ?>
      window.mmp.cookies.sections.advertisement.functions.gtagAds = function () {
        gtag('consent', 'update', {
          'ad_storage': 'granted',
          'ad_user_data': 'granted',
          'ad_personalization': 'granted',
        });

        <?php
          if ($googleAdsEnhancedConversionsActive) {
              $gAdsConsentActiveProps = ['allow_enhanced_conversions' => true, 'allow_ad_personalization_signals' => true];
          } else {
              $gAdsConsentActiveProps = ['allow_ad_personalization_signals' => true];
          }
        ?>

        gtag('config', '<?php echo $googleAdsConversionCode ?>', <?php echo json_encode($gAdsConsentActiveProps) ?>);
      };
  <?php endif; ?>
</script>
