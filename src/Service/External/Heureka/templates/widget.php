<?php if ($functionalCookiesEnabled): ?>
    <script>
        window.heureka_widget_active = true;

        //<![CDATA[
        var _hwq = _hwq || [];
        _hwq.push(['setKey', '<?php echo $widgetId ?>']);
        _hwq.push(['setTopPos', '<?php echo $marginTop ?>']);
        _hwq.push(['showWidget', '<?php echo $position ?>']);
        (function () {
          var ho = document.createElement('script');
          ho.async = true;
          ho.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.heureka.<?php echo $langLower ?>/direct/i/gjs.php?n=wdgt&sak=<?php echo $widgetId ?>';
          var s = document.getElementsByTagName('script')[0];
          s.parentNode.insertBefore(ho, s);
        })();
        //]]>
    </script>
<?php else: ?>
    <script>
        window.mmp.cookies.sections.functional.functions.heurekaWidget = function () {
            window.heureka_widget_active = true;

            //<![CDATA[
            var _hwq = _hwq || [];
            _hwq.push(['setKey', '<?php echo $widgetId ?>']);
            _hwq.push(['setTopPos', '<?php echo $marginTop ?>']);
            _hwq.push(['showWidget', '<?php echo $position ?>']);
            (function () {
                var ho = document.createElement('script');
                ho.async = true;
                ho.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.heureka.<?php echo $langLower ?>/direct/i/gjs.php?n=wdgt&sak=<?php echo $widgetId ?>';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ho, s);
            })();
            //]]>
        }
    </script>
<?php endif; ?>
