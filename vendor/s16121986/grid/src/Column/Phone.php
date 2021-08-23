<?php
namespace Corelib\Grid\Column;

class Phone extends AbstractColumn{

	protected $_options = array(
		'href' => 'tel:%value%'
	);
	
}