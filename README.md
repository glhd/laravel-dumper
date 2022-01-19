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

<table border="0">
<tbody>
    <tr>
        <td width="50%" valign="top">
            <h3>Before</h3>
            <img alt="Animated gif scrolling through hundreds of lines of debug output" src="https://user-images.githubusercontent.com/21592/150063119-10fd364b-17be-4b71-a0bc-2b0882292e17.gif" />
        </td>
        <td width="50%" valign="top">
            <h3>After</h3>
            <img alt="Small screenshot of query builder output with Laravel Dumper installed" src="https://user-images.githubusercontent.com/21592/150063393-5f69637f-39ad-406c-bac0-f489dd86e521.png" />
        </td>
    </tr>
</tbody>
</table>


## Installation

Install as a dev dependency:

```shell
# composer require glhd/laravel-dumper --dev
```

## Usage

Just use `dd()` as you would normally, and enjoy the newly curated output!
