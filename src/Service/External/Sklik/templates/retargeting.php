<script>
  var sklikRetargetingConf = {
    rtgId: <?php echo $retargetingId ?>,
    consent: <?php echo $consent ?>,
  };

  if (window.rc && window.rc.retargetingHit) {
    window.rc.retargetingHit(sklikRetargetingConf);
  }
</script>

<!-- Update consent on accept -->
<script>
  window.mmp.cookies.sections.advertisement.functions.sklikRetargeting = function () {
    sklikRetargetingConf.consent = 1;
  };
</script>
