<?php

namespace Gsdk\View\Layout;

use Ustabor\Infrastructure\Models\Category\Category;
use Ustabor\Repositories\System\SiteRepository;

trait AppData {

	public function addMetaVariable($name, $value): static {
		$this->head->addMetaName($name, htmlspecialchars(json_encode($value)));
		return $this;
	}

	public function addAppData(array $data): static {
		return $this->addMetaVariable('application-data', $data);
	}

}
