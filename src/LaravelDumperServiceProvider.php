<?php

namespace Glhd\LaravelDumper;

use Glhd\LaravelDumper\Casters\BuilderCaster;
use Glhd\LaravelDumper\Casters\CarbonCaster;
use Glhd\LaravelDumper\Casters\ContainerCaster;
use Glhd\LaravelDumper\Casters\DatabaseConnectionCaster;
use Glhd\LaravelDumper\Casters\HeaderBagCaster;
use Glhd\LaravelDumper\Casters\ModelCaster;
use Glhd\LaravelDumper\Casters\ParameterBagCaster;
use Glhd\LaravelDumper\Casters\RequestCaster;
use Glhd\LaravelDumper\Casters\ResponseCaster;
use Illuminate\Support\ServiceProvider;

class LaravelDumperServiceProvider extends ServiceProvider
{
	protected array $casters = [
		ContainerCaster::class,
		ModelCaster::class,
		BuilderCaster::class,
		DatabaseConnectionCaster::class,
		CarbonCaster::class,
		RequestCaster::class,
		ParameterBagCaster::class,
		HeaderBagCaster::class,
		ResponseCaster::class,
	];
	
	protected string $base_dir;
	
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->base_dir = dirname(__DIR__);
	}
	
	public function register()
	{
		$this->mergeConfigFrom("{$this->base_dir}/config.php", 'laravel-dumper');
		
		require_once __DIR__.'/helpers.php';
		
		if ($this->isEnabledInCurrentEnvironment()) {
			$this->registerCasters();
		}
	}
	
	public function boot()
	{
		if (method_exists($this->app, 'configPath')) {
			$this->publishes([
				"{$this->base_dir}/config.php" => $this->app->configPath('laravel-dumper.php'),
			], ['laravel-dumper', 'laravel-dumper-config']);
		}
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
}
