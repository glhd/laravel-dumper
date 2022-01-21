<?php

namespace Glhd\LaravelDumper\Tests;

use Glhd\LaravelDumper\Facades\LaravelDumper;

class CustomCasterTest extends TestCase
{
	public function test_custom_caster(): void
	{
		LaravelDumper::for(MyCustomObject::class)
			->only(['foo', 'nothing', 'nah'])
			->dynamic('dyn', fn() => 'this is a dynamic prop')
			->virtual('virt', fn() => 'this is a virtual prop')
			->filter()
			->reorder(['dyn', 'foo']);
		
		LaravelDumper::for(MyOtherCustomObject::class)
			->virtual('foo', fn() => 'bar');
		
		$expected = <<<EOD
		Glhd\LaravelDumper\Tests\MyCustomObject {
		  +"dyn": "this is a dynamic prop"
		  #foo: "foo"
		  virt: "this is a virtual prop"
		   …1
		}
		EOD;
		
		$this->assertDumpEquals($expected, new MyCustomObject());
		
		$expected = <<<EOD
		Glhd\LaravelDumper\Tests\MyOtherCustomObject {
		  foo: "bar"
		}
		EOD;
		
		$this->assertDumpEquals($expected, new MyOtherCustomObject());
	}
}

class MyCustomObject
{
	protected $foo = 'foo';
	
	protected $bar = 'bar';
	
	protected $nothing = null;
	
	protected $nah = [];
}

class MyOtherCustomObject
{
}
