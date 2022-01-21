<?php

use Glhd\LaravelDumper\Casters\Caster;

if (!function_exists('ddf')) {
	function ddf(...$vars)
	{
		Caster::disable();
		
		dd(...$vars);
		
		exit(1);
	}
}

if (!function_exists('dumpf')) {
	function dumpf($var, ...$moreVars)
	{
		try {
			Caster::disable();
			return dump($var, ...$moreVars);
		} finally {
			Caster::enable();
		}
	}
}
