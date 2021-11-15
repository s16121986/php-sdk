<?php

namespace Gsdk\Grid\View;

class Table extends AbstractView {

	protected function _render(): string {
		$html = '<table class="' . $this->grid->class . '">';
		if (false !== $this->grid->header)
			$html .= $this->renderTHead();

		$html .= $this->renderTBody();
		$html .= $this->renderTFoot();
		$html .= '</table>';
		return $html;
	}

	protected function renderTHead(): string {
		$data = $this->grid->getData();
		$html = '<thead>';
		$html .= '<tr>';
		foreach ($this->grid->getColumns() as $column) {
			$html .= $this->getColumnTh($column);
		}
		$html .= '</tr>';
		$html .= '</thead>';
		return $html;
	}

	protected function renderTBody(): string {
		$html = '<tbody>';
		foreach ($this->grid->getData()->get() as $row) {
			$row = (object)$row;
			$html .= '<tr>';
			foreach ($this->grid->getColumns() as $column) {
				$html .= $this->getColumnTd($column, $row);
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';

		return $html;
	}

	protected function renderTFoot(): string {
		$columns = $this->grid->getColumns();
		$html = '';
		foreach ($this->initFeatures() as $feature) {
			$html .= '<tr class="grid-feature-summary">';
			foreach ($columns as $column) {
				$value = $feature->getColumnValue($column);
				$html .= '<td class="' . $this->getColumnClass($column) . '">';
				if (null === $value)
					$html .= '&nbsp;';
				else
					$html .= $this->getColumnText($column, $value); //TODO fixit
				$html .= '</td>';
			}
			$html .= '</tr>';
		}
		return ($html ? '<tfoot>' . $html . '</tfoot>' : '');
	}

}
