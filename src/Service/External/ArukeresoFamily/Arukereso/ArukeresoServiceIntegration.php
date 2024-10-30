<?php

namespace Mergado\Service\External\ArukeresoFamily\Arukereso;

use Mergado\Service\External\ArukeresoFamily\AbstractArukeresoFamilyServiceIntegration;
use Mergado\Traits\SingletonTrait;

class ArukeresoServiceIntegration extends AbstractArukeresoFamilyServiceIntegration {

    use SingletonTrait;

	public function __construct() {
        parent::__construct(ArukeresoService::getInstance());
	}
}
