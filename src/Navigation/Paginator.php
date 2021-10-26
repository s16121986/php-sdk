<?php

namespace Gsdk\Navigation;

use Illuminate\Database\Eloquent\Builder;
use stdClass;

class Paginator {

	protected $options = [
		'step' => 10,
		'pagesStep' => 4,
		'prevText' => '{{lang:Prev}}',
		'nextText' => '{{lang:Next}}',
		'baseUrl' => null,
		'queryParam' => 'p',
		'view' => 'layouts.paginator'
	];

	protected $current;

	protected $count = 0;

	protected $query;

	public function __get($name) {
		return ($this->options[$name] ?? null);
	}

	public function __set($name, $value) {
		$this->options[$name] = $value;
	}

	public function __construct($options = null) {
		if (is_int($options))
			$this->setOptions(['step' => $options]);
		else if ($options)
			$this->setOptions($options);
	}

	public function setOptions($options): static {
		foreach ($options as $k => $v) {
			$this->options[$k] = $v;
		}
		return $this;
	}

	public function getQuery($name, $default = null) {
		return ($_GET[$name] ?? $default);
	}

	public function setStep($step): static {
		$this->options['step'] = $step;
		return $this;
	}

	public function setCount($count): static {
		$this->count = $count;
		return $this;
	}

	public function getCount(): int {
		return $this->count;
	}

	public function getStartIndex(): int {
		return ($this->getCurrentPage() - 1) * $this->step;
	}

	public function getCurrentPage(): int {
		if (null !== $this->current)
			return $this->current;

		$this->current = (int)$this->getQuery($this->queryParam);
		if ($this->current > $this->getPageCount())
			$this->current = $this->getPageCount();
		else if ($this->current < 1)
			$this->current = 1;

		return $this->current;
	}

	public function getPageCount(): int {
		return ceil($this->count / $this->step);
	}

	public function link($page, $text = null): string {
		if (null === $text)
			$text = $page;

		$query = $_SERVER['QUERY_STRING'];
		if ($query)
			$query = '?' . $query;

		$url = $this->baseUrl;
		if (null === $url) {
			$url = $_SERVER['REQUEST_URI'];
			if (false !== ($pos = strpos($url, '?')))
				$url = substr($url, 0, $pos);
			$this->baseUrl = $url;
		}
		$params = $_GET;
		if ($page == 1)
			unset($params[$this->queryParam]);
		else
			$params[$this->queryParam] = $page;

		if ($params)
			$url .= '?' . http_build_query($params);

		return '<a href="' . $url . '">' . $text . '</a>';
	}

	private function getPages(): ?stdClass {
		$pageCount = $this->getPageCount();
		if ($pageCount <= 1)
			return null;

		$pages = new stdClass();
		$pages->count = $this->count;
		$pages->step = $this->step;
		$pages->first = 1;
		$pages->current = $this->getCurrentPage();
		$pages->last = $pageCount;
		$pages->previous = null;
		$pages->next = null;

		if ($pages->current - 1 > 0)
			$pages->previous = $pages->current - 1;

		if ($pages->current + 1 <= $pageCount)
			$pages->next = $pages->current + 1;

		$firstPageInRange = $pages->current - $this->pagesStep;
		$lastPageInRange = $pages->current + $this->pagesStep;
		if ($firstPageInRange <= 2)
			$firstPageInRange = 1;

		if ($lastPageInRange > $pages->last - 2)
			$lastPageInRange = $pages->last;

		$pagesInRange = [];
		for ($i = $firstPageInRange; $i <= $lastPageInRange; $i++) {
			$pagesInRange[] = $i;
		}

		$pages->pagesInRange = $pagesInRange;
		$pages->firstPageInRange = $firstPageInRange;
		$pages->lastPageInRange = $lastPageInRange;

		return $pages;
	}

	public function query(Builder $query): static {
		$count = $query->count();
		$this->setCount($count);
		$query
			->limit($this->step)
			->offset($this->getStartIndex());
		return $this;
	}

	public function render($view = null) {
		$pages = $this->getPages();
		if (!$pages)
			return '';

		return view($view ?? $this->view, [
			'pages' => $pages,
			'paginator' => $this
		]);
	}

}