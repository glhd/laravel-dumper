<?php

namespace Glhd\LaravelDumper\Casters;

use Illuminate\Database\ConnectionInterface;
use Symfony\Component\VarDumper\Cloner\Stub;

class DatabaseConnectionCaster extends Caster
{
	public static array $targets = [ConnectionInterface::class];
	
	/**
	 * @param ConnectionInterface $target
	 * @param array $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		$key = Key::protected('config');
		
		if (!isset($properties[$key])) {
			return $properties;
		}
		
		$config = $properties[$key];
		
		$stub->cut += count($properties);
		
		return [
			Key::virtual('name') => $config['name'],
			Key::virtual('database') => $config['database'],
			Key::virtual('driver') => $config['driver'],
		];
	}
}
