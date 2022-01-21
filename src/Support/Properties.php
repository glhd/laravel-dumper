<?php

namespace Glhd\LaravelDumper\Support;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\CutStub;
use Symfony\Component\VarDumper\Cloner\Stub;

class Properties extends Collection
{
	protected array $prefixes = [
		Caster::PREFIX_PROTECTED,
		Caster::PREFIX_VIRTUAL,
		Caster::PREFIX_DYNAMIC,
	];
	
	public function applyCutsToStub(Stub $stub, Properties $original): Properties
	{
		$stub->cut += ($original->count() - $this->count());
		
		return $this;
	}
	
	public function cut($key, $default = null): CutStub
	{
		return new CutStub($this->get($key, $default));
	}
	
	public function cutProtected($key, $default = null): CutStub
	{
		return $this->cut(Key::protected($key), $default);
	}
	
	public function cutVirtual($key, $default = null): CutStub
	{
		return $this->cut(Key::virtual($key), $default);
	}
	
	public function cutDynamic($key, $default = null): CutStub
	{
		return $this->cut(Key::dynamic($key), $default);
	}
	
	public function get($key, $default = null)
	{
		$missing = new stdClass();
		foreach ($this->addPrefixes($key) as $prefixed_key) {
			$parameter = parent::get($prefixed_key, $missing);
			if ($missing !== $parameter) {
				return $parameter;
			}
		}
		
		return $default;
	}
	
	public function has($key)
	{
		$keys = is_array($key)
			? $key
			: func_get_args();
		
		foreach ($keys as $value) {
			if (!$this->hasAny($this->addPrefixes($value))) {
				return false;
			}
		}
		
		return true;
	}
	
	public function hasAny($key)
	{
		if ($this->isEmpty()) {
			return false;
		}
		
		$keys = is_array($key)
			? $key
			: func_get_args();
		
		foreach ($keys as $value) {
			if (array_key_exists($value, $this->items)) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getProtected($key, $default = null)
	{
		return $this->get(Key::protected($key), $default);
	}
	
	public function getVirtual($key, $default = null)
	{
		return $this->get(Key::virtual($key), $default);
	}
	
	public function getDynamic($key, $default = null)
	{
		return $this->get(Key::dynamic($key), $default);
	}
	
	public function putProtected($key, $value): Properties
	{
		return $this->put(Key::protected($key), $value);
	}
	
	public function putVirtual($key, $value): Properties
	{
		return $this->put(Key::virtual($key), $value);
	}
	
	public function putDynamic($key, $value): Properties
	{
		return $this->put(Key::dynamic($key), $value);
	}
	
	public function copy($key, Properties $from, $default = null): Properties
	{
		return $this->put($key, $from->get($key, $default));
	}
	
	public function copyProtected($key, Properties $from, $default = null): Properties
	{
		return $this->copy(Key::protected($key), $from, $default);
	}
	
	public function copyVirtual($key, Properties $from, $default = null): Properties
	{
		return $this->copy(Key::virtual($key), $from, $default);
	}
	
	public function copyDynamic($key, Properties $from, $default = null): Properties
	{
		return $this->copy(Key::dynamic($key), $from, $default);
	}
	
	public function copyAndCut($key, Properties $from, $default = null): Properties
	{
		return $this->put($key, $from->cut($key, $default));
	}
	
	public function copyAndCutProtected($key, Properties $from, $default = null): Properties
	{
		return $this->copyAndCut(Key::protected($key), $from, $default);
	}
	
	public function copyAndCutVirtual($key, Properties $from, $default = null): Properties
	{
		return $this->copyAndCut(Key::virtual($key), $from, $default);
	}
	
	public function copyAndCutDynamic($key, Properties $from, $default = null): Properties
	{
		return $this->copyAndCut(Key::dynamic($key), $from, $default);
	}
	
	public function only($keys)
	{
		return $this->filter(function($value, $key) use ($keys) {
			return Str::is($keys, $key) || Str::is($keys, $this->stripPrefix($key));
		});
	}
	
	public function except($keys)
	{
		return $this->reject(function($value, $key) use ($keys) {
			return Str::is($keys, $key) || Str::is($keys, $this->stripPrefix($key));
		});
	}
	
	public function filter(callable $callback = null)
	{
		if (null === $callback) {
			$callback = static function($property) {
				if (is_array($property)) {
					return count($property);
				}
				
				if ($property instanceof Enumerable) {
					return $property->isNotEmpty();
				}
				
				return null !== $property;
			};
		}
		
		return parent::filter($callback);
	}
	
	public function reorder(array $rules): Properties
	{
		return $this->sortBy($this->getReorderCallback($rules));
	}
	
	protected function getReorderCallback(array $rules): Closure
	{
		$map = $this->createReorderMapFromRules($rules);
		
		return function($value, $key) use ($map) {
			$result = Arr::pull($map, '*');
			
			foreach ($map as $pattern => $position) {
				if ($key === $pattern || Str::is($pattern, $this->stripPrefix($key))) {
					$result = $position;
				}
			}
			
			return $result;
		};
	}
	
	protected function createReorderMapFromRules(array $rules): array
	{
		$rules = array_values($rules);
		$map = array_combine($rules, array_keys($rules));
		
		// Ensure that there's always a '*' pattern, defaulting to the end
		$map['*'] ??= count($map);
		
		return $map;
	}
	
	protected function stripPrefix(string $key): string
	{
		return str_replace($this->prefixes, '', $key);
	}
	
	protected function addPrefixes(string $key): array
	{
		if (Str::startsWith($key, $this->prefixes)) {
			return [$key];
		}
		
		return array_merge([$key], array_map(fn($prefix) => $prefix.$key, $this->prefixes));
	}
}
