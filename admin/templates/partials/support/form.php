<?php
    $formSent = false;

    if (isset($_POST['submit-ticket-form'])) {
        $formSent = true;
        $to = 'mergado@mergado.cz';
        $from = $_POST['email'];
        $name = get_bloginfo('name');
        $attachment = '';
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html;charset=utf-8"."\r\n";
        $headers .= "From: " . $name . " <" . $from . ">" . "\r\n";
        $subject = '[MP_support_woo] ' . $_POST['subject'];
        $msg = $_POST['issue'];

        $msg .= '<br><br>';

        $msg .= '<table><tbody><tr><td>';
        $msg .= formattedTable($settingsData['base']);
        $msg .= '</td>';

        $msg .= '<td>';
        $msg .= formatAds($settingsData['adsystems']);
        $msg .= '</td></tr></tbody>';

        $msg .= emailStyles();

        wp_mail($to, $subject, $msg, $headers, $attachment);

        unset($_POST['email']);
        unset($_POST['subject']);
        unset($_POST['issue']);
    }

    function formattedTable($data)
    {
        $output = '<table class="special">';
        $output .= '<thead>';
        $output .= '<tr><th colspan="2">Basic info</th></tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        foreach($data as $item) {
            $output .= '<tr>';
            $output .= '<th>' . $item['name'] . '</th>';
            $output .= '<td>' . $item['value'] . '</td>';
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';

        return $output;
    }

    function formatAds($data)
    {
        $output = '<table class="special">';
        $output .= '<thead>';
        $output .= '<tr><th colspan="2">Ad systems</th></tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        foreach($data as $key => $item) {
            if ($item === 'active') {
                $class = 'active';
            } else {
                $class= '';
            }

            $output .= '<tr class="' . $class . '">';
            $output .= '<th>' . $key . '</th>';
            $output .= '<td>' . $item . '</td>';
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        return $output;
    }

    function emailStyles()
    {
        return '<style>
            table td {
                vertical-align: top;
            }

            table.special {
              font-family: Arial, Helvetica, sans-serif;
              border-collapse: collapse;
              font-size: 12px;
            }
            
            table.special td, table th {
              border: 1px solid #ddd;
              padding: 6px;
            }

            table.special tr:nth-child(even){background-color: #f2f2f2;}

            table.special tr:hover {background-color: #ddd;}

            table.special thead th {
              padding-top: 8px;
              padding-bottom: 8px;
              text-align: left;
              background-color: #04AA6D;
              color: white;
              font-weight: 600;
            }
           
            tr.active td,
            tr.active th {
                background-color: #e4ffc3;
            }
            
            table.special tbody th {
                font-weight: 500;
                text-align: left;
            }
        </style>';
    }
?>

<form method="post" class="mmp_contactForm">
    <div class="mmp_contactForm__line">
        <div class="mmp_contactForm__col">
            <label for="email"><?php echo __('Email for reply', 'mergado-marketing-pack') ?></label>
            <input type="email" id="email" name="email" required />
        </div>
        <div class="mmp_contactForm__col">
            <label for="subject"><?php echo __('Subject', 'mergado-marketing-pack') ?></label>
            <input type="text" id="subject" name="subject" required />
        </div>
    </div>
    <div class="mmp_contactForm__issue">
        <label for="issue"><?php echo __('Issue description', 'mergado-marketing-pack') ?></label>
        <textarea name="issue" id="issue" cols="20" rows="7" required></textarea>
    </div>

    <p class="mmp_contactForm__notice"><?php echo __('By sending this ticket you also agree to submit diagnostic information of your website. This data includes your WP and WC versions, PHP version, Mergado Pack plugin settings, URLs of your exports and cron, list of plugins and theme, WooCoommerce logs. All this information will help us to process your request faster.', 'mergado-marketing-pack') ?></p>

    <div class="mmp_btnHolder mmp_btnHolder--right" style="margin-top: 10px;">
        <button type="submit" name="submit-ticket-form" class="mmp_btn__blue mmp_btn__blue--small">
            <svg class="mmp_icon">
                <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#email' ?>"></use>
            </svg>
            <?php echo __('Send ticket', 'mergado-marketing-pack') ?>
        </button>
    </div>

    <?php if($formSent): ?>
        <div class="mmp_contactForm__formSent">
            <?php echo __('Your ticket has been sent. We will answer you as soon as possible', 'mergado-marketing-pack') ?>
        </div>
    <?php endif; ?>
</form>
