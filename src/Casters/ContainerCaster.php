<?php

namespace Glhd\LaravelDumper\Casters;

use Glhd\LaravelDumper\Support\Properties;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\VarDumper\Cloner\Stub;

class ContainerCaster extends Caster
{
	public static array $targets = [Container::class];
	
	protected array $included = [
		'bindings',
		'aliases',
		'resolved',
		'extenders',
	];
	
	public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		if ($is_nested) {
			$stub->cut += $properties->count();
			return [];
		}
		
		return $properties
			->only($this->included)
			->reorder($this->included)
			->applyCutsToStub($stub, $properties)
			->all();
	}
}
