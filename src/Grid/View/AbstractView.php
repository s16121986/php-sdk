<?php

namespace Gsdk\Grid\View;

use Gsdk\Grid\Grid;

abstract class AbstractView {

	protected $grid;
	protected $config = [];

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
		$this->grid->setParams($this->grid->sorting->get());

		if ($this->grid->isEmpty())
			return '<div class="grid-empty-text">' . $this->grid->emptyGridText . '</div>';

		$html = $this->_render();
		$data = $this->grid->getData();
		if (($paginator = $data->paginator))
			$html .= $paginator->render();

		return $html;
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

			$cls = 'App\Services\Grid\Feature\\' . ucfirst($name);
			if (!class_exists($cls, true))
				throw new \Exception('Feature not exists');

			$features[$name] = new $cls($this->grid);
		}

		return $features;
	}

	protected function getColumnText($column, $row) {
		$dataValue = isset($row->{$column->name}) ? $row->{$column->name} : null;

		if ($column->renderer)
			$columnValue = call_user_func_array($column->renderer, [$row, $column->formatValue($dataValue, $row), $column->params]);
		else if (method_exists($column, 'renderer'))
			$columnValue = call_user_func_array([$column, 'renderer'], [$row, $dataValue, $column->params]);
		else
			$columnValue = $column->formatValue($dataValue, $row);

		if (null === $columnValue || '' === $columnValue)
			return $column->emptyText;

		if ($column->href) {
			$href = preg_replace_callback('/{(.+)}/', function ($m) use ($row) {
				return $row->{$m[1]} ?? '';
			}, $column->href);
			return '<a href="' . $href . '"' . ($column->hrefTarget ? ' target="' . $column->hrefTarget . '"' : '') . '>' . $columnValue . '</a>';
		}

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
			$html .= '<a href="' . $sorting->columnUrl($column) . '">';
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
