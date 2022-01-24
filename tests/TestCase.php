<?php

namespace Glhd\LaravelDumper\Tests;

use Glhd\LaravelDumper\Casters\Caster;
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
	
	protected bool $write_diff_if_configured = true;
	
	protected int $diff_count = 0;
	
	protected function setUp(): void
	{
		parent::setUp();
		
		Caster::enable();
		$this->write_diff_if_configured = true;
		$this->diff_count = 0;
	}
	
	protected function withoutWritingDiffs(): self
	{
		$this->write_diff_if_configured = false;
		
		return $this;
	}
	
	protected function getPackageProviders($app)
	{
		return [
			LaravelDumperServiceProvider::class,
		];
	}
	
	protected function getApplicationTimezone($app)
	{
		return 'America/New_York';
	}
	
	protected function getDump($data, $key = null, int $filter = 0): ?string
	{
		if (!$this->write_diff_if_configured || '1' !== getenv('WRITE_DIFFS')) {
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
		if ($before === $after) {
			return;
		}
		
		$fs = new Filesystem();
		$path = __DIR__.'/../diffs';
		
		[$_, $_, $_, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
		$name = Str::of($caller['function'])->after('test_')->replace('_', '-');
		
		$this->diff_count++;
		if ($this->diff_count > 1) {
			$name .= "-{$this->diff_count}";
		}
		
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
