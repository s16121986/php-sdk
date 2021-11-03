<?php

namespace Gsdk\Grid\Data;

use Illuminate\Database\Eloquent\Builder;
use Paginator;

class Data {

	protected $dataEntity;
	protected $data;
	protected $paginator;
	protected array $params = [
		'orderby' => null,
		'sortorder' => 'asc'
	];

	public function __construct($data = null) {
		$this->set($data);
	}

	public function __get($name) {
		switch ($name) {
			case 'paginator':
				return $this->paginator;
		}
		return ($this->params[$name] ?? null);
	}

	public function paginator($paginator = null): Paginator {
		if (null === $paginator) {
			if (null === $this->paginator)
				$this->paginator = new Paginator();
		} else if (is_numeric($paginator))
			$this->paginator = new Paginator($paginator);
		else if ($paginator instanceof Paginator)
			$this->paginator = $paginator;

		return $this->paginator;
	}

	public function setParams($params) {
		foreach ($params as $k => $v) {
			$this->params[$k] = $v;
		}
	}

	public function getParams(): array {
		$params = $this->params;
		if ($this->paginator && $this->paginator->getCount()) {
			$params['start-index'] = $this->paginator->getStartIndex();
			$params['max-results'] = $this->paginator->step;
		}
		return $params;
	}

	public function getData() {
		return $this->dataEntity;
	}

	public function set($data): static {
		$this->dataEntity = $data;

		return $this;
	}

	public function get() {
		if (null !== $this->data)
			return $this->data;

		$data = $this->dataEntity;
		if ($data instanceof Builder) {
			if ($this->paginator)
				$this->paginator->query($data);

			$this->data = $data->get();
		} else if (is_iterable($data))
			$this->data = $data;
		else
			$this->data = [];

		return $this->data;
	}

	public function load($params = []) {
		$q = $_GET;
		foreach (['orderby', 'sortorder'] as $k) {
			if (isset($q[$k]) && $q[$k]) {
				$params[$k] = $q[$k];
			}
		}
		$this->setParams($params);
		if ($this->paginator) {
			$this->paginator->setCount($this->count());
		}
		$params = $this->getParams();
		$this->set($this->api->select($params)->getItems());
		return $this->data;
	}

	public function count(): int {
		return $this->data ? count($this->data) : 0;
	}

	public function order($name, $direction = 'asc') {
		return $this->setParams([
			'orderby' => $name,
			'sortorder' => $direction
		]);
	}

	public function isEmpty(): bool {
		return empty($this->get());
	}

}
