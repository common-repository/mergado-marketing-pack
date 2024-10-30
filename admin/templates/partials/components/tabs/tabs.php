<!--
    $tabsSettings variable should be set in parent template:

    LIKE:
    $tabsSettings = [
        'tabs' => [
            'category' => ['title' => 'Category feeds', 'active' => true, 'icon' => 'icons.svg#list'], //default active with icon
            'stock' => ['title' => 'Heureka<br>Availability feed', 'icon' => 'icons.svg#service-heureka'], // normal with icon
            'settings' => ['title' => '', 'icon' => 'icons.svg#settings'], //only icon
        ],
        'tabContentPath' => 'other-feeds-tabs/',
    ];
-->

<?php
    function isTabActive($key, $tab)
    {
        if (isset($_GET['mmp-tab'])) {
            $currentActive = $_GET['mmp-tab'];
        } else {
            $currentActive = false;
        }

        if (($currentActive && $currentActive === $key) || (!$currentActive && isset($tab['active']) && $tab['active'])) {
            return true;
        } else {
            return false;
        }
    }
?>

<ul class="mmp-tabs mmp-tabs__menu">
    <?php foreach ($tabsSettings['tabs'] as $key => $tab): ?>
        <li class="<?php echo isTabActive($key, $tab) ? 'active' : '' ?>">
            <?php if(isset($tab['icon']) && $tab['icon'] !== ''): ?>
                <a href="#" data-mmp-tab-button="<?php echo $key ?>" class="hasIcon">
                    <svg>
                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . $tab['icon'] ?>"></use>
                    </svg>
            <?php
                else:
            ?>
                <a href="#" data-mmp-tab-button="<?php echo $key ?>">
            <?php endif; ?>

                <?php if(isset($tab['title']) && $tab['title'] !== ''): ?>
                    <div class="mmp-tabs__title">
                        <?php echo $tab['title'] ?>
                    </div>
                <?php endif; ?>
                <div class="mmp-tabs__active-count"></div>

            </a>
        </li>
    <?php endforeach ?>
</ul>

<div class="mmp-tabs mmp-tabs__content">
    <?php foreach ($tabsSettings['tabs'] as $key => $tab): ?>
        <div class="mmp-tabs__tab <?php echo isTabActive($key, $tab) ? 'active' : '' ?>"
             data-mmp-tab="<?php echo $key ?>">
            <?php include($tabsSettings['tabContentPath'] . $key . '.php'); ?>
        </div>
    <?php endforeach ?>
</div>
