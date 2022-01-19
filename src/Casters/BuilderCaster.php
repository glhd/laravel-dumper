<?php

namespace Glhd\LaravelDumper\Casters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use SqlFormatter;
use Symfony\Component\VarDumper\Caster\CutStub;
use Symfony\Component\VarDumper\Cloner\Stub;

class BuilderCaster extends Caster
{
	public static array $targets = [
		BaseBuilder::class,
		EloquentBuilder::class,
		Relation::class,
	];
	
	/**
	 * @param BaseBuilder|EloquentBuilder|Relation $target
	 * @param array $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		$result = [
			Key::virtual('sql') => $this->formatSql($target->toSql(), $target->getBindings()),
			Key::protected('connection') => $target->getConnection(),
		];
		
		if ($target instanceof EloquentBuilder) {
			$result[Key::protected('model')] = new CutStub($properties[Key::protected('model')]);
			$result[Key::protected('eagerLoad')] = $properties[Key::protected('eagerLoad')];
		}
		
		if ($target instanceof Relation) {
			$result[Key::protected('parent')] = new CutStub($properties[Key::protected('parent')]);
			$result[Key::protected('related')] = new CutStub($properties[Key::protected('related')]);
		}
		
		$stub->cut += (count($properties) - count($result));
		
		return $result;
	}
	
	protected function formatSql($sql, $bindings): string
	{
		$bindings = Arr::flatten($bindings);
		$merged = preg_replace_callback('/\?/', fn() => DB::getPdo()->quote(array_shift($bindings)), $sql);
		
		if (strlen($merged) > 120) {
			$merged = SqlFormatter::format($merged, false);
		}
		
		return $merged;
	}
}
