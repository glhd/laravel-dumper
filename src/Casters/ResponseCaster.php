<?php

namespace Glhd\LaravelDumper\Casters;

use Glhd\LaravelDumper\Support\Properties;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\VarDumper\Cloner\Stub;

class ResponseCaster extends Caster
{
	public static array $targets = [Response::class];
	
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
			->filter()
			->applyCutsToStub($stub, $properties)
			->all();
	}
}
