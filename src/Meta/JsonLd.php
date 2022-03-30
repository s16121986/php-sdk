<?php

namespace Gsdk\Meta;

class JsonLd {

	private $items = [];

	public function __get($name) {
		return $this->items[$name] ?? null;
	}

	public function addThing($type, $data = null) {
		$cls = __NAMESPACE__ . '\JsonLd\\' . $type;
		$item = new $cls($data);
		$this->items[strtolower($type)] = $item;
		return $item;
	}

	public function addOrganization($data = null) {
		return $this->addThing('Organization', $data);
	}

	public function addArticle($data = null) {
		return $this->addThing('Article', $data);
	}

	public function addBreadcrumbs($data = null) {
		return $this->addThing('BreadcrumbList', $data);
	}

	public function getHtml(): string {
		$html = [];
		foreach ($this->items as $item) {
			$s = $item->getHtml();
			if ($s)
				$html[] = $s;
		}

		return empty($html) ? '' : '<script type="application/ld+json">[' . implode(',', $html) . ']</script>';
	}

}