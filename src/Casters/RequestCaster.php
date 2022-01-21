<?php

namespace Glhd\LaravelDumper\Casters;

use Glhd\LaravelDumper\Support\Properties;
use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Cloner\Stub;

class RequestCaster extends Caster
{
	public static array $targets = [Request::class];
	
	/**
	 * @param Request $target
	 * @param \Glhd\LaravelDumper\Support\Properties $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		return $properties
			->except(['userResolver', 'routeResolver'])
			->filter()
			->applyCutsToStub($stub, $properties)
			->all();
	}
}
