<?php
namespace Gsdk\Form\Element;

use Gsdk\Form\Element;

abstract class Xhtml extends Element{

	protected function getJs($options = array()) {
		return '<script type="text/javascript">$(document).ready(function(){Form.initElement("' . $this->id . '", "' . $this->type . '",' . json_encode($options) . ');});</script>';
	}

}
