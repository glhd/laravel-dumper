<?php

namespace Glhd\LaravelDumper\Casters;

use Carbon\CarbonInterface;
use Glhd\LaravelDumper\Support\Properties;
use Symfony\Component\VarDumper\Cloner\Stub;

class CarbonCaster extends Caster
{
	public static array $targets = [CarbonInterface::class];
	
	/**
	 * @param CarbonInterface $target
	 * @param \Glhd\LaravelDumper\Support\Properties $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		return $properties
			->putVirtual('date', $target->format($this->getFormat($target)))
			->when($is_nested, fn(Properties $properties) => $properties->only('date'))
			->filter()
			->reorder(['date', '*'])
			->applyCutsToStub($stub, $properties)
			->all();
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
