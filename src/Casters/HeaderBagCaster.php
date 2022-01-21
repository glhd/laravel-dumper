<?php

namespace Glhd\LaravelDumper\Casters;

use Glhd\LaravelDumper\Support\Key;
use Glhd\LaravelDumper\Support\Properties;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\VarDumper\Cloner\Stub;

class HeaderBagCaster extends Caster
{
	public static array $targets = [HeaderBag::class];
	
	/**
	 * @param HeaderBag $target
	 * @param \Glhd\LaravelDumper\Support\Properties $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		$result = collect($target->all())
			->map(function(array $headers) {
				return 1 === count($headers)
					? $headers[0]
					: $headers;
			})
			->mapWithKeys(fn($value, $key) => [Key::virtual($key) => $value])
			->all();
		
		$result[Key::protected('cacheControl')] = $properties[Key::protected('cacheControl')];
		
		return $result;
	}
}
