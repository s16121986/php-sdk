<?php

namespace Gsdk\Grid\Feature;

use Gsdk\Grid\Grid;

abstract class AbstractFeature {

	protected $grid = null;

	public function __construct(Grid $grid) {
		$this->grid = $grid;
	}

}
