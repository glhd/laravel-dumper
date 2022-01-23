<?php

namespace Glhd\LaravelDumper;

use Illuminate\Support\ServiceProvider;

class LaravelDumperServiceProvider extends ServiceProvider
{
	protected array $casters = [
		Casters\ContainerCaster::class,
		Casters\ModelCaster::class,
		Casters\BuilderCaster::class,
		Casters\DatabaseConnectionCaster::class,
		Casters\CarbonCaster::class,
		Casters\RequestCaster::class,
		Casters\ParameterBagCaster::class,
		Casters\HeaderBagCaster::class,
		Casters\ResponseCaster::class,
	];
	
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
		$casters = array_merge($this->casters, config('laravel-dumper.casters', []));
		
		foreach ($casters as $caster) {
			$caster::register($this->app);
		}
	}
	
	protected function packageConfigFile(): string
	{
		return dirname(__DIR__).DIRECTORY_SEPARATOR.'config.php';
	}
}
