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
		
		$expected = <<<EOD
		Glhd\LaravelDumper\Tests\User {
		  +"id": %d
		  +"company_id": %d
		  +"email": "foo@bar.com"
		  +"name": "Chris"
		  +"created_at": "{$timestamp}"
		  +"updated_at": "{$timestamp}"
		  isDirty(): true
		  +exists: true
		  +wasRecentlyCreated: true
		  #relations: array:1 [
		    "company" => Glhd\LaravelDumper\Tests\Company {
		      +"id": %d
		      +"name": "Galahad"
		      +"created_at": "{$timestamp}"
		      +"updated_at": "{$timestamp}"
		      isDirty(): false
		      +exists: true
		      +wasRecentlyCreated: true
		      #relations: []
		       …%d
		    }
		  ]
		  #connection: "testing"
		  #table: "users"
		  #original: array:6 [
		    "name" => "John"
		    "email" => "foo@bar.com"
		    "company_id" => %d
		    "updated_at" => "{$timestamp}"
		    "created_at" => "{$timestamp}"
		    "id" => %d
		  ]
		  #changes: []
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $user);
	}
	
	public function test_query_builder(): void
	{
		$builder = DB::table('users')
			->where('email', 'bogdan@foo.com')
			->limit(10);
		
		$expected = <<<EOD
		Illuminate\Database\Query\Builder {
		  sql: "select * from "users" where "email" = 'bogdan@foo.com' limit 10"
		  #connection: Illuminate\Database\SQLiteConnection {
		    name: "testing"
		    database: ":memory:"
		    driver: "sqlite"
		     …%d
		  }
		   …%d
		}
		EOD;

		$this->assertDumpMatchesFormat($expected, $builder);
	}
	
	/** @see https://github.com/glhd/laravel-dumper/issues/6 */
	public function test_where_between_statement(): void
	{
		$builder = User::where('name', 'test')
			->whereBetween('id', [1, 2]);
		
		$expected = <<<EOD
		Illuminate\Database\Eloquent\Builder {
		  sql: "select * from "users" where "name" = 'test' and "id" between '1' and '2'"
		  #connection: Illuminate\Database\SQLiteConnection {
		    name: "testing"
		    database: ":memory:"
		    driver: "sqlite"
		     …%d
		  }
		  #model: Glhd\LaravelDumper\Tests\User { …}
		  #eagerLoad: []
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $builder);
	}
	
	public function test_eloquent_builder(): void
	{
		$builder = User::query()
			->with('company')
			->where('email', 'bogdan@foo.com')
			->limit(10);
		
		$expected = <<<EOD
		Illuminate\Database\Eloquent\Builder {
		  sql: "select * from "users" where "email" = 'bogdan@foo.com' limit 10"
		  #connection: Illuminate\Database\SQLiteConnection {
		    name: "testing"
		    database: ":memory:"
		    driver: "sqlite"
		     …%d
		  }
		  #model: Glhd\LaravelDumper\Tests\User { …}
		  #eagerLoad: array:1 [
		    "company" => Closure() {
		      class: "Illuminate\Database\Eloquent\Builder"
		      file: "%s"
		      line: "%d to %d"
		    }
		  ]
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $builder);
	}
	
	public function test_eloquent_relation(): void
	{
		Company::create(['id' => 1, 'name' => 'Galahad']);
		$user = User::create(['id' => 1, 'name' => 'John', 'email' => 'foo@bar.com', 'company_id' => 1]);
		
		$expected = <<<EOD
		Illuminate\Database\Eloquent\Relations\BelongsTo {
		  sql: "select * from "companies" where "companies"."id" = '1'"
		  #connection: Illuminate\Database\SQLiteConnection {
		    name: "testing"
		    database: ":memory:"
		    driver: "sqlite"
		     …%d
		  }
		  #parent: Glhd\LaravelDumper\Tests\User { …}
		  #related: Glhd\LaravelDumper\Tests\Company { …}
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $user->company());
	}
	
	public function test_unexpected_database_connections(): void
	{
		// Database connections don't necessarily need to have a $config
		// array. If they don't, we just return the original.
		
		$conn = new class() implements ConnectionInterface {
			public $foo = 'bar';
			
			public function table($table, $as = null)
			{
			}
			
			public function raw($value)
			{
			}
			
			public function selectOne($query, $bindings = [], $useReadPdo = true)
			{
			}
			
			public function select($query, $bindings = [], $useReadPdo = true)
			{
			}
			
			public function cursor($query, $bindings = [], $useReadPdo = true)
			{
			}
			
			public function insert($query, $bindings = [])
			{
			}
			
			public function update($query, $bindings = [])
			{
			}
			
			public function delete($query, $bindings = [])
			{
			}
			
			public function statement($query, $bindings = [])
			{
			}
			
			public function affectingStatement($query, $bindings = [])
			{
			}
			
			public function unprepared($query)
			{
			}
			
			public function prepareBindings(array $bindings)
			{
			}
			
			public function transaction(Closure $callback, $attempts = 1)
			{
			}
			
			public function beginTransaction()
			{
			}
			
			public function commit()
			{
			}
			
			public function rollBack()
			{
			}
			
			public function transactionLevel()
			{
			}
			
			public function pretend(Closure $callback)
			{
			}
			
			public function getDatabaseName()
			{
			}
		};
		
		$expected = <<<EOD
		Illuminate\Database\ConnectionInterface@anonymous {
		  +foo: "bar"
		}
		EOD;
		
		$this->assertDumpEquals($expected, $conn);
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
