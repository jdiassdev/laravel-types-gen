# Changelog

All notable changes to `laravel-types-gen` will be documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-05-12

### Added
- Generate TypeScript interfaces from Laravel FormRequests
- Generate TypeScript interfaces from Laravel API Resources
- Support for string-style rules (`'required|email'`)
- Support for array-style rules (`['required', 'email']`)
- Optional fields: fields without `required` are emitted as `field?: type`
- Dot-notation fields converted to nested TypeScript objects (`address.city`)
- Array of objects via wildcard notation (`items.*.name`)
- Configurable output path via `config/laravel-types-gen.php`
- `php artisan ts:generate` command
- Laravel 11 and 12 support
- PHP 8.2 and 8.3 support
