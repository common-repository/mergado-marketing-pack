(function ($) {
    'use strict';

    $(function () {
        $('.wp-schedule-input').each(function () {
            changeEstimate($(this));
        });

        $('.wp-schedule-input').on('change', function () {
            changeEstimate($(this));
        });

        function changeEstimate(element) {
            var feedType = element.closest('.mmp_wizard').attr('data-mmp-wizard-type');
            var schedule = element.children("option:selected").val();

            var data = window.mmpWizardData[feedType];
            var token = data.token;

            $.ajax({
                type: "POST",
                url: 'admin-ajax.php',
                data: {
                    action: 'ajax_get_schedule_estimate',
                    feed: data.feed,
                    schedule: schedule,
                    token: token,
                },
                success: function (response) {
                    $('[data-mmp-wizard-type="' + feedType + '"][data-mmp-wizard-step="4a"]').find('[data-pps-output]').html(response['data']['estimate']);
                },
            });
        }
    });
})(jQuery)
