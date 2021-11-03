<?php

namespace Gsdk\Grid\Column;

abstract class AbstractColumn {

	private static array $defaultOptions = [
		'order' => false,
		'text' => '',
		'renderer' => null,
		'params' => null,
		'emptyText' => ''
	];

	protected array $options = [
		'renderer' => false
	];

	public function __set($name, $value) {
		$this->setOption($name, $value);
	}

	public function __get($name) {
		if (isset($this->options[$name]))
			return $this->options[$name];
		//if (isset(self::$_default[$name])) return self::$_default[$name];
		return null;
	}

	public function __construct($name, $options = []) {
		if (!isset($options['id']))
			$options['id'] = 'grid_column_' . $name;

		if (!isset($options['class']))
			$options['class'] = '';

		$options['type'] = strtolower(str_replace(__NAMESPACE__ . '\\', '', get_class($this)));
		$options['class'] .= ' column-' . $options['type'];
		$this->setName($name)
			->setOptions(array_merge(self::$defaultOptions, $this->options, $options));

		$this->init();
	}

	public function setName($name) {
		$this->options['name'] = $name;
		return $this;
	}

	public function setOptions($options) {
		foreach ($options as $k => $v) {
			$this->setOption($k, $v);
		}
		return $this;
	}

	public function setOption($key, $option) {
		$this->options[$key] = $option;
		return $this;
	}

	public function formatValue($value, $row = null) {
		return $value;
	}

	public function prepareValue($value) {
		return $value;
	}

	public function render($value, $row) {
		$value = $this->formatValue($value, $row);
		if ($this->renderer)
			$value = call_user_func_array($this->renderer, [$row, $value, $this->params]);

		if (null === $value || '' === $value)
			return $this->emptyText;

		//$row['value'] = $value;
		if ($this->href)
			return '<a href="' . \Format::formatTemplate($this->href, $row) . '"'
				. ($this->hrefTarget ? ' target="' . $this->hrefTarget . '"' : '') . '>' . $value . '</a>';

		return $value;
	}

	protected function init() {

	}

}
