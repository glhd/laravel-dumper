<div style="float: right;">
	<a href="https://github.com/glhd/laravel-dumper/actions" target="_blank">
		<img 
			src="https://github.com/glhd/laravel-dumper/workflows/PHPUnit/badge.svg" 
			alt="Build Status" 
		/>
	</a>
	<a href="https://codeclimate.com/github/glhd/laravel-dumper/test_coverage" target="_blank">
		<img 
			src="https://api.codeclimate.com/v1/badges/89d825bd1cba002b271c/test_coverage" 
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

## Comparison to Default Output

You can see comparisons between the default `dd()` output and the `laravel-dumper` output
in the [diffs directory of this repository](./diffs/).

## Custom Casters

If there are objects in your project that you would like to customize the `dd()` behavior
for, you can register custom casters using the `CustomCaster` class:

```php
use Glhd\LaravelDumper\Casters\CustomCaster;

CustomCaster::for(User::class)
    ->only(['attributes', 'exists', 'wasRecentlyCreated']) // Props to keep (or use `except` to exclude)
    ->virtual('admin', fn(User $user) => $user->isAdmin()) // Add virtual props
    ->filter() // Filter out empty/null props (accepts callback)
    ->reorder(['attributes', 'admin', '*']); // Adjust the order of props
```

The `reorder` method accepts an array of patterns. For example, the default `Model` caster
uses the following ordering rules:

```php
$order = [
  'id',
  '*_id',
  '*',
  '*_at',
  'created_at',
  'updated_at',
  'deleted_at',
];
```

This ensures that `id` is always first, followed by all foreign keys, followed by all
other attributes, and then finally followed by timestamp attributes (with `deleted_at` last). 
By applying bespoke ordering rules, you can make sure that the properties you usually
need to debug are at the top of the `dd()` output.

### Advanced Custom Casters

It's also possible to register your own casters for any class by publishing the `laravel-dumper`
config file and registering your custom classes in the `'casters'` section of the config.
This gives you the same level of control over the `dd()` output as the core Symfony
VarDumper package, but is more complex to implement.

Your custom casters should extend `Glhd\LaravelDumper\Casters\Caster` and implement the
`cast` method. See any of our [built-in casters](./src/Casters/) for more details.
