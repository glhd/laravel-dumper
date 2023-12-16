<?php

namespace Glhd\LaravelDumper;

use Illuminate\Support\ServiceProvider;

class LaravelDumperServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom($this->packageConfigFile(), 'laravel-dumper');
		
		if ($this->isEnabledInCurrentEnvironment()) {
			require_once __DIR__.DIRECTORY_SEPARATOR.'helpers.php';
			
			$this->registerCasters();
		}
	}
	
	public function boot()
	{
		$this->publishes([
			$this->packageConfigFile() => $this->app->configPath('laravel-dumper.php'),
		], ['laravel-dumper', 'laravel-dumper-config']);
	}
	
	protected function isEnabledInCurrentEnvironment(): bool
	{
		$environments = config('laravel-dumper.environments', ['local', 'testing']);
		
		return $this->app->environment($environments);
	}
	
	protected function registerCasters(): void
	{
		$casters = require __DIR__.'/Casters/manifest.php';
		foreach ($casters as $caster) {
			$caster::register($this->app);
		}
	}
	
	protected function packageConfigFile(): string
	{
		return dirname(__DIR__).DIRECTORY_SEPARATOR.'config.php';
	}
}
