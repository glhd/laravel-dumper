<?php

namespace Glhd\LaravelDumper\Casters;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\VarDumper\Caster\Caster as BaseCaster;
use Symfony\Component\VarDumper\Cloner\Stub;

class ResponseCaster extends Caster
{
	public static array $targets = [Response::class];
	
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
		$result = BaseCaster::filter($properties, BaseCaster::EXCLUDE_NULL, [], $filtered);
		
		$stub->cut += $filtered;
		
		return $result;
	}
}
