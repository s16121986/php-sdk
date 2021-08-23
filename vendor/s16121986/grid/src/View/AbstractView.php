<?php

namespace Corelib\Grid\View;

use Grid;

abstract class AbstractView {

	protected $grid;
	protected $config = [];
	protected $_url = null;

	public function __construct(Grid $grid) {
		$this->grid = $grid;
		if ($grid->viewConfig) {
			$this->config = array_merge($this->config, $grid->viewConfig);
		}
	}

	public function __get($name) {
		if (isset($this->config[$name])) {
			return $this->config[$name];
		}
		return $this->grid->$name;
	}

	public function render() {
		if ($this->grid->isEmpty())
			return '<div class="grid-empty-text">' . $this->grid->emptyGridText . '</div>';

		return $this->_render();
	}

	public function initFeatures(): array {
		if (!$this->grid->features)
			return [];

		$features = [];
		$featuresTemp = $this->grid->features;
		if (!is_array($featuresTemp))
			$featuresTemp = [$featuresTemp];

		foreach ($featuresTemp as $name) {
			if (!preg_match('/^[a-z_]+$/i', $name))
				throw new \Exception('Invalid feature');

			$cls = 'Corelib\Grid\Feature\\' . ucfirst($name);
			if (!class_exists($cls, true))
				throw new \Exception('Feature not exists');

			$features[$name] = new $cls($this->grid);
		}

		return $features;
	}

	protected function getColumnText($column, $row) {
		$dataValue = isset($row->{$column->name}) ? $row->{$column->name} : null;

		$columnValue = $column->formatValue($dataValue, $row);

		if ($column->renderer)
			$columnValue = call_user_func_array($column->renderer, [$row, $columnValue, $column->params]);

		if (null === $columnValue || '' === $columnValue)
			return $column->emptyText;

		//$row['value'] = $value;
		if ($column->href)
			return '<a href="' . \Format::formatTemplate($column->href, $row) . '"'
				. ($column->hrefTarget ? ' target="' . $column->hrefTarget . '"' : '') . '>' . $columnValue . '</a>';

		return $columnValue;
	}

	protected function getColumnTd($column, $row): string {
		$html = '<td class="' . $this->getColumnClass($column) . '">';
		//$dataValue = isset($row->{$column->name}) ? $row->{$column->name} : null;
		$html .= $this->getColumnText($column, $row);
		$html .= '</td>';
		return $html;
	}

	protected function getColumnTh($column): string {
		$sorting = $this->grid->sorting;

		$html = '<th class="' . $this->getColumnClass($column) . '">';
		if ($column->order) {
			$html .= '<div class="column-inner">';
			$html .= '<a href="' . $this->orderurl($column) . '">';
			$html .= $column->text;
			$html .= '</a>';
			if ($sorting->orderby == $column->name)
				$html .= '<div class="grid-sorted-arrow"></div>';

			$html .= '</div>';
		} else
			$html .= $column->text;

		$html .= '</th>';
		return $html;
	}

	protected function getColumnClass($column): string {
		$sorting = $this->grid->sorting;

		$cls = 'column-' . $column->type;

		if ($column->type !== $column->name)
			$cls = ' column-' . $column->name;

		if ($column->class)
			$cls .= ' ' . $column->class;

		if ($column->order && $sorting->orderby == $column->name)
			$cls .= ' column-sorted column-sorted-' . $sorting->sortorder;

		return $cls;
	}

	abstract protected function _render(): string;

}