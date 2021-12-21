<?php

namespace Gsdk\Grid;

use Gsdk\Grid\Data\Data;
use Gsdk\Grid\Util\Sorting;

class Grid {

	protected $options = [
		'emptyGridText' => '',
		'class' => 'table-grid',
		'header' => true,
		'view' => 'Table',
		'viewConfig' => null,
		'orderUrl' => null
	];
	protected array $columns = [];
	protected $data = null;
	protected $sorting;

	public static function extend($type, $class) {
		ServiceManager::extend($type, $class);
	}

	public static function registerNamespace($namespace) {
		ServiceManager::registerNamespace($namespace);
	}

	public function __construct($options = []) {
		$this->setOptions($options);
		$this->data = new Data();
		$this->data->setParams($options);
		$this->sorting = new Sorting($options);
	}

	public function __get(string $name) {
		if (isset($this->$name))
			return $this->$name;

		return ($this->options[$name] ?? null);
	}

	public function __set($name, $value) {
		$this->options[$name] = $value;
	}

	public function setOptions($options): static {
		foreach ($options as $k => $v) {
			$this->setOption($k, $v);
		}
		return $this;
	}

	public function setOption($key, $option): static {
		$this->options[$key] = $option;
		return $this;
	}

	public function addColumn($column, $type = 'text', array $options = []): static {
		if (is_array($type)) {
			$options = $type;
			$type = 'text';
		}

		if (is_string($column))
			$column = ServiceManager::columnFactory($column, $type, $options);
		else if ($column instanceof Column\AbstractColumn) {

		}

		$this->columns[$column->name] = $column;
		return $this;
	}

	public function getColumns(): array {
		return $this->columns;
	}

	public function getColumn($name) {
		return ($this->columns[$name] ?? null);
	}

	public function getData(): ?Data {
		return $this->data;
	}

	public function setData($data): static {
		$this->data->set($data);
		return $this;
	}

	public function setParams(array $params): static {
		$this->data->setParams($params);
		return $this;
	}

	public function orderBy($name, $order = 'asc'): static {
		$this->sorting->orderBy($name, $order);
		return $this;
	}

	public function paginator($paginator = null): static {
		$this->data->paginator($paginator);
		return $this;
	}

	public function getPaginator() {
		return $this->data->paginator;
	}

	public function isEmpty(): bool {
		return $this->data->isEmpty();
	}

	public function render() {
		$this->sorting->fromRequest();
		$cls = __NAMESPACE__ . '\View\\' . $this->view;
		$view = new $cls($this);
		return $view->render();
	}

	public function __toString(): string {
		return $this->render();
	}

}
