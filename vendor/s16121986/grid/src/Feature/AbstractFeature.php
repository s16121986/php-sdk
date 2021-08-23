<?php

namespace Corelib\Grid\Feature;

use Corelib\Grid\Grid;

abstract class AbstractFeature {

	protected $grid = null;

	public function __construct(Grid $grid) {
		$this->grid = $grid;
	}

}