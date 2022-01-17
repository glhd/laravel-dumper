<?php

namespace Galahad\LaravelPackageTemplate\Support;

use Galahad\Aire\Aire;
use Galahad\Aire\Elements\Form;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
	protected string $base_dir;
	
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->base_dir = dirname(__DIR__, 2);
	}
	
	public function boot()
	{
		require_once __DIR__.'/helpers.php';
		
		$this->bootConfig();
		$this->bootViews();
		$this->bootBladeComponents();
	}
	
	public function register()
	{
		$this->mergeConfigFrom("{$this->base_dir}/config.php", 'laravel-package-template');
	}
	
	protected function bootViews() : self
	{
		$views_directory = "{$this->base_dir}/resources/views";
		
		$this->loadViewsFrom($views_directory, 'laravel-package-template');
		
		if (method_exists($this->app, 'resourcePath')) {
			$this->publishes([
				$views_directory => $this->app->resourcePath('views/vendor/laravel-package-template'),
			], 'laravel-package-template-views');
		}
		
		return $this;
	}
	
	protected function bootBladeComponents() : self
	{
		if (version_compare($this->app->version(), '8.0.0', '>=')) {
			Blade::componentNamespace('Glhd\\LaravelPackageTemplate\\Components', 'laravel-package-template');
		}
		
		return $this;
	}
	
	protected function bootConfig() : self
	{
		if (method_exists($this->app, 'configPath')) {
			$this->publishes([
				"{$this->base_dir}/config.php" => $this->app->configPath('laravel-package-template.php'),
			], 'laravel-package-template-config');
		}
		
		return $this;
	}
}
