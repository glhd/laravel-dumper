<?php

namespace Glhd\LaravelDumper\Casters;

use Glhd\LaravelDumper\Support\Properties;
use Herd\Symfony\Component\VarDumper\Cloner\Stub as HerdStub;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use SqlFormatter;
use Symfony\Component\VarDumper\Cloner\Stub;
use Throwable;

class BuilderCaster extends Caster
{
	public static array $targets = [
		BaseBuilder::class,
		EloquentBuilder::class,
		Relation::class,
	];
	
	/**
	 * @param BaseBuilder|EloquentBuilder|Relation $target
	 * @param \Glhd\LaravelDumper\Support\Properties $properties
	 * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
	 * @param bool $is_nested
	 * @param int $filter
	 * @return array
	 */
	public function cast($target, Properties $properties, Stub|HerdStub $stub, bool $is_nested, int $filter = 0): array
	{
		$result = new Properties();
		
		$result->putVirtual('sql', $this->formatSql($target));
		$result->putProtected('connection', $target->getConnection());
		
		if ($target instanceof EloquentBuilder) {
			$result->copyAndCutProtected('model', $properties);
			$result->copyProtected('eagerLoad', $properties);
		}
		
		if ($target instanceof Relation) {
			$result->copyAndCutProtected('parent', $properties);
			$result->copyAndCutProtected('related', $properties);
		}
		
		$result->applyCutsToStub($stub, $properties);
		
		return $result->all();
	}
	
	protected function formatSql($target): string
	{
		$formatted = null;
		
		try {
			if (method_exists($target, 'toRawSql')) {
				$formatted = $target->toRawSql();
			}
		} catch (Throwable) {
			// Just fall back on naive formatter below
		}
		
		if (null === $formatted) {
			$sql = $target->toSql();
			$bindings = Arr::flatten($target->getBindings());
			$formatted = preg_replace_callback('/\?/', function() use (&$bindings) {
				return DB::getPdo()->quote(array_shift($bindings));
			}, $sql);
		}
		
		if (strlen($formatted) > 120 && class_exists(SqlFormatter::class)) {
			$formatted = SqlFormatter::format($formatted, false);
		}
		
		return $formatted;
	}
}
