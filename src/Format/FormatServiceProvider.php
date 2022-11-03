<?php

namespace Gsdk\Format;

use Gsdk\Format\Rules;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FormatServiceProvider extends ServiceProvider implements DeferrableProvider {

	protected $formats = [];

	public function register() {
		$this->registerFormatFactory();
	}

	protected function registerFormatFactory() {
		$this->app->singleton('format', function ($app) {
			$factory = new Factory();//$app['translator'], $app

			//set configs
			$this->registerFormats($factory);
			$this->registerDefaultRules($factory);
			$this->registerAliases($factory);

			return $factory;
		});
	}

	protected function registerFormats($factory) {
		$factory->registerFormats($this->formats);
	}

	protected function registerDefaultRules($factory) {
		$factory->extend('string', Rules\Text::class);
		$factory->extend('number', Rules\Number::class);
		$factory->extend('boolean', Rules\Boolean::class);
		$factory->extend('date', Rules\Date::class);
		$factory->extend('filesize', Rules\FileSize::class);
	}

	protected function registerAliases($factory) {
		$factory->alias('text', 'string');
		//$factory->alias('float', 'number');
		//$factory->alias('bool', 'boolean');
	}

	public function provides() {
		return [
			'format'
		];
	}

}