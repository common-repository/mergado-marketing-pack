<?php declare(strict_types=1);

namespace Mergado\Service;

use WC_Product_CSV_Exporter;

class ProductExportService extends WC_Product_CSV_Exporter
{
    public static function getProducts($start = 0, $limit = 999999999): array
    {
        $exporter = new ProductExportService();

        $products = [];

        foreach ($exporter->exportProducts($start, $limit) as $k => $v) {
            $parentId = wc_get_product($v['id'])->get_parent_id();
            $product = wc_get_product($v['id']);
            $productPublished = in_array($product->get_status(), ['private', 'publish']);

            if ($parentId != 0) {
                $parentProduct = wc_get_product($parentId);
                $parentPublished = in_array($parentProduct->get_status(), ['private', 'publish']);
            } else {
                $parentProduct = false;
                $parentPublished = true;
            }

            // Check if parent product exist (woocommerce made error - deleted main product but not variations)
            // Variation is for VARIATION, VARIABLE is for main VARIATION product
            if ($product->is_type('variation') && $parentProduct == false) {
                $parentExists = false;
            } else {
                $parentExists = true;
            }

            // Check if not password protected and if parent no password protected (for variants)
            if ($parentPublished && $productPublished && $parentExists) {
                if ($v['type'] != 'grouped') { // preskoceni slozenych produktu
                    $v['productObject'] = $product;
                    $v['parentProductId'] = $parentId;
                    $v['parentProduct'] = $parentProduct;
                    $products[$v['id']] = $v;
                }
            }
        }

        return $products;
    }

    private function exportProducts($start = 0, $limit = null): array
    {
        if($limit === NULL) {
            $limit = 9999999;
        }

        $this->set_page($start);
        $this->set_limit($limit);
        $this->prepare_data_to_export();
        return $this->row_data;
    }
}
