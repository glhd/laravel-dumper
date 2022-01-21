<?php

namespace Glhd\LaravelDumper\Support;

use Glhd\LaravelDumper\Casters\Caster;
use Glhd\LaravelDumper\Casters\CustomCaster;
use Symfony\Component\VarDumper\Cloner\AbstractCloner;
use Symfony\Component\VarDumper\Cloner\Stub;

class CustomDumperRegistrar
{
	public function for(string $class_name): CustomCaster
	{
		$caster = new CustomCaster();
		
		AbstractCloner::$defaultCasters[$class_name] = static function($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0) use ($caster) {
			return Caster::$enabled
				? $caster->cast($target, new Properties($properties), $stub, $is_nested, $filter)
				: $properties;
		};
		
		return $caster;
	}
}
