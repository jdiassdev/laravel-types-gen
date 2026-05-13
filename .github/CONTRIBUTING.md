# Contributing

Contributions are welcome and appreciated. Before opening a pull request, please check if there is an existing issue or discussion related to your change.

## Setup

```bash
git clone https://github.com/jdiassdev/laravel-types-gen.git
cd laravel-types-gen
composer install
```

## Running tests

```bash
composer test
```

All pull requests must pass the full test suite before being merged.

## Pull request guidelines

- Keep changes focused — one fix or feature per PR
- Add or update tests to cover your change
- Update the `CHANGELOG.md` under an `[Unreleased]` section
- Follow the existing code style (minimal comments, lowercase)

## Reporting bugs

Open an [issue](https://github.com/jdiassdev/laravel-types-gen/issues) and include:

- PHP and Laravel versions
- A minimal reproduction of the problem
- The actual vs expected output
