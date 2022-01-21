<div style="float: right;">
	<a href="https://github.com/glhd/laravel-dumper/actions" target="_blank">
		<img 
			src="https://github.com/glhd/laravel-dumper/workflows/PHPUnit/badge.svg" 
			alt="Build Status" 
		/>
	</a>
	<a href="https://codeclimate.com/github/glhd/laravel-dumper/test_coverage" target="_blank">
		<img 
			src="https://api.codeclimate.com/v1/badges/f597a6e8d9f968a55f03/test_coverage" 
			alt="Coverage Status" 
		/>
	</a>
	<a href="https://packagist.org/packages/glhd/laravel-dumper" target="_blank">
        <img 
            src="https://poser.pugx.org/glhd/laravel-dumper/v/stable" 
            alt="Latest Stable Release" 
        />
	</a>
	<a href="./LICENSE" target="_blank">
        <img 
            src="https://poser.pugx.org/glhd/laravel-dumper/license" 
            alt="MIT Licensed" 
        />
    </a>
    <a href="https://twitter.com/inxilpro" target="_blank">
        <img 
            src="https://img.shields.io/twitter/follow/inxilpro?style=social" 
            alt="Follow @inxilpro on Twitter" 
        />
    </a>
</div>

# Laravel Dumper

Improve the default output of `dump()` and `dd()` in Laravel projects. Improves the default
dump behavior for many core Laravel objects, including:

- Models
- Query Builders
- Service Container
- Database Connections
- Carbon Instances
- Requests and Responses

https://user-images.githubusercontent.com/21592/150163719-547ecd90-b029-4588-9648-34891e5e0886.mp4

## Installation

Install as a dev dependency:

```shell
# composer require glhd/laravel-dumper --dev
```

## Usage

Just use `dd()` as you would normally, and enjoy the newly curated output! If, for some reason,
you really need the full debug output for an object that `laravel-dumper` customizes, you can
do a "full" dump with `ddf()` and `dumpf()`.

## Custom Casters

> Please note, the API for custom casting is likely to change. Use at your own risk!

It's possible to register your own casters for any class by publishing the `laravel-dumper`
config file and registering your custom classes in the `'casters'` section of the config.

Your custom casters should extend `Glhd\LaravelDumper\Casters\Caster` and would look
something like (this example would mean that dumped `User` objects would only include
a single piece of `summary` data):

```php
class UserCaster extends Caster
{
	public static array $targets = [User::class];
	
	public function cast($target, array $properties, Stub $stub, bool $is_nested, int $filter = 0): array
	{
		return [
		    Key::virtual('summary') => "$target->name ($target->email)",
        ];
	}
}
```
