<?php

namespace Gsdk\Format;

use Gsdk\Contracts\Format\Rule;

class Factory {

	protected array $formats = [];

	protected array $aliases = [];

	protected array $extensions = [];

	public function __call(string $name, array $arguments) {
		if (!isset($arguments[0]))
			throw new \Exception('Value argument required');

		if (str_starts_with($name, 'format'))
			$name = strtolower(substr($name, 6));

		return $this->ruleFormat($name, $arguments[0], $arguments[1] ?? null);
	}

	public function hasExtension(string $rule): bool {
		return isset($this->extensions[$rule]) || isset($this->aliases[$rule]);
	}

	public function extend(string $rule, $extension): static {
		$this->extensions[$rule] = $extension;

		return $this;
	}

	public function alias(string $alias, string $rule): static {
		$this->aliases[$alias] = $rule;

		return $this;
	}

	public function registerFormat(string $alias, string $format): static {
		$this->formats[$alias] = $format;

		return $this;
	}

	public function registerFormats(array $formats): static {
		$this->formats = array_merge($this->formats, $formats);

		return $this;
	}

	public function getFormat(?string $alias) {
		return $this->formats[$alias] ?? $alias;
	}

	protected function ruleFactory(string $name) {
		if (isset($this->aliases[$name]))
			$name = $this->aliases[$name];

		$extension = $this->extensions[$name];
		if ($extension instanceof Rule)
			return $extension;

		if (is_string($extension)) {
			if (!class_exists($extension))
				throw new \Exception('Rule class [' . $extension . '] undefined');

			return new $extension();
		}

		if (is_callable($extension))
			return new Rules\Custom($extension);

		throw new \Exception('Cant create extension [' . $name . ']');
	}

	protected function ruleFormat($ruleName, $value, $format = null): string {
		$rule = $this->ruleFactory($ruleName);

		return $rule->format($value, $format);
	}

}