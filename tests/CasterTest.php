<?php

namespace Glhd\LaravelDumper\Tests;

use Carbon\Carbon;
use Glhd\LaravelDumper\Casters\Caster;
use Glhd\LaravelDumper\Casters\CustomCaster;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CasterTest extends TestCase
{
	public function test_carbon_date(): void
	{
		$now = Carbon::parse('2022-01-18 19:44:02.572622', 'America/New_York');
		
		$dump = $this->getDump($now);
		
		$this->assertStringStartsWith('Carbon\\Carbon', $dump);
		$this->assertStringContainsString('date: 2022-01-18 19:44:02.572622 America/New_York (-05:00)', $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
		
		$this->assertStringNotContainsString('localMacros', $dump);
	}
	
	public function test_package_can_be_disabled(): void
	{
		$this->withoutWritingDiffs();
		
		CustomCaster::for(static::class)
			->only([])
			->virtual('foo', fn() => 'bar');
		
		$getLineCount = fn() => substr_count($this->getDump($this), "\n") + 1;
		
		$this->assertEquals(4, $getLineCount());
		
		Caster::disable();
		
		$this->assertGreaterThan(100, $getLineCount());
		
		Caster::enable();
		
		$this->assertEquals(4, $getLineCount());
	}
	
	public function test_container(): void
	{
		$container = new Container();
		
		$container->bind(static::class, fn() => $this);
		$container->alias(static::class, 'bar');
		$container->extend('bar', fn() => $this);
		$container->make('bar');
		
		$dump = $this->getDump($container);
		
		$this->assertStringStartsWith('Illuminate\\Container\\Container', $dump);
		$this->assertStringContainsString('#bindings', $dump);
		$this->assertStringContainsString('#aliases', $dump);
		$this->assertStringContainsString('#resolved', $dump);
		$this->assertStringContainsString('#extenders', $dump);
		$this->assertStringContainsString(static::class, $dump);
		
		// These won't be true until I get a PR to Laravel
		// $this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
		// $this->assertStringNotContainsString('#globalBeforeResolvingCallbacks', $dump);
	}
	
	public function test_container_nested(): void
	{
		$container = new Container();
		
		$expected = <<<EOD
		array:1 [
		  0 => Illuminate\Container\Container { …%d}
		]
		EOD;
		
		$this->assertDumpMatchesFormat($expected, [$container]);
	}
	
	public function test_request(): void
	{
		$request = Request::create('/1');
		
		$dump = $this->getDump($request);
		
		$this->assertStringStartsWith('Illuminate\\Http\\Request {', $dump);
		$this->assertStringContainsString('+attributes', $dump);
		$this->assertStringContainsString('+request', $dump);
		$this->assertStringContainsString('+query', $dump);
		$this->assertStringContainsString('+server', $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
	}
	
	public function test_response(): void
	{
		$response = new Response('Hello world.');
		
		$dump = $this->getDump($response);
		
		$this->assertStringStartsWith('Illuminate\\Http\\Response {', $dump);
		$this->assertStringContainsString('+headers', $dump);
		$this->assertStringContainsString('#content: "Hello world."', $dump);
		$this->assertStringContainsString('#statusCode: 200', $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
	}
}
