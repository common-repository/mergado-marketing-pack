<?php

use Mergado\Feed\Customer\CustomerFeed;

$customerFeed = new CustomerFeed();
$isAlreadyFinished = $customerFeed->isWizardFinished();
$wizardName = 'customer';

if ((isset($_GET['mmp-wizard']) && $_GET['mmp-wizard'] === 'customer') || (!$isAlreadyFinished || isset($_GET['step']))) {

    $wizardData = $customerFeed->getWizardData();
?>
    <script>
        if (typeof window.mmpWizardData === 'undefined') {
            window.mmpWizardData = {'customer': <?php echo json_encode($wizardData) ?>};
        } else {
            window.mmpWizardData['customer'] = (<?php echo json_encode($wizardData) ?>);
        }
    </script>
<?php
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/main.php'));
} else {
    ?>
    <div class="card full">
        <h1 class="mmp_h1"><?php echo __('Customer feeds', 'mergado-marketing-pack') ?></h1>

        <?php
        $feedBoxData = $customerFeed->getDataForTemplates();

        include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'feedBox/feedBox.php'));
        ?>
    </div>
    <?php
}
