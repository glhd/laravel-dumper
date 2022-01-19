<?php

namespace Glhd\LaravelDumper\Casters;

use Illuminate\Contracts\Container\Container;
use Symfony\Component\VarDumper\Caster\Caster as BaseCaster;
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
	
	public function cast($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		$result = [];
		
		if (!$is_nested) {
			// We want to do this in a foreach so that we can re-order the list as well as filter it
			foreach ($this->included as $property) {
				$index = Key::protected($property);
				if (isset($properties[$index])) {
					$result[$index] = $properties[$index];
				}
			}
		}
		
		$result = BaseCaster::filter($result, BaseCaster::EXCLUDE_EMPTY, [], $filtered);
		$stub->cut += $filtered + count($properties) - count($result);
		
		return $result;
	}
}
