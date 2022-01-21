<?php

namespace Glhd\LaravelDumper\Support;

use Symfony\Component\VarDumper\Caster\Caster;

class Key
{
	public static function protected(string $key): string
	{
		return Caster::PREFIX_PROTECTED.$key;
	}
	
	public static function virtual(string $key): string
	{
		return Caster::PREFIX_VIRTUAL.$key;
	}
	
	public static function dynamic(string $key): string
	{
		return Caster::PREFIX_DYNAMIC.$key;
	}
}
