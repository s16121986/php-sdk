<?php

namespace Corelib\Grid\Util;

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

		if (!$set('params', ['orderParams', 'sortingParams']))
			$this->params = $_GET;

		if (!$set('orderby', ['orderby']))
			$this->orderby = isset($this->params[self::PARAM_ORDERBY]) ? $this->params[self::PARAM_ORDERBY] : null;

		if (!$set('sortorder', ['sortorder']))
			$this->sortorder = isset($this->params[self::PARAM_SORTORDER]) && $this->params[self::PARAM_SORTORDER] === 'desc' ? 'desc' : 'asc';
	}

	public function __get($name) {
		return isset($this->$name) ? $this->$name : null;
	}

	public function setOptions() {

	}

	public function columnUrl($columnName): string {
		$dir = 'asc';
		if ($this->orderby === $columnName)
			$dir = $this->sortorder == 'asc' ? 'desc' : 'asc';

		$q = $this->params;

		$q[self::PARAM_ORDERBY] = $columnName;
		$q[self::PARAM_SORTORDER] = $dir;

		return $this->url . '?' . http_build_query($q);
	}

	public function setUrl(string $url) {
		$this->url = $url;
	}

	public function getParams(): ?array {
		return $this->params;
	}

}