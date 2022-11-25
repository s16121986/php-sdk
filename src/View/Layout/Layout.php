<?php

namespace Gsdk\View\Layout;

use Gsdk\Meta\Page as MetaPage;

class Layout {

	use MetaConfig,
		MenuTrait,
		AppData;

	protected $options = [
		'template' => 'layouts.default'
	];

	protected array $data = [];

	protected $page;

	public function __construct() {
		$this->page = new MetaPage();
	}

	public function __call($name, $arguments) {
		return call_user_func_array([$this->page, $name], $arguments);
	}

	public function __get($name) {
		switch ($name) {
			case 'page':
				return $this->$name;
			case 'head':
				return $this->page->getHead();
		}

		return $this->page->$name;
	}

	public function __set(string $name, $value): void {
		$this->data[$name] = $value;
	}

	public function setOption($name, $value): static {
		$this->options[$name] = $value;
		return $this;
	}

	public function view($view, array $data = []): static {
		$this->setOption('view', $view);
		foreach ($data as $k => $v) {
			$this->data[$k] = $v;
		}

		if (isset($data['style']))
			$this->options['style'] = $data['style'];

		if (isset($data['script']))
			$this->options['script'] = $data['script'];

		return $this;
	}

	public function layout() {
		$this->configure();

		return view($this->options['template'], $this->getLayoutData());
	}

	public function __toString(): string {
		return (string)$this->layout();
	}

	protected function getLayoutData() {
		return [
			'layout' => $this,
			'meta' => $this->page,
			'content' => view($this->options['view'], $this->getViewData())
		];
	}

	protected function getViewData() {
		$data = $this->data;
		$data['layout'] = $this;
		$data['page'] = $this->options['page'] ?? null;
		$data['title'] = $this->options['title'] ?? null;

		return $data;
	}

	protected function configure() {
		$this->addDefaultMeta();

		$this->addPageData();
	}

}
