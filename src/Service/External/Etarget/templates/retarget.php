<script>
    if (window.addEventListener) {
        window.addEventListener("load", loadRetarget<?php echo $id; ?>);
    } else if (window.attachEvent) {
        window.attachEvent("onload", loadRetarget<?php echo $id; ?>);
    }

    function loadRetarget<?php echo $id; ?> () {
        var scr = document.createElement("script");
        scr.setAttribute("async", "true");
        scr.src = "//" + "cz.search.etargetnet.com/j/?h=<?php echo $hash ?>";
        ((document.getElementsByTagName("head") || [null])[0] || document.getElementsByTagName("script")[0].parentNode).appendChild(scr);
    }
</script>
