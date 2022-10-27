<?php

namespace Gsdk\Support\Facades\Facades;

use Illuminate\Support\Facades\Facade;

class LastModified extends Facade {

	protected static function getFacadeAccessor(): string {
		return 'last-modified';
	}

}