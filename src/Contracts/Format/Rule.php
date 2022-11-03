<?php

namespace Gsdk\Contracts\Format;

interface Rule {

	public function format($value, $format = null): string;

}