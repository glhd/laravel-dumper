<?php

namespace Glhd\LaravelDumper\Casters;

use Glhd\LaravelDumper\Support\Key;
use Glhd\LaravelDumper\Support\Properties;
use Illuminate\Database\ConnectionInterface;
use Symfony\Component\VarDumper\Cloner\Stub;

class DatabaseConnectionCaster extends Caster
{
	public static array $targets = [ConnectionInterface::class];
	
	/**
	 * @param ConnectionInterface $target
	 * @param \Glhd\LaravelDumper\Support\Properties $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		if (!is_array($config = $properties->getProtected('config'))) {
			return $properties->all();
		}
		
		$stub->cut += count($properties);
		
		return [
			Key::virtual('name') => $config['name'],
			Key::virtual('database') => $config['database'],
			Key::virtual('driver') => $config['driver'],
		];
	}
}
