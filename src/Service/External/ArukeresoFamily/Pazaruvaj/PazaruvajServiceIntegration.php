<?php

namespace Mergado\Service\External\ArukeresoFamily\Pazaruvaj;

use Mergado\Service\External\ArukeresoFamily\AbstractArukeresoFamilyServiceIntegration;
use Mergado\Traits\SingletonTrait;

class PazaruvajServiceIntegration extends AbstractArukeresoFamilyServiceIntegration {

    use SingletonTrait;

	public function __construct() {
        parent::__construct(PazaruvajService::getInstance());
	}
}
