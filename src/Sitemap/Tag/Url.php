<?php

namespace Gsdk\Sitemap\Tag;

use Gsdk\Sitemap\AbstractParent;

class Url extends AbstractParent {

	protected $tag = 'url';
	
	protected function init() {
		$this
				->addTag('loc')
				->addTag('lastmod')
				->addTag('changefreq')
				->addTag('priority');
	}
	
	public function addAlternate($hreflang, $href) {
		$this->tags[] = new Tag('xhtml:link', [
			'rel' => 'alternate',
			'hreflang' => $hreflang,
			'href' => $href
		]);
	}

}
