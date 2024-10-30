<?php

use Mergado\Feed\Stock\StockFeed;

$stockFeed = new StockFeed();
$isAlreadyFinished = $stockFeed->isWizardFinished();
$wizardName = 'Heureka availability';

if ((isset($_GET['mmp-wizard']) && $_GET['mmp-wizard'] === 'stock') || (!$isAlreadyFinished || isset($_GET['step']))) {
    $wizardData = $stockFeed->getWizardData();
	?>
    <script>
        if (typeof window.mmpWizardData === 'undefined') {
            window.mmpWizardData = {'stock': <?php echo json_encode($wizardData) ?>};
        } else {
            window.mmpWizardData['stock'] = (<?php echo json_encode($wizardData) ?>);
        }
    </script>
	<?php

    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/main.php'));
} else {
    ?>
    <div class="card full">
        <h1 class="mmp_h1"><?php echo __('Heureka Availability feed', 'mergado-marketing-pack') ?></h1>

        <?php
        $feedBoxData = $stockFeed->getDataForTemplates();

        include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'feedBox/feedBox.php'));
        ?>
    </div>
    <?php
}
