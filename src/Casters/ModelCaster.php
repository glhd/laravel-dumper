<?php

namespace Glhd\LaravelDumper\Casters;

use Glhd\LaravelDumper\Support\Key;
use Glhd\LaravelDumper\Support\Properties;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\Cloner\Stub;

class ModelCaster extends Caster
{
	public static array $targets = [Model::class];
	
	protected array $attribute_order = [
		'id',
		'*_id',
		'*',
		'*_at',
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	/**
	 * @param Model $target
	 * @param \Glhd\LaravelDumper\Support\Properties $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, Properties $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		return array_merge(
			$this->attributesToDynamicProperties($target),
			$this->virtualProperties($target),
			$this->cutProperties($properties, $stub, $is_nested)
		);
	}
	
	protected function attributesToDynamicProperties(Model $obj): array
	{
		return Properties::make($obj->getAttributes())
			->sortKeys()
			->reorder($this->attribute_order)
			->mapWithKeys(fn($value, $key) => [Key::dynamic($key) => $value])
			->all();
	}
	
	protected function virtualProperties(Model $obj): array
	{
		return [
			Key::virtual('isDirty()') => $obj->isDirty(),
		];
	}
	
	protected function cutProperties(Properties $properties, Stub $stub, bool $is_nested): array
	{
		$keep = [
			'exists',
			'wasRecentlyCreated',
			Key::protected('relations'),
		];
		
		if (!$is_nested) {
			$keep = array_merge($keep, [
				Key::protected('connection'),
				Key::protected('table'),
				Key::protected('original'),
				Key::protected('changes'),
			]);
		}
		
		return $properties
			->only($keep)
			->reorder($keep)
			->applyCutsToStub($stub, $properties)
			->all();
	}
}
