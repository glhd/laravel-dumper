<?php

namespace Glhd\LaravelDumper\Casters;

use Closure;
use Glhd\LaravelDumper\Support\Properties;
use Symfony\Component\VarDumper\Cloner\AbstractCloner;
use Symfony\Component\VarDumper\Cloner\Stub;

abstract class Caster
{
	public static array $targets = [];
	
	protected static bool $enabled = true;
	
	public static function register($app): void
	{
		if ($app instanceof Illuminate\Contracts\Foundation\Application || $app instanceof \Laravel\Lumen\Application) {
			$app->singleton(static::class);
		
			foreach (static::$targets as $target) {
				AbstractCloner::$defaultCasters[$target] = self::callback(static::class);
			}
		} else {
			// Maybe do some errorhandling here
		}
	}
	
	public static function callback($caster): Closure
	{
		return static function($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0) use ($caster) {
			$instance = $caster instanceof Caster
				? $caster
				: app($caster);
			
			return self::$enabled
				? $instance->cast($target, new Properties($properties), $stub, $is_nested, $filter)
				: $properties;
		};
	}
	
	public static function disable(): void
	{
		self::$enabled = false;
	}
	
	public static function enable(): void
	{
		self::$enabled = true;
	}
	
	abstract public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array;
}
