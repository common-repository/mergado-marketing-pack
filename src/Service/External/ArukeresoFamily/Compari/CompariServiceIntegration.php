<?php

namespace Mergado\Service\External\ArukeresoFamily\Compari;

use Mergado\Service\External\ArukeresoFamily\AbstractArukeresoFamilyServiceIntegration;
use Mergado\Traits\SingletonTrait;

class CompariServiceIntegration extends AbstractArukeresoFamilyServiceIntegration {

    use SingletonTrait;

	public function __construct() {
        parent::__construct(CompariService::getInstance());
	}
}
