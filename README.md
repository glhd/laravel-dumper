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

Sample output:

![Screen shot of dump output](https://user-images.githubusercontent.com/21592/150059496-a9d5dffc-1538-43b8-96b5-6f62f0ee6f68.png)

## Installation

Install as a dev dependency:

```shell
# composer require glhd/laravel-dumper --dev
```

## Usage

Just use `dd()` as you would normally, and enjoy the newly curated output!
