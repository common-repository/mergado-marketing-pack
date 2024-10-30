<?php

namespace Mergado\FeedVisibility;

use WC_Product;

abstract class AbstractFeedVisibilityService
{
    private const WP_PRODUCT_VISIBILITY_DEFAULT = 'mmp-wp-product-feed-visibility';
    private const WC_PRODUCT_VISIBILITY_DEFAULT = 'mmp-wc-product-feed-visibility';

    protected $feedType;
    public $baseVisibilityOptions;
    public $catalogVisibilityOptions;

    protected function __construct(string $feedType)
    {
        $this->feedType = $feedType;
        $this->baseVisibilityOptions = $this->getBaseVisibilityOptions();
        $this->catalogVisibilityOptions = $this->getCatalogVisibilityOptions();
    }

    private function getBaseVisibilityOptions(): array
    {
        $dbNamePrivate = $this->getDbFieldName(self::WP_PRODUCT_VISIBILITY_DEFAULT,'private');
        $dbNamePassword = $this->getDbFieldName(self::WP_PRODUCT_VISIBILITY_DEFAULT,'password');
        $dbNamePublic = $this->getDbFieldName(self::WP_PRODUCT_VISIBILITY_DEFAULT,'public');

        return [
            'private' => [
                'name' => __('Private'),
                'databaseName' => $dbNamePrivate,
                'value' => get_option($dbNamePrivate, 0)
            ],
            'password' => [
                'name' => __('Password protected'),
                'databaseName' => $dbNamePassword,
                'value' => get_option($dbNamePassword, 0)
            ],
            'public' => [
                'name' => __('Public'),
                'databaseName' => $dbNamePublic,
                'value' => get_option($dbNamePublic, 0)
            ]
        ];
    }

    private function getCatalogVisibilityOptions(): array
    {
        $output = [];
        $options = wc_get_product_visibility_options();

        if ($options && count($options) > 0) {
            foreach ($options as $key => $item) {
                $dbName = $this->getDbFieldName(self::WC_PRODUCT_VISIBILITY_DEFAULT, $key);

                $output[$key] = [
                    'name' => $item,
                    'databaseName' => $dbName,
                    'value' => get_option($dbName, 0),
                ];
            }
        }

        return $output;
    }

    public function getDbFieldName(string $defaultName, string $name): string
    {
        return sprintf('%s-%s--%s', $defaultName, $this->feedType, $name);
    }

    public function isVisibilityActive(string $baseVisibility, string $catalogVisibility): bool
    {
        if (isset($this->baseVisibilityOptions[$baseVisibility]) && $this->baseVisibilityOptions[$baseVisibility]['value'] === '1') {
            if (isset($this->catalogVisibilityOptions[$catalogVisibility]) && $this->catalogVisibilityOptions[$catalogVisibility]['value'] === '1') {
                return true;
            }
        }
        return false;
    }

    /**
     * Get product base visibility by WP rules
     */
    public function getBaseVisibility(WC_Product $productObject): string
    {
        if ( 'private' === $productObject->get_status() ) {
            $baseVisibility = 'private';
        } elseif ( ! empty( $productObject->get_post_password() ) ) {
            $baseVisibility = 'password';
        } else {
            $baseVisibility = 'public';
        }

        return $baseVisibility;
    }

    /**
     * Returns product visibility if enabled for export
     *
     * @param $productObject
     * @param $parentObject
     * @return array | bool
     */
    public function isProductVisibilityEnabledForExport($productObject, $parentObject) {
        $baseVisibility = $this->getBaseVisibility($productObject);
        $catalogVisibility = $productObject->get_catalog_visibility();

        // Variation
        if ($parentObject) {
            // Product isn't disabled
            if ($baseVisibility !== 'private') {
                $baseParentVisibility = $this->getBaseVisibility($parentObject);
                $catalogParentVisibility = $parentObject->get_catalog_visibility();

                if ($this->isVisibilityActive($baseParentVisibility, $catalogParentVisibility)) {
                    return ['visibility' => $baseParentVisibility, 'catalogVisibility' => $catalogParentVisibility];
                }
            }

        // Simple products
        } else {
            if ($this->isVisibilityActive($baseVisibility, $catalogVisibility)) {
                return ['visibility' => $baseVisibility, 'catalogVisibility' => $catalogVisibility];
            }
        }

        return false;
    }
}
