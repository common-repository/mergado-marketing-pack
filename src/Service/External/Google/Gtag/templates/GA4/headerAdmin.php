<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="//www.googletagmanager.com/gtag/js?id=<?php echo $gtag4Code ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }

  gtag('js', new Date());
  gtag('config', '<?php echo $gtag4Code ?>'<?php if(MERGADO_DEBUG): echo ", { 'debug_mode':true }"; endif; ?>);
</script>
