<script>
    const bianoPixelConfig = {
        consent: <?php echo $consent; ?>,
        debug: <?php if(MERGADO_DEBUG): ?>1<?php else: ?>0<?php endif; ?>
    };

    const bianoLanguage = '<?php echo $lang; ?>';

    !function (b, i, a, n, o, p, x, s) {
        if (b.bianoTrack) return;
        o = b.bianoTrack = function () {
            o.callMethod ?
                o.callMethod.apply(o, arguments) : o.queue.push(arguments)
        };
        o.push = o;
        o.queue = [];
        a = a || {};
        n = a.consent === void (0) ? !0 : !!a.consent;
        o.push('consent', n);
        s = 'script';
        p = i.createElement(s);
        p.async = !0;
        p.src = 'https://' + (n ? 'pixel.biano.' + bianoLanguage : 'bianopixel.com') +
            '/' + (a.debug ? 'debug' : 'min') + '/pixel.js';
        x = i.getElementsByTagName(s)[0];
        x.parentNode.insertBefore(p, x);
    }(window, document, bianoPixelConfig);
</script>
