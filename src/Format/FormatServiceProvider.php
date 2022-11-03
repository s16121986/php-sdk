<?php

namespace Gsdk\Format;

use Gsdk\Format\Rules;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FormatServiceProvider extends ServiceProvider implements DeferrableProvider {

	protected $formats = [];

	protected $defaultRules = [
		'string' => Rules\Text::class,
		'number' => Rules\Number::class,
		'boolean' => Rules\Boolean::class,
		'date' => Rules\Date::class,
		'filesize' => Rules\FileSize::class
	];

	protected $rules = [];

	public function register() {
		$this->registerFormatFactory();
	}

	protected function registerFormatFactory() {
		$this->app->singleton('format', function ($app) {
			$factory = new Factory();//$app['translator'], $app

			//set configs
			$this->registerFormats($factory);
			$this->registerDefaultRules($factory);
			$this->registerRules($factory);
			$this->registerAliases($factory);

			return $factory;
		});
	}

	protected function registerFormats($factory) {
		$factory->registerFormats($this->formats);
	}

	protected function registerDefaultRules($factory) {
		foreach ($this->defaultRules as $k => $v) {
			$factory->extend($k, $v);
		}
	}

	protected function registerRules($factory) {
		foreach ($this->rules as $k => $v) {
			$factory->extend($k, $v);
		}
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