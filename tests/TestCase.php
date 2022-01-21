<?php

namespace Glhd\LaravelDumper\Tests;

use Glhd\LaravelDumper\Casters\Caster;
use Glhd\LaravelDumper\Facades\LaravelDumper;
use Glhd\LaravelDumper\LaravelDumperServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
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
		return [
			'LaravelDumper' => LaravelDumper::class,
		];
	}
	
	protected function getApplicationTimezone($app)
	{
		return 'America/New_York';
	}
	
	protected function getDump($data, $key = null, int $filter = 0): ?string
	{
		if ('1' !== getenv('WRITE_DIFFS')) {
			return $this->baseGetDump($data, $key, $filter);
		}
		
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
		$path = __DIR__.'/../diffs';
		
		[$_, $_, $_, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
		$name = Str::of($caller['function'])->after('test_')->replace('_', '-');
		
		$before_file = "{$path}/{$name}.1.txt";
		$after_file = "{$path}/{$name}.2.txt";
		
		try {
			$fs->put($before_file, "{$before}\n");
			$fs->put($after_file, "{$after}\n");
			
			$diff = (new Process(['/usr/bin/diff', '-u', $before_file, $after_file]));
			$diff->run();
			$fs->put("{$path}/{$name}.diff", str_replace([$before_file, $after_file], ['Before', 'After'], $diff->getOutput()));
		} finally {
			$fs->delete([$before_file, $after_file]);
		}
	}
}
