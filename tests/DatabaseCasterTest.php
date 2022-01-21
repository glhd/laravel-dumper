<?php

namespace Glhd\LaravelDumper\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class DatabaseCasterTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_it_casts_eloquent_models(): void
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
		     …%d
		  }
		   …%d
		}
		EOD;

		$this->assertDumpMatchesFormat($expected, $builder);
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
		     …%d
		  }
		  #parent: Glhd\LaravelDumper\Tests\User { …}
		  #related: Glhd\LaravelDumper\Tests\Company { …}
		   …%d
		}
		EOD;
		
		$this->assertDumpMatchesFormat($expected, $user->company());
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
