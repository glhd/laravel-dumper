<?php

namespace Glhd\LaravelDumper\Tests;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

class CasterTest extends TestCase
{
	use VarDumperTestTrait;
	
	public function test_it_casts_carbon_instances(): void
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
	
	public function test_it_cuts_most_internals_from_a_container(): void
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
	
	public function test_it_cuts_all_internals_from_a_nested_container(): void
	{
		$container = new Container();
		
		$expected = <<<EOD
		array:1 [
		  0 => Illuminate\Container\Container { …%d}
		]
		EOD;
		
		$this->assertDumpMatchesFormat($expected, [$container]);
	}
}
