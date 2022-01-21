<?php

namespace Glhd\LaravelDumper\Tests;

use Glhd\LaravelDumper\Casters\Caster;
use Glhd\LaravelDumper\LaravelDumperServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Process\Process;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

abstract class TestCase extends Orchestra
{
	use VarDumperTestTrait {
		getDump as baseGetDump;
	}
	
	protected function getPackageProviders($app)
	{
		return [
			LaravelDumperServiceProvider::class,
		];
	}
	
	protected function getPackageAliases($app)
	{
		return [];
	}
	
	protected function getApplicationTimezone($app)
	{
		return 'America/New_York';
	}
	
	protected function getDump($data, $key = null, int $filter = 0): ?string
	{
		Caster::disable();
		$before = $this->baseGetDump($data, $key, $filter);
		
		Caster::enable();
		$after = $this->baseGetDump($data, $key, $filter);
		
		$this->writeDiff($before, $after);
		
		return $after;
	}
	
	protected function writeDiff($before, $after)
	{
		$fs = new Filesystem();
		
		[$_, $_, $_, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
		$test_name = $caller['function'];
		
		$before_file = tempnam(sys_get_temp_dir(), $test_name.'1');
		$after_file = tempnam(sys_get_temp_dir(), $test_name.'2');
		
		try {
			$fs->put($before_file, $before);
			$fs->put($after_file, $after);
			
			$diff = (new Process(['/usr/bin/diff', $before_file, $after_file]));
			$diff->run();
			$fs->put(__DIR__.'/../diffs/'.$test_name.'.diff', $diff->getOutput());
		} finally {
			$fs->delete([$before_file, $after_file]);
		}
	}
}
