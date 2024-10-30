<?php

use Mergado\Feed\Product\ProductFeed;

$productFeed = new ProductFeed();
$isAlreadyFinished = $productFeed->isWizardFinished();
$wizardName = 'product';

if ((isset($_GET['mmp-wizard']) && $_GET['mmp-wizard'] === 'product') || (!$isAlreadyFinished || isset($_GET['step']))) {
    $wizardData = $productFeed->getWizardData();
?>

    <script>
        if (typeof window.mmpWizardData === 'undefined') {
            window.mmpWizardData = {'product': <?php echo json_encode($wizardData) ?>};
        } else {
            window.mmpWizardData['product'] = (<?php echo json_encode($wizardData) ?>);
        }
    </script>

<?php
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/main.php'));
} else {
    ?>
    <div class="card full">
        <h1 class="mmp_h1"><?php echo __('Product feeds', 'mergado-marketing-pack') ?></h1>

        <?php
            $feedBoxData = $productFeed->getDataForTemplates();

            include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'feedBox/feedBox.php'));
        ?>
    </div>
    <?php
}
