<?php

namespace Jdiassdev\LaravelTypesGen\Rules;

class TypeRules
{
    public const TYPES = [

        // string
        'string' => 'string',
        'email' => 'string',
        'url' => 'string',
        'date' => 'string',
        'datetime' => 'string',
        'time' => 'string',
        'uuid' => 'string',
        'ip' => 'string',
        'mac_address' => 'string',

        // numbers
        'integer' => 'number',
        'numeric' => 'number',
        'decimal' => 'number',
        'float' => 'number',
        'double' => 'number',

        // boolean
        'boolean' => 'boolean',
        'bool' => 'boolean',

        // complex
        'array' => 'any[]',
        'json' => 'any',

    ];
}
