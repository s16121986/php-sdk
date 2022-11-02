<?php

namespace Gsdk\View;

class ParamsTable {

	protected $view;

	protected $tableClass = 'table-params';

	private array $params = [];

	private array $data = [];

	public function add(string $name, string $label, $format = 'text', array $options = []): static {
		$options['name'] = $name;
		$options['label'] = $label;
		$options['format'] = $format;
		$this->params[] = $options;
		return $this;
	}

	public function text(string $name, string $label): static {
		return $this->add($name, $label, 'text');
	}

	public function email(string $name, string $label): static {
		return $this->add($name, $label, 'email');
	}

	public function phone(string $name, string $label): static {
		return $this->add($name, $label, 'phone');
	}

	public function custom(string $name, string $label, callable $formatFunction): static {
		return $this->add($name, $label, $formatFunction);
	}

	public function enum(string $name, string $label, string $enum): static {
		return $this->add($name, $label, 'enum', ['enum' => $enum]);
	}

	public function data(array $data): static {
		$this->data = $data;

		return $this;
	}

	public function value($name, $value): static {
		$this->data[$name] = $value;
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

	public function __construct(array $data = null) {
		if ($data)
			$this->data = $data;
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

		return match ($param->format) {
			'email' => 'mailto:' . $value,
			'phone' => 'tel:' . preg_replace('/[^0-9+]/', '', $value),
			'url' => $value,
			default => null,
		};
	}

	protected function prepareValue($value, $param) {
		if (is_callable($param->format))
			return call_user_func($param->format, $value);

		if (!is_scalar($value))
			$value = serialize($value);

		return match ($param->format) {
			'enum' => call_user_func([$param->enum, 'getLabel'], $value),
			default => $value,
		};
	}

	protected function prepareEmpty($param) {
		return $param->emptyText ?? null;
	}

	protected function prepareText($value, $param): string {
		if (empty($value))
			return $this->prepareEmpty($param);

		$href = $this->getValueHref($value, $param);

		$value = $this->prepareValue($value, $param);

		$text = match ($param->format) {
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
		if (!array_key_exists($param->name, $this->data))
			return '';

		$text = $this->prepareText($this->data[$param->name], $param);
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