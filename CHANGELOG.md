# Changelog

All notable changes will be documented in this file following the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) 
format. This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed

- Fixed how bindings are merged into SQL queries when dumping a query builder

## [1.0.0] - 2022-01-24

## [0.5.0] - 2022-01-23

### Changed

-   Removed the `LaravelDumper` facade in favor of registering custom casters directly

## [0.4.0]

### Added

-   Added Laravel 9 support

## [0.3.0]

### Added

-   Added support for dynamic custom casters
-   Added `LaravelDumper` facade
-   Added custom `Properties` collection for easier manipulation of dumped properties

### Changed

-   Changed `Caster` interface to use `Properties` collection
-   Updated all casters to use new `Properties` collection

## [0.2.0]

### Added

-   Added `ddf()` and `dumpf()` for access to original `dd()` and `dump()` behavior

## [0.1.0]

### Added

-   Added support for Requests and Responses
-   Added support for ParameterBags
-   Added support for HeaderBags

### Changed

-   Improved tests

## [0.0.1]

### Added

-   Initial release

# Keep a Changelog Syntax

-   `Added` for new features.
-   `Changed` for changes in existing functionality.
-   `Deprecated` for soon-to-be removed features.
-   `Removed` for now removed features.
-   `Fixed` for any bug fixes. 
-   `Security` in case of vulnerabilities.

[Unreleased]: https://github.com/glhd/laravel-dumper/compare/1.0.0...HEAD

[1.0.0]: https://github.com/glhd/laravel-dumper/compare/0.5.0...1.0.0

[0.5.0]: https://github.com/glhd/laravel-dumper/compare/0.4.0...0.5.0

[0.4.0]: https://github.com/glhd/laravel-dumper/compare/0.3.0...0.4.0

[0.3.0]: https://github.com/glhd/laravel-dumper/compare/0.2.0...0.3.0

[0.2.0]: https://github.com/glhd/laravel-dumper/compare/0.1.0...0.2.0

[0.1.0]: https://github.com/glhd/laravel-dumper/compare/0.0.1...0.1.0

[0.0.1]: https://github.com/glhd/laravel-dumper
