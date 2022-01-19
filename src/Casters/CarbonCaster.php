<?php

namespace Glhd\LaravelDumper\Casters;

use Carbon\CarbonInterface;
use Symfony\Component\VarDumper\Caster\Caster as BaseCaster;
use Symfony\Component\VarDumper\Cloner\Stub;

class CarbonCaster extends Caster
{
	public static array $targets = [CarbonInterface::class];
	
	/**
	 * @param CarbonInterface $target
	 * @param array $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		$date_key = Key::virtual('date');
		
		$result = [$date_key => $target->format($this->getFormat($target))];
		
		if (!$is_nested) {
			$result += $properties;
		}
		
		$result = BaseCaster::filter($result, BaseCaster::EXCLUDE_NULL, [], $filtered);
		
		$stub->cut += $filtered + count($properties) - count($result);
		
		return $result;
	}
	
	protected function getFormat(CarbonInterface $target): string
	{
		// Only include microseconds if we have it
		$microseconds = '000000' === $target->format('u')
			? ''
			: '.u';
		
		// Only include timezone name ("America/New_York") if we have it
		$timezone = $target->getTimezone()->getLocation()
			? ' e (P)'
			: ' P';
		
		return 'Y-m-d H:i:s'.$microseconds.$timezone;
	}
}
