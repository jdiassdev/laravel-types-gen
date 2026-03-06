<?php

namespace Jdiassdev\LaravelTypesGen\Mappers;

use Jdiassdev\LaravelTypesGen\Rules\RuleModifiers;
use Jdiassdev\LaravelTypesGen\Rules\TypeRules;

class TypeMapper
{
    public static function map(array $rules): string
    {
        $type = 'any';
        $modifiers = '';

        foreach ($rules as $rule) {

            if (isset(TypeRules::TYPES[$rule])) {
                $type = TypeRules::TYPES[$rule];
            }

            if (isset(RuleModifiers::MODIFIERS[$rule])) {
                $modifiers .= RuleModifiers::MODIFIERS[$rule];
            }
        }

        return $type . $modifiers;
    }
}
