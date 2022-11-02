<?php

namespace Gsdk\View\Navigation;

use stdClass;

class Breadcrumbs {

	protected array $items = [];

	protected $homeItem;

	protected $view;

	protected $separator = '<div class="separator"></div>';

	public function __construct() {
		$this->boot();
	}

	public function view($view): static {
		$this->view = $view;
		return $this;
	}

	public function isEmpty(): bool {
		return empty($this->items);
	}

	public function add($params): static {
		if (is_string($params))
			$params = ['text' => $params];

		$this->items[] = $this->itemFactory($params);
		return $this;
	}

	public function addUrl($url, $params): static {
		if (is_string($params))
			$params = ['text' => $params];

		return $this->add(array_merge($params, ['url' => $url]));
	}

	public function addRoute($route, $params): static {
		if (is_string($params))
			$params = ['text' => $params];

		return $this->add(array_merge($params, [
			'id' => $route,
			'url' => route($route)
		]));
	}

	public function addHome(string $url, $params): static {
		if (is_string($params))
			$params = ['text' => $params];

		$this->homeItem = $this->itemFactory(array_merge($params, ['url' => $url]));

		return $this;
	}

	public function items(): array {
		return $this->items;
	}

	public function render(): string {
		if ($this->isEmpty())
			return '';

		if ($this->view)
			return (string)view($this->view, ['breadcrumbs' => $this]);

		return '<div class="breadcrumbs"><nav>'
			. $this->renderItems()
			. '</nav></div>';
	}

	public function __toString(): string {
		return $this->render();
	}

	protected function boot() {

	}

	protected function itemFactory($params): stdClass {
		$item = new stdClass();
		$item->id = $params['id'] ?? null;
		$item->title = $params['title'] ?? null;
		$item->target = $params['target'] ?? null;
		$item->href = $params['href'] ?? $params['url'] ?? null;
		$item->text = $params['text'] ?? '';
		$item->class = $params['class'] ?? $params['cls'] ?? null;

		return $item;
	}

	protected function renderItems(): string {
		$menu = [];

		if ($this->homeItem)
			$menu[] = $this->renderItem($this->homeItem);

		foreach ($this->items as $item) {
			$menu[] = $this->renderItem($item);
		}

		return implode($this->separator, $menu);
	}

	protected function renderItem($item): string {
		$tag = $item->href ? 'a' : 'div';
		$html = '<' . $tag;
		$attributes = ['id', 'title', 'target', 'href', 'class'];
		$html .= array_reduce($attributes, function ($s, $k) use ($item) {
			return $s . ($item->$k ? ' ' . $k . '="' . $item->$k . '"' : '');
		}, '');
		$html .= '>';
		$html .= $item->text;
		$html .= '</' . $tag . '>';

		return $html;
	}

}
