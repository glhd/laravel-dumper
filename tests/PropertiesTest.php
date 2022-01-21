<?php

namespace Glhd\LaravelDumper\Tests;

use Glhd\LaravelDumper\Support\Key;
use Glhd\LaravelDumper\Support\Properties;

class PropertiesTest extends TestCase
{
	protected Properties $parameters;
	
	protected function setUp(): void
	{
		parent::setUp();
		
		$this->parameters = new Properties([
			Key::protected('protected') => 1,
			Key::virtual('virtual') => 1,
			Key::dynamic('dynamic') => 1,
			'prefix_b' => 1,
			'prefix_a' => 1,
			'b_suffix' => 1,
			'a_suffix' => 1,
			'other' => 1,
		]);
	}
	
	public function test_reordering_parameters(): void
	{
		$rules = [
			'prefix_*',
			'dynamic',
			'virtual',
			'*',
			'protected',
			'*_suffix',
		];
		
		$reordered = $this->parameters->reorder($rules)->all();
		
		$this->assertEquals([
			'prefix_b' => 1,
			'prefix_a' => 1,
			Key::dynamic('dynamic') => 1,
			Key::virtual('virtual') => 1,
			'other' => 1,
			Key::protected('protected') => 1,
			'b_suffix' => 1,
			'a_suffix' => 1,
		], $reordered);
	}
	
	public function test_only_keeping_specific_parameters(): void
	{
		$subset = $this->parameters->only(['dynamic', '*_suffix'])->all();
		
		$this->assertEquals([
			Key::dynamic('dynamic') => 1,
			'b_suffix' => 1,
			'a_suffix' => 1,
		], $subset);
	}
	
	public function test_excluding_specific_parameters(): void
	{
		$subset = $this->parameters->except(['dynamic', '*_suffix'])->all();
		
		$this->assertEquals([
			Key::protected('protected') => 1,
			Key::virtual('virtual') => 1,
			'prefix_b' => 1,
			'prefix_a' => 1,
			'other' => 1,
		], $subset);
	}
	
	public function test_has_method(): void
	{
		$this->assertTrue($this->parameters->has('protected'));
		$this->assertTrue($this->parameters->has(Key::protected('protected')));
		$this->assertTrue($this->parameters->has(Key::protected('protected'), 'prefix_b'));
		
		$this->assertFalse($this->parameters->has(Key::virtual('protected')));
		$this->assertFalse($this->parameters->has(Key::protected('protected'), 'foo'));
		
		$this->assertTrue($this->parameters->hasAny(Key::protected('protected'), 'foo'));
	}
}
