<?php

namespace Glhd\LaravelDumper\Tests;

use Closure;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class DatabaseCasterTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_eloquent_model(): void
	{
		Date::setTestNow($now = now());
		
		$company = Company::create(['name' => 'Galahad']);
		$user = User::create(['name' => 'John', 'email' => 'foo@bar.com', 'company_id' => $company->id]);
		$user->setRelation('company', $company);
		$user->name = 'Chris';
		
		$timestamp = $now->format('Y-m-d H:i:s');
		
		$dump = $this->getDump($user);
		
		$this->assertStringStartsWith(User::class, $dump);
		$this->assertStringContainsString('id', $dump);
		$this->assertStringContainsString('company_id', $dump);
		$this->assertStringContainsString('email', $dump);
		$this->assertStringContainsString('name', $dump);
		$this->assertStringContainsString('isDirty()', $dump);
		$this->assertStringContainsString('exists', $dump);
		$this->assertStringContainsString('wasRecentlyCreated', $dump);
		$this->assertStringContainsString($timestamp, $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
		
		$this->assertStringNotContainsString('escapeWhenCastingToString', $dump);
	}
	
	public function test_query_builder(): void
	{
		$builder = DB::table('users')
			->where('email', 'bogdan@foo.com')
			->limit(10);
		
		$dump = $this->getDump($builder);
		
		$this->assertStringStartsWith('Illuminate\\Database\\Query\\Builder {', $dump);
		$this->assertStringContainsString('select * from "users" where "email" = \'bogdan@foo.com\' limit 10', $dump);
		$this->assertStringContainsString('#connection', $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
	}
	
	/** @see https://github.com/glhd/laravel-dumper/issues/6 */
	public function test_where_between_statement(): void
	{
		$builder = User::where('name', 'test')
			->whereBetween('id', [1, 2]);
		
		$dump = $this->getDump($builder);
		
		$this->assertStringStartsWith('Illuminate\\Database\\Eloquent\\Builder {', $dump);
		$this->assertStringContainsString('select * from "users" where "name" = \'test\' and "id" between \'1\' and \'2\'', $dump);
		$this->assertStringContainsString('#connection', $dump);
		$this->assertStringContainsString('#model', $dump);
		$this->assertStringContainsString(User::class, $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
	}
	
	public function test_eloquent_builder(): void
	{
		$builder = User::query()
			->with('company')
			->where('email', 'bogdan@foo.com')
			->limit(10);
		
		$dump = $this->getDump($builder);
		
		$this->assertStringStartsWith('Illuminate\\Database\\Eloquent\\Builder {', $dump);
		$this->assertStringContainsString('select * from "users" where "email" = \'bogdan@foo.com\' limit 10', $dump);
		$this->assertStringContainsString('#model', $dump);
		$this->assertStringContainsString('#eagerLoad', $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
	}
	
	public function test_eloquent_relation(): void
	{
		Company::create(['id' => 1, 'name' => 'Galahad']);
		$user = User::create(['id' => 1, 'name' => 'John', 'email' => 'foo@bar.com', 'company_id' => 1]);
		
		$dump = $this->getDump($user->company());
		
		$this->assertStringStartsWith('Illuminate\\Database\\Eloquent\\Relations\\BelongsTo {', $dump);
		$this->assertStringContainsString('select * from "companies" where "companies"."id" = \'1\'', $dump);
		$this->assertStringContainsString('#parent', $dump);
		$this->assertStringContainsString('#related', $dump);
		$this->assertStringContainsString(User::class, $dump);
		$this->assertStringContainsString(Company::class, $dump);
		$this->assertMatchesRegularExpression('/\s*…\d+\n}$/', $dump);
	}
	
	protected function defineDatabaseMigrations()
	{
		$this->loadMigrationsFrom(__DIR__.'/migrations');
	}
}

class User extends Model
{
	protected $guarded = [];
	
	public function company()
	{
		return $this->belongsTo(Company::class);
	}
}

class Company extends Model
{
	protected $guarded = [];
	
	public function users()
	{
		return $this->hasMany(User::class);
	}
}
