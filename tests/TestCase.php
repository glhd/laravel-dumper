<?php

namespace Galahad\LaravelPackageTemplate\Tests;

use Galahad\LaravelPackageTemplate\Support\PackageServiceProvider;
use Illuminate\Container\Container;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
	protected function getPackageProviders($app)
	{
		return [
			PackageServiceProvider::class,
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
}
