<?php

namespace Gsdk\Grid\Util;

class Sorting {

	const PARAM_ORDERBY = 'orderby';
	const PARAM_SORTORDER = 'sortorder';

	protected ?string $url = null;
	protected ?array $params = null;
	protected ?string $orderby = null;
	protected ?string $sortorder = null;

	public function __construct(array $options = []) {
		$self = $this;

		$set = function ($param, array $gridOptions) use ($self, $options) {
			foreach ($gridOptions as $optionName) {
				if (!array_key_exists($optionName, $options))
					continue;

				$self->$param = $options[$optionName];
				return true;
			}
			return false;
		};

		if (!$set('url', ['orderUrl', 'sortingUrl'])) {
			$url = $_SERVER['REQUEST_URI'];
			if (false !== ($pos = strpos($url, '?')))
				$url = substr($url, 0, $pos);

			$this->url = $url;
		}

		$set('params', ['orderParams', 'sortingParams']);

		$set('orderby', ['orderby']);

		$set('sortorder', ['sortorder']);
	}

	public function __get($name) {
		return isset($this->$name) ? $this->$name : null;
	}

	public function fromRequest() {
		$params = $_GET;

		$this->params = $params;

		if (isset($params[self::PARAM_ORDERBY]))
			$this->orderby = $params[self::PARAM_ORDERBY];

		if (isset($params[self::PARAM_SORTORDER]))
			$this->sortorder = $params[self::PARAM_SORTORDER] === 'desc' ? 'desc' : 'asc';
	}

	public function orderBy($name, $order = 'asc'): static {
		$this->orderby = $name;
		$this->sortorder = $order;
		return $this;
	}

	public function columnUrl($column): string {
		$dir = 'asc';
		if ($this->orderby === $column->name)
			$dir = $this->sortorder == 'asc' ? 'desc' : 'asc';

		$q = $this->params;

		$q[self::PARAM_ORDERBY] = $column->name;
		$q[self::PARAM_SORTORDER] = $dir;

		return $this->url . '?' . http_build_query($q);
	}

	public function setUrl(string $url) {
		$this->url = $url;
	}

	public function get() {
		return $this->orderby ? [
			'orderby' => $this->orderby,
			'sortorder' => $this->sortorder
		] : [];
	}

	public function getParams(): ?array {
		return $this->params;
	}

}
