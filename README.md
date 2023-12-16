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

Just use `dd()` as you would normally, and enjoy the newly curated output!

## Original Dump Output
If, for some reason, you really need the full debug output for an object that `laravel-dumper` customizes, you can
do a "full" dump with `ddf()` and `dumpf()`.

## Comparison to Default Output

You can see comparisons between the default `dd()` output and the `laravel-dumper` output
in the [diffs directory of this repository](./diffs/).

## Custom Casters

Due to [changes in how Laravel registers the var dumper](https://github.com/laravel/framework/pull/44211) it
is no longer possible to register custom casters.
