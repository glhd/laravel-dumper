<?php

namespace Glhd\LaravelDumper\Casters;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\VarDumper\Caster\Caster as BaseCaster;
use Symfony\Component\VarDumper\Cloner\Stub;

class RequestCaster extends Caster
{
	public static array $targets = [Request::class];
	
	/**
	 * @param Request $target
	 * @param array $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		$result = Arr::except($properties, [
			Key::protected('userResolver'),
			Key::protected('routeResolver'),
		]);
		
		$result = BaseCaster::filter($result, BaseCaster::EXCLUDE_NULL | BaseCaster::EXCLUDE_EMPTY, [], $filtered);
		
		$stub->cut += $filtered + count($properties) - count($result);
		
		return $result;
	}
}
