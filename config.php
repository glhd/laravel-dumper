<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Enabled Environments
	|--------------------------------------------------------------------------
	|
	| We recommend that you only install this package in your dev dependencies
	| so that it's not even installed in production. If you would like to use
	| custom dumpers in production, add that environment here.
	|
	*/
	
	'environments' => ['local', 'testing'],
	
	/*
	|--------------------------------------------------------------------------
	| Custom Casters
	|--------------------------------------------------------------------------
	|
	| If you would like to register any custom casters, you can do so here.
	|
	*/
	
	'casters' => [
		// App\VarDumper\Casters\MyCustomCaster::class,
	],
];
