<?php

namespace Glhd\LaravelDumper\Casters;

use BadMethodCallException;
use Closure;
use Glhd\LaravelDumper\Support\Properties;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\VarDumper\Cloner\AbstractCloner;
use Symfony\Component\VarDumper\Cloner\Stub;

class CustomCaster extends Caster
{
	protected array $operations = [];
	
	public static function for(string $class_name): CustomCaster
	{
		$caster = new self();
		
		AbstractCloner::$defaultCasters[$class_name] = Caster::callback($caster);
		
		return $caster;
	}
	
	public static function register(Application $app): void
	{
		throw new BadMethodCallException('Custom casters must be registered via the LaravelDumper facade.');
	}
	
	public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		return collect($this->operations)
			->reduce(fn(Properties $properties, Closure $operation) => $operation($properties, $target), $properties)
			->applyCutsToStub($stub, $properties)
			->all();
	}
	
	public function reorder(array $rules): CustomCaster
	{
		$this->operations[] = fn(Properties $properties) => $properties->reorder($rules);
		
		return $this;
	}
	
	public function filter(callable $filter = null): CustomCaster
	{
		$this->operations[] = fn(Properties $properties) => $properties->filter($filter);
		
		return $this;
	}
	
	public function dynamic(string $key, Closure $callback): CustomCaster
	{
		$this->operations[] = fn(Properties $properties, $target) => $properties->putDynamic($key, $callback($target, $properties));
		
		return $this;
	}
	
	public function virtual(string $key, Closure $callback): CustomCaster
	{
		$this->operations[] = fn(Properties $properties, $target) => $properties->putVirtual($key, $callback($target, $properties));
		
		return $this;
	}
	
	public function only($keys): CustomCaster
	{
		$this->operations[] = fn(Properties $properties) => $properties->only($keys);
		
		return $this;
	}
	
	public function except($keys): CustomCaster
	{
		$this->operations[] = fn(Properties $properties) => $properties->except($keys);
		
		return $this;
	}
}
