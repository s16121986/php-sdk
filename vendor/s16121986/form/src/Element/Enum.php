<?php

namespace Corelib\Form\Element;

class Enum extends Select {

	public function getItems() {
		if (null === $this->_items) {
			$this->_items = [];
			$items = [];
			if ($this->enum)
				$items = call_user_func(['\\' . $this->enum, 'getLabels']);

			if (is_array($this->items))
				$items = array_merge($this->items, $items);

			foreach ($items as $k => $v) {
				$this->initItem($k, $v);
			}
		}
		return $this->_items;
	}

}
