<?php

namespace Glhd\LaravelDumper\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

class DatabaseCasterTest extends TestCase
{
	use RefreshDatabase;
	use VarDumperTestTrait;
	
	public function test_it_casts_eloquent_models(): void
	{
		Date::setTestNow($now = now());
		
		$company = Company::create(['id' => 1, 'name' => 'Galahad']);
		$user = User::create(['id' => 1, 'name' => 'John', 'email' => 'foo@bar.com', 'company_id' => 1]);
		$user->setRelation('company', $company);
		$user->name = 'Chris';
		
		$timestamp = $now->format('Y-m-d H:i:s');
		
		$expected = <<<EOD
		Glhd\LaravelDumper\Tests\User {
		  +"id": 1
		  +"company_id": 1
		  +"email": "foo@bar.com"
		  +"name": "Chris"
		  +"created_at": "{$timestamp}"
		  +"updated_at": "{$timestamp}"
		  isDirty(): true
		  +exists: true
		  +wasRecentlyCreated: true
		  #relations: array:1 [
		    "company" => Glhd\LaravelDumper\Tests\Company {
		      +"id": 1
		      +"name": "Galahad"
		      +"created_at": "{$timestamp}"
		      +"updated_at": "{$timestamp}"
		      isDirty(): false
		      +exists: true
		      +wasRecentlyCreated: true
		      #relations: []
		       …27
		    }
		  ]
		  #connection: "testing"
		  #table: "users"
		  #original: array:6 [
		    "id" => 1
		    "name" => "John"
		    "email" => "foo@bar.com"
		    "company_id" => 1
		    "updated_at" => "{$timestamp}"
		    "created_at" => "{$timestamp}"
		  ]
		  #changes: []
		   …23
		}
		EOD;
		
		$this->assertDumpEquals($expected, $user);
	}
	
	public function test_it_dumps_basic_query_builders(): void
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
		     …22
		  }
		   …21
		}
		EOD;

		$this->assertDumpEquals($expected, $builder);
	}
	
	public function test_it_dumps_eloquent_query_builders(): void
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
		     …22
		  }
		  #model: Glhd\LaravelDumper\Tests\User { …}
		  #eagerLoad: array:1 [
		    "company" => Closure() {
		      class: "Illuminate\Database\Eloquent\Builder"
		      file: "./vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php"
		      line: "1364 to 1366"
		    }
		  ]
		   …5
		}
		EOD;
		
		$this->assertDumpEquals($expected, $builder);
	}
	
	public function test_it_dumps_eloquent_relations(): void
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
		     …22
		  }
		  #parent: Glhd\LaravelDumper\Tests\User { …}
		  #related: Glhd\LaravelDumper\Tests\Company { …}
		   …4
		}
		EOD;
		
		$this->assertDumpEquals($expected, $user->company());
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
