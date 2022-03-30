<?php

namespace Gsdk\Meta\JsonLd;

class BreadcrumbList extends AbstractThing {

	private $items = [];

	protected function init() {
	}

	public function addItem($name, $item) {
		$this->items[] = [
			'@type' => 'ListItem',
			'position' => count($this->items),
			'name' => $name,
			'item' => $item
		];
		return $this;
	}

	public function getHtml() {
		if (empty($this->items))
			return '';

		$data = [
			'@context' => self::SCHEME,
			'@type' => $this->type,
			'itemListElement' => []
		];

		foreach ($this->items as $item) {
			$data['itemListElement'][] = $item;
		}

		return json_encode($data);
	}

}