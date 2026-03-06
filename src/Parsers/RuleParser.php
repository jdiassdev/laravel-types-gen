<?php

namespace Jdiassdev\LaravelTypesGen\Parsers;

class RuleParser
{
    public static function parse(array|string $rules): array
    {
        if (is_array($rules)) {
            return $rules;
        }

        return array_map(
            fn($rule) => trim(explode(':', $rule)[0]),
            explode('|', $rules)
        );
    }
}
