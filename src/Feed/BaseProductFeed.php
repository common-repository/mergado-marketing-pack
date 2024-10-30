<?php declare(strict_types=1);

namespace Mergado\Feed;

abstract class BaseProductFeed extends BaseFeed {
    public function getTotalTemporaryFiles($itemsPerRun, $lastRunIterationCount)
    {
        if($itemsPerRun === 0) {
            $totalFiles = 0;
        } else {
            $publishedProductsCount = (int) wp_count_posts( 'product' )->publish;

            $specialCoefficient = ( $lastRunIterationCount * $itemsPerRun ) * 1.2; // Last run products + 20%;

            // If first run of cron or someone added more than 20% of products
            if ( $lastRunIterationCount === 0 || $specialCoefficient < $publishedProductsCount ) {
                $totalGenerationRuns = $publishedProductsCount / $itemsPerRun;
            } else {
                $totalGenerationRuns = $lastRunIterationCount;
            }

            $totalFiles = ceil( $totalGenerationRuns );
        }

        return $totalFiles;
    }

    public function getTotalProducts($productsPerRun, $lastRunIterationCount)
    {
        return $this->getTotalTemporaryFiles($productsPerRun, $lastRunIterationCount) * $productsPerRun;
    }
}
