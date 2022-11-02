<?php

namespace Gsdk\View;

class ParamsTable {

	protected static $defaults = [
		'dateFormat' => 'Y-m-d H:i'
	];

	protected $view;

	protected $tableClass = 'table-params';

	private array $params = [];

	private $data;

	private $values = [];

	public static function setDefaults(array $defaults) {
		static::$defaults = array_merge(static::$defaults, $defaults);
	}

	public function __construct($data = null) {
		$this->data($data);
	}

	public function __get(string $name) {
		return $this->values[$name] ?? $this->data->$name ?? null;
	}

	public function add(string $name, string $label, $type = 'text', array $options = []): static {
		$options['name'] = $name;
		$options['label'] = $label;
		$options['type'] = $type;
		$this->params[] = $this->paramFactory($options);
		return $this;
	}

	public function text(string $name, string $label, array $options = []): static {
		return $this->add($name, $label, 'text', $options);
	}

	public function email(string $name, string $label, array $options = []): static {
		return $this->add($name, $label, 'email', $options);
	}

	public function phone(string $name, string $label, array $options = []): static {
		return $this->add($name, $label, 'phone', $options);
	}

	public function custom(string $name, string $label, callable $formatFunction, array $options = []): static {
		$options['format'] = $formatFunction;
		return $this->add($name, $label, 'custom', $options);
	}

	public function enum(string $name, string $label, string $enum, array $options = []): static {
		$options['enum'] = $enum;
		return $this->add($name, $label, 'enum', $options);
	}

	public function date(string $name, string $label, array $options = []): static {
		return $this->add($name, $label, 'date', $options);
	}

	public function number(string $name, string $label, array $options = []): static {
		return $this->add($name, $label, 'number', $options);
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
		if (!empty($param->format) && !is_string($param->format) && is_callable($param->format))
			return call_user_func($param->format, $value);

		return match ($param->type) {
			'enum' => call_user_func([$param->enum, 'getLabel'], $value),
			'date' => $this->formatDate($value, $param),
			default => $value,
		};
	}

	protected function formatDate($value, $param) {
		if ($value instanceof \DateTime)
			return $value->format($param->format ?? static::$defaults['dateFormat']);

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
				. '>' . $text . '</a>';

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
