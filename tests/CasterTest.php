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
		
		$expected = <<<EOD
		Carbon\Carbon @%d {
		  date: 2022-01-18 19:44:02.572622 America/New_York (-05:00)
		  #endOfTime: false
		  #startOfTime: false
		  #constructedObjectId: "%s"
		  #dumpProperties: array:3 [
		    0 => "date"
		    1 => "timezone_type"
		    2 => "timezone"
		  ]
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $now);
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
		
		$fqcn = static::class;
		
		$expected = <<<EOD
		Illuminate\Container\Container {
		  #bindings: array:1 [
		    "{$fqcn}" => array:2 [
		      "concrete" => Closure() {
		        class: "{$fqcn}"
		        this: {$fqcn} {#1 …}
		        file: "%s"
		        line: "%d to %d"
		      }
		      "shared" => false
		    ]
		  ]
		  #aliases: array:1 [
		    "bar" => "{$fqcn}"
		  ]
		  #resolved: array:1 [
		    "{$fqcn}" => true
		  ]
		  #extenders: array:1 [
		    "{$fqcn}" => array:1 [
		      0 => Closure() {
		        class: "{$fqcn}"
		        this: Glhd\LaravelDumper\Tests\CasterTest {#1 …}
		        file: "%s"
		        line: "%d to %d"
		      }
		    ]
		  ]
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $container);
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
		
		$expected = <<<EOD
		Illuminate\Http\Request {
		  +attributes: Symfony\Component\HttpFoundation\ParameterBag {}
		  +request: Symfony\Component\HttpFoundation\InputBag {}
		  +query: Symfony\Component\HttpFoundation\InputBag {}
		  +server: Symfony\Component\HttpFoundation\ServerBag {
		    SERVER_NAME: "localhost"
		    SERVER_PORT: 80
		    HTTP_HOST: "localhost"
		    HTTP_USER_AGENT: "Symfony"
		    HTTP_ACCEPT: "%s"
		    HTTP_ACCEPT_LANGUAGE: "%s"
		    HTTP_ACCEPT_CHARSET: "%s"
		    REMOTE_ADDR: "%s"
		    SCRIPT_NAME: ""
		    SCRIPT_FILENAME: ""
		    SERVER_PROTOCOL: "HTTP/1.1"
		    REQUEST_TIME: %d
		    REQUEST_TIME_FLOAT: %d.%d
		    PATH_INFO: ""
		    REQUEST_METHOD: "GET"
		    REQUEST_URI: "/1"
		    QUERY_STRING: ""
		  }
		  +files: Symfony\Component\HttpFoundation\FileBag {}
		  +cookies: Symfony\Component\HttpFoundation\InputBag {}
		  +headers: Symfony\Component\HttpFoundation\HeaderBag {
		    host: "localhost"
		    user-agent: "Symfony"
		    accept: "%s"
		    accept-language: "%s"
		    accept-charset: "%s"
		    #cacheControl: []
		  }
		  #defaultLocale: "en"
		  -isHostValid: true
		  -isForwardedValid: true
		  pathInfo: "/1"
		  requestUri: "/1"
		  baseUrl: ""
		  basePath: ""
		  method: "GET"
		  format: "html"
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $request);
	}
	
	public function test_response(): void
	{
		$response = new Response('Hello world.');
		
		$expected = <<<EOD
		Illuminate\Http\Response {
		  +headers: Symfony\Component\HttpFoundation\ResponseHeaderBag {
		    cache-control: "%s"
		    date: "%s"
		    #cacheControl: []
		  }
		  #content: "Hello world."
		  #version: "%d.%d"
		  #statusCode: 200
		  #statusText: "OK"
		  +original: "Hello world."
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $response);
	}
}
