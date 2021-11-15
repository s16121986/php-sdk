<?php

namespace Gsdk\Grid\View;

class Tree extends Table {

	protected $config = [
		'parentIndex' => 'parent_id',
		'treeIndent' => '&nbsp;&nbsp;&nbsp;&nbsp;',
		'indentColumn' => 'name'
	];

	protected function renderTBody(): string {
		$html = '<tbody>';
		$html .= $this->tree(null);
		$html .= '</tbody>';
		return $html;
	}

	private function tree($parentId, $level = 0): string {
		$html = '<tbody>';
		foreach ($this->grid->getData()->get() as $row) {
			$row = (object)$row;
			if ($row->{$this->parentIndex} != $parentId)
				continue;
			$html .= '<tr>';
			foreach ($this->grid->getColumns() as $column) {
				$html = '<td class="' . $this->getColumnClass($column) . '">';
				if ($column->name == $this->indentColumn)
					$html .= self::indentPad($this->treeIndent, $level);

				$html .= $this->getColumnText($column, $row);
				$html .= '</td>';
			}
			$html .= '</tr>';
			$html .= $this->tree($row->id, $level + 1);
		}
		$html .= '</tbody>';

		return $html;
	}

	private static function indentPad($indent, $count): string {
		return str_repeat($indent, $count);
	}

}