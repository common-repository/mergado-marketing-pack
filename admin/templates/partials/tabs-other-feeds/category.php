<?php

use Mergado\Feed\Category\CategoryFeed;

$categoryFeed = new CategoryFeed();
$isAlreadyFinished = $categoryFeed->isWizardFinished();
$wizardName = 'category';

if ((isset($_GET['mmp-wizard']) && $_GET['mmp-wizard'] === 'category') || (!$isAlreadyFinished || isset($_GET['step']))) {

    $wizardData = $categoryFeed->getWizardData();
?>
    <script>
        if (typeof window.mmpWizardData === 'undefined') {
            window.mmpWizardData = {'category': <?php echo json_encode($wizardData) ?>};
        } else {
            window.mmpWizardData['category'] = (<?php echo json_encode($wizardData) ?>);
        }
    </script>
<?php
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/main.php'));
} else {
    ?>
    <div class="card full">
        <h1 class="mmp_h1"><?php echo __('Category feeds', 'mergado-marketing-pack') ?></h1>

        <?php
        $feedBoxData = $categoryFeed->getDataForTemplates();

        include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'feedBox/feedBox.php'));
        ?>
    </div>
    <?php
}
