<?php

namespace Gsdk\Predicator;

class Predicator {

	protected array $params = [];

	protected array $predicates = [];

	public function setParams($params): static {
		foreach ($params as $name => $value) {
			$this->setParam($name, $value);
		}
		return $this;
	}

	public function setParam($name, $value): static {
		$this->params[$name] = $value;
		return $this;
	}

	public function getParam($name) {
		return $this->params[$name] ?? null;
	}

	public function hasParam($name): bool {
		return isset($this->params[$name]);
	}

	public function removeParam($name): static {
		unset($this->params[$name]);
		return $this;
	}

	public function addPredicate($name, $predicate): static {
		$this->predicates[$name] = $predicate;
		return $this;
	}

	public function predicate($name, $identifier, array $options = []): static {
		return $this->predicateInt($name, $identifier, $options);
	}

	public function predicateInt($name, $identifier, array $options = []): static {
		return $this->addPredicate($name, new Predicate\IntPredicate($identifier, $options));
	}

	public function predicateDate($name, $identifier, array $options = []): static {
		return $this->addPredicate($name, new Predicate\DatePredicate($identifier, $options));
	}

	public function predicateFloat($name, $identifier, array $options = []): static {
		return $this->addPredicate($name, new Predicate\FloatPredicate($identifier, $options));
	}

	public function predicateBool($name, $identifier, array $options = []): static {
		return $this->addPredicate($name, new Predicate\BoolPredicate($identifier, $options));
	}

	public function predicateString($name, $identifier, array $options = []): static {
		return $this->addPredicate($name, new Predicate\StringPredicate($identifier, $options));
	}

	public function when(string $name, callable $callback, array $options = []): static {
		return $this->addPredicate($name, new Predicate\WhenPredicate($callback, $options));
	}

	public function quicksearch(array $options = []): static {
		return $this->when('quicksearch', function ($query, $term) {
			$query->quicksearch($term);
		}, $options);
	}

	public function query($query): static {
		foreach ($this->predicates as $name => $predicate) {
			if (!$this->hasParam($name))
				continue;

			$predicate->query($query, $this->getParam($name));
		}

		$this->queryOrderBy($query);

		$this->queryLimit($query);

		$this->queryOffset($query);

		return $this;
	}

	private function queryOrderBy($query): void {
		if ($this->hasParam('order')) {
			$orderParam = $this->getParam('order');
			$s = explode(' ', $orderParam);
			$query->orderBy($s[0], $s[1] ?? 'asc');
		} else if ($this->hasParam('orderby'))
			$query->orderBy($this->getParam('orderby'), $this->getParam('sortorder') ?? 'asc');
	}

	private function queryLimit($query): void {
		if (!$this->hasParam('limit'))
			return;

		$query->limit((int)$this->params['limit']);
	}

	private function queryOffset($query): void {
		if (!$this->hasParam('offset'))
			return;

		$query->offset((int)$this->params['offset']);
	}

}
