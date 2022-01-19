<?php

namespace Glhd\LaravelDumper\Casters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\Cloner\Stub;

class ModelCaster extends Caster
{
	public static array $targets = [Model::class];
	
	protected array $attribute_order = [
		'id' => 1,
		'*_id' => 2,
		'*' => 3,
		'*_at' => 4,
		'created_at' => 5,
		'updated_at' => 6,
		'deleted_at' => 7,
	];
	
	/**
	 * @param Model $target
	 * @param array $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		return array_merge(
			$this->attributesToDynamicProperties($target),
			$this->virtualProperties($target),
			$this->cutProperties($properties, $stub, $is_nested)
		);
	}
	
	protected function attributesToDynamicProperties(Model $obj): array
	{
		return collect($obj->getAttributes())
			->sortKeys()
			->sortBy(fn($value, $key) => $this->getAttributePosition($key))
			->mapWithKeys(fn($value, $key) => [Key::dynamic($key) => $value])
			->all();
	}
	
	protected function virtualProperties(Model $obj): array
	{
		return [
			Key::virtual('isDirty()') => $obj->isDirty(),
		];
	}
	
	protected function getAttributePosition(string $attribute): int
	{
		$result = $this->attribute_order['*'];
		
		foreach ($this->attribute_order as $pattern => $position) {
			if ('*' !== $pattern && Str::is($pattern, $attribute)) {
				$result = $position;
			}
		}
		
		return $result;
	}
	
	protected function cutProperties(array $properties, Stub $stub, bool $isNested): array
	{
		$keep = [
			'exists',
			'wasRecentlyCreated',
			Key::protected('relations'),
		];
		
		if (!$isNested) {
			$keep = array_merge($keep, [
				Key::protected('connection'),
				Key::protected('table'),
				Key::protected('original'),
				Key::protected('changes'),
			]);
		}
		
		// Doing this in a foreach allows us to update the order while filtering
		$result = [];
		foreach ($keep as $key) {
			if (isset($properties[$key])) {
				$result[$key] = $properties[$key];
			}
		}
		
		$stub->cut += (count($properties) - count($result));
		
		return $result;
	}
}
