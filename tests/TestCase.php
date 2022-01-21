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
		
		$this->writeDiff($data, $before, $after);
		
		return $after;
	}
	
	protected function writeDiff($data, $before, $after)
	{
		$fs = new Filesystem();
		$path = __DIR__.'/../diffs';
		
		[$_, $_, $_, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
		$test_name = $caller['function'];
		
		$before_file = "{$path}/{$test_name}-before.txt";
		$after_file = "{$path}/{$test_name}-after.txt";
		
		try {
			$fs->put($before_file, "{$before}\n");
			$fs->put($after_file, "{$after}\n");
			
			$diff = (new Process(['/usr/bin/diff', '-c', $before_file, $after_file]));
			$diff->run();
			$fs->put("{$path}/{$test_name}.diff", str_replace([$before_file, $after_file], ['before', 'after'], $diff->getOutput()));
		} finally {
			$fs->delete([$before_file, $after_file]);
		}
	}
}
