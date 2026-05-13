# Laravel TypesGen

[![Tests](https://github.com/jdiassdev/laravel-types-gen/actions/workflows/tests.yml/badge.svg)](https://github.com/jdiassdev/laravel-types-gen/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/jdiassdev/laravel-types-gen.svg?style=flat-square)](https://packagist.org/packages/jdiassdev/laravel-types-gen)
[![Total Downloads](https://img.shields.io/packagist/dt/jdiassdev/laravel-types-gen.svg?style=flat-square)](https://packagist.org/packages/jdiassdev/laravel-types-gen)
[![PHP Version](https://img.shields.io/packagist/php-v/jdiassdev/laravel-types-gen?style=flat-square)](https://packagist.org/packages/jdiassdev/laravel-types-gen)
[![License](https://img.shields.io/packagist/l/jdiassdev/laravel-types-gen?style=flat-square)](LICENSE)

Generate TypeScript interfaces automatically from your Laravel **FormRequests** and **API Resources**. Keep your backend and frontend contracts in sync without any manual duplication.

```bash
php artisan ts:generate
```

```ts
// resources/types/api-request.ts — auto-generated

export interface StoreUserRequest {
  name: string;
  email: string;
  age: number | null;
  bio?: string;
}
```

---

## Requirements

- PHP 8.2+
- Laravel 11 or 12

---

## Installation

Install via Composer:

```bash
composer require jdiassdev/laravel-types-gen
```

The service provider is registered automatically via Laravel's package discovery. No additional setup required.

---

## Usage

Run the artisan command from your project root:

```bash
php artisan ts:generate
```

Two files are generated inside `resources/types/`:

| File | Source |
|---|---|
| `api-request.ts` | `app/Http/Requests/**/*Request.php` |
| `api-resource.ts` | `app/Http/Resources/**/*Resource.php` |

---

## How it works

The package performs **static analysis** — it reads your PHP files directly without instantiating any class or booting the framework. This means it is safe to run at any point, including CI pipelines.

---

## FormRequests

Both rule formats are supported.

**String style**

```php
public function rules(): array
{
    return [
        'name'  => 'required|string',
        'email' => 'required|email',
        'age'   => 'required|integer|nullable',
        'bio'   => 'string',
    ];
}
```

**Array style**

```php
public function rules(): array
{
    return [
        'name'  => ['required', 'string'],
        'tags'  => ['required', 'array'],
    ];
}
```

Generated output:

```ts
export interface StoreUserRequest {
  name: string;
  email: string;
  age: number | null;
  bio?: string;   // no `required` → optional
  tags: any[];
}
```

> Fields that do not have the `required` rule are emitted as optional (`field?: type`).

---

## Nested fields

Dot-notation fields are converted into nested TypeScript objects and arrays.

```php
public function rules(): array
{
    return [
        'address.city'   => 'required|string',
        'address.street' => 'string',
        'items.*.name'   => 'required|string',
        'items.*.qty'    => 'required|integer',
    ];
}
```

```ts
export interface StoreOrderRequest {
  address: {
    city: string;
    street?: string;
  };
  items: {
    name: string;
    qty: number;
  }[];
}
```

---

## API Resources

The package reads the return array of `toArray()` and extracts the field names. Since values depend on runtime data, all resource fields default to `any`.

```php
public function toArray(Request $request): array
{
    return [
        'id'         => $this->id,
        'name'       => $this->name,
        'email'      => $this->email,
        'created_at' => $this->created_at,
    ];
}
```

```ts
export interface UserResource {
  id: any;
  name: any;
  email: any;
  created_at: any;
}
```

---

## Type mapping

| Laravel rule | TypeScript type |
|---|---|
| `string`, `email`, `url`, `date`, `uuid`, `ulid`, `ip`, `timezone`, `password` | `string` |
| `integer`, `numeric`, `decimal`, `float`, `double` | `number` |
| `boolean`, `accepted`, `declined` | `boolean` |
| `array` | `any[]` |
| `file`, `image` | `File` |
| `json` | `any` |
| `object` | `Record<string, any>` |
| `nullable` modifier | `type \| null` |
| field without `required` | `field?: type` |

---

## Configuration

The output path can be customized. Publish the config file:

```bash
php artisan vendor:publish --tag=laravel-types-gen-config
```

```php
// config/laravel-types-gen.php

return [
    /*
    |--------------------------------------------------------------------------
    | Output Path
    |--------------------------------------------------------------------------
    |
    | The directory where the generated TypeScript files will be written.
    | Both api-request.ts and api-resource.ts are placed inside this path.
    |
    */
    'output_path' => resource_path('types'),
];
```

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for information on recent changes.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover a security vulnerability, please review our [security policy](.github/SECURITY.md) and report it responsibly.

## Credits

- [João Dias](https://github.com/jdiassdev)
- [All Contributors](https://github.com/jdiassdev/laravel-types-gen/contributors)

## License

The MIT License. Please see [LICENSE](LICENSE) for more information.
