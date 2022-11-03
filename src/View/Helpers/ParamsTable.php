<?php

namespace Gsdk\View\Helpers;

use Gsdk\Format\Number;

class ParamsTable {

	protected $view;

	protected $tableClass = 'table-params';

	private array $params = [];

	private $data;

	private $values = [];

	public function __construct($data = null) {
		$this->data($data);
	}

	public function __get(string $name) {
		return $this->values[$name] ?? $this->data->$name ?? null;
	}

	public function __call(string $name, array $arguments) {
		if (count($arguments) < 2)
			throw new \Exception('Arguments number required');

		return $this->param($arguments[0], $arguments[1], $name, $arguments[2] ?? []);
	}

	public function param(string $name, string $label, $type = 'text', array $options = []): static {
		$options['name'] = $name;
		$options['label'] = $label;
		$options['type'] = $type;
		$this->params[] = $this->paramFactory($options);
		return $this;
	}

	public function custom(string $name, string $label, callable $formatFunction, array $options = []): static {
		$options['format'] = $formatFunction;
		return $this->param($name, $label, 'custom', $options);
	}

	public function enum(string $name, string $label, string $enum, array $options = []): static {
		$options['enum'] = $enum;
		return $this->param($name, $label, 'enum', $options);
	}

	public function data($data): static {
		if (empty($data))
			$this->data = new \stdClass();
		else if (is_array($data))
			$this->data = (object)$data;
		else if (is_object($data))
			$this->data = $data;
		else
			throw new \Exception('Data format error');

		return $this;
	}

	public function value($name, $value): static {
		$this->values[$name] = $value;
		return $this;
	}

	public function values(array $values): static {
		$this->values = array_merge($this->values, $values);
		return $this;
	}

	public function tableClass(string $tableClass): static {
		$this->tableClass = $tableClass;

		return $this;
	}

	public function view(string $view): static {
		$this->view = $view;

		return $this;
	}

	public function render(): string {
		if ($this->view)
			return (string)view($this->view, [
				'table' => $this
			]);

		$html = '';
		foreach ($this->params as $param) {
			$html .= $this->renderParam($param);
		}

		return $html ? '<table class="' . $this->tableClass . '">' . $html . '</table>' : '';
	}

	protected function paramFactory($options): object {
		return (object)$options;
	}

	protected function getValueHref($value, $param) {
		if (isset($param->href))
			return $param->href;

		return match ($param->type) {
			'email' => 'mailto:' . $value,
			'phone' => 'tel:' . preg_replace('/[^0-9+]/', '', $value),
			'url' => $value,
			default => null,
		};
	}

	protected function prepareValue($value, $param) {
		$format = $param->format ?? null;
		if (!empty($format) && !is_string($param->format) && is_callable($param->format))
			return call_user_func($param->format, $value);

		$rule = $param->type;
		switch ($rule) {
			case 'enum':
				return call_user_func([$param->enum, 'getLabel'], $value);
		};

		if (app('format')->hasExtension($rule))
			return app('format')->$rule($value, $format ?? $rule);

		return $value;
	}

	protected function prepareEmpty($param) {
		return $param->emptyText ?? null;
	}

	protected function prepareText($value, $param): ?string {
		if (empty($value))
			return $this->prepareEmpty($param);

		$href = $this->getValueHref($value, $param);

		$value = $this->prepareValue($value, $param);

		if (!is_scalar($value))
			$value = serialize($value);

		$text = match ($param->type) {
			'address' => '<address>' . $value . '</address>',
			default => $value,
		};

		if ($href)
			return '<a href="' . $href . '"'
				. (isset($param->target) ? ' target="' . $param->target . '"' : '')
				. '>' . ($param->text ?? $text) . '</a>';

		return $text;
	}

	protected function renderParam($param): string {
		$text = $this->prepareText($this->{$param->name}, $param);
		if (null === $text)
			return '';

		return '<tr class="' . ($param->class ?? '') . '">'
			. '<th>' . $param->label . '</th>'
			. '<td>' . $text . '</td>'
			. '</tr>';
	}

	public function __toString(): string {
		return $this->render();
	}

}
