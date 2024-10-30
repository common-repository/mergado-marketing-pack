<!-- Měřící kód Sklik.cz -->
<script>
  var sklikConversionConf = {
    id: <?php echo $conversionId ?>, /* Sklik conversion identifier*/
    value: <?php echo $conversionValue ?>, /* Order value in CZK*/
    consent: <?php echo $consent ?>, /* Consent from the visitor to send a conversion hit, allowed values: 0 (no consent) or 1 (yes consent)*/
  };

  // Make sure the method exists before calling it
  if (window.rc && window.rc.conversionHit) {
    window.rc.conversionHit(sklikConversionConf);
  }
</script>
