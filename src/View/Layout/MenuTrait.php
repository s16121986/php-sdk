<?php

namespace Gsdk\View\Layout;

trait MenuTrait {

	protected $menuNamespace;

	protected array $menu = [];

	public function menu($menu) {
		if (!isset($this->menu[$menu])) {
			$cls = $this->menuNamespace . '\\' . ucfirst($menu);

			$this->menu[$menu] = new $cls();
		}

		return $this->menu[$menu];
	}

}
