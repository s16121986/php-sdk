<?php

namespace Gsdk\Sitemap\Tag;

use Gsdk\Sitemap\AbstractParent;

class Sitemap extends AbstractParent {

	protected $tag = 'sitemap';
	
	protected function init() {
		$this
				->addTag('loc')
				->addTag('lastmod');
	}
	
	

}
