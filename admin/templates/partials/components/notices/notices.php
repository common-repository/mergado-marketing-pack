<?php
    if (isset($_GET['flash'])) {
        switch ($_GET['flash']) {
            case 'settingsSaved':
                ?>
                <div class="mergado-updated-notice success deleteOnTab">
                    <p><?php echo __('Settings saved.', 'mergado-marketing-pack'); ?></p>
                </div>
                <?php
                break;
        }
    }
?>
