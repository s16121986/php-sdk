<?php

namespace Gsdk\Http\LastModified;

use Illuminate\Support\ServiceProvider;

class LastModifiedServiceProvider extends ServiceProvider {

	public function register(): void {
		$this->app->singleton('last-modified', fn() => new LastModified());
	}

	public function boot(): void {

	}

}