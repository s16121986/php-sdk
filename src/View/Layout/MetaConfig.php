<?php

namespace Gsdk\View\Layout;

use Illuminate\Support\Facades\App;

trait MetaConfig {

	protected $version = '';

	public function title($title): static {
		return $this->setOption('title', $title);
	}

	public function ss($style, $script = null): static {
		return $this
			->setOption('style', $style)
			->setOption('script', $script ?? $style);
	}

	public function style($style): static {
		return $this->setOption('style', $style);
	}

	public function script($script): static {
		return $this->setOption('script', $script);
	}

	protected function addDefaultMeta() {
		$head = $this->head;
		$head
			->addLinkRel('icon', '/favicon.ico')
			->addMetaHttpEquiv('Content-Type', 'text/html; charset=utf-8')
			->addMetaHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
			->addMetaHttpEquiv('Content-language', App::currentLocale())
			->addMetaName('viewport', 'width=device-width, initial-scale=1')
			->addMetaName('csrf-token', csrf_token());
	}

	protected function addPageData() {
		$this->addStyles();

		if (isset($this->options['title'])) {
			$this->setH1($this->options['title']);
			$this->head->setTitle($this->options['title']);
		} else if (isset($this->options['page']) && ($page = $this->options['page']))
			$this->head->setTitle($page->title);
	}

	protected function addStyles() {
		$style = $this->options['style'] ?? 'main';

		$this->head
			->addScript(($this->options['script'] ?? $style ?? 'main') . '.js?' . $this->version, ['defer' => true])
			->addLinkRel('preload', '/css/' . $style . '.css?' . $this->version, ['as' => 'style'])
			->addStyle($style . '.css?' . $this->version);

		//$this->head->addStyle('print.css?' . $this->version);
	}

}
