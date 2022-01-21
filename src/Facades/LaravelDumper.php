<?php

namespace Glhd\LaravelDumper\Facades;

use Glhd\LaravelDumper\Support\CustomDumperRegistrar;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Glhd\LaravelDumper\Casters\CustomCaster for(string $class_name)
 */
class LaravelDumper extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return CustomDumperRegistrar::class;
	}
}
