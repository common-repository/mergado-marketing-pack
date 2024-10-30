<script>
  function setZboziCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = '<?php echo $cookieName ?>' +"=" + cname + ";" + expires + ";path=/";
  }

  /**
   * TODO: Make it global helper script
   *
   * @param cname
   * @returns {string}
   */
  function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == " ") {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  if (getCookie('<?php echo $cookieName ?>') === "") {
    (function (w, d, s, u, n, k, c, t) {
      w.ZboziConversionObject = n;
      w[n] = w[n] || function () {
        (w[n].q = w[n].q || []).push(arguments)
      };
      w[n].key = k;
      c = d.createElement(s);
      t = d.getElementsByTagName(s)[0];
      c.async = 1;
      c.src = u;
      t.parentNode.insertBefore(c, t)
    })
    (window, document, "script", "<?php echo $scriptUrl ?>", "zbozi", <?php echo $conversionShopId ?>);

      <?php if($useSandbox): ?>
    zbozi("useSandbox");
      <?php endif; ?>

    zbozi("setOrder", {
      "orderId": <?php echo $conversionOrderId ?>,
      "consent": <?php echo $consent ?>
    });

    zbozi("send");

    setZboziCookie(<?php echo $conversionOrderId ?>, '<?php echo $cookieName ?>', 15);
  }
</script>
