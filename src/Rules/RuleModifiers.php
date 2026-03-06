<?php

namespace Jdiassdev\LaravelTypesGen\Rules;

class RuleModifiers
{
    public const MODIFIERS = [

        'nullable' => ' | null',

        // esses não mudam tipo
        'required' => '',
        'sometimes' => '',
        'filled' => '',
    ];
}
