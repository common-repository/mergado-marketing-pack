<?php

use Mergado\Manager\TokenManager;

$tokenReseted = false;

    if(isset($_POST['reset_token'])) {
        $tokenReseted = TokenManager::generateToken();
        unset($_POST['reset_token']);
    }
?>

<div class="card full">
    <h2 class="mmp_h2"><?php echo __('Reset feed token', 'mergado-marketing-pack') ?></h2>

    <p>
        <?php echo __('In case that someone stole your token that is used to generate and display feeds, click on the button below and change it.', 'mergado-marketing-pack'); ?>
    </p>
    <p class="mmp_token_change_warning">
        <strong style="margin-right: 10px;">Warning:</strong> <?php echo __('Resetting the token will change the URLs of external crons and all feeds! You must manually change them in all services!', 'mergado-marketing-pack'); ?>
    </p>

    <form method="post" class="mmp_reset_token" style="margin-bottom: 20px;">
        <input type="hidden" name="reset_token" value="true">
        <button type="submit" class="mmp_btn__blue mmp_btn__blue--small"><?php echo __('Reset feed token', 'mergado-marketing-pack') ?></button>

        <?php if($tokenReseted): ?>
            <div class="mmp_reset_token__changed">
                <?php echo __('Your token has been changed. In case you are using external crons, please update cron URLS and also feed URLs in Mergado application.', 'mergado-marketing-pack') ?>
            </div>
        <?php endif; ?>
    </form>
</div>
