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
        'date_format' => 'string',
        'time' => 'string',
        'uuid' => 'string',
        'ulid' => 'string',
        'ip' => 'string',
        'ipv4' => 'string',
        'ipv6' => 'string',
        'mac_address' => 'string',
        'alpha' => 'string',
        'alpha_num' => 'string',
        'alpha_dash' => 'string',
        'hex_color' => 'string',
        'timezone' => 'string',
        'password' => 'string',

        // numbers
        'integer' => 'number',
        'numeric' => 'number',
        'decimal' => 'number',
        'float' => 'number',
        'double' => 'number',

        // boolean
        'boolean' => 'boolean',
        'bool' => 'boolean',
        'accepted' => 'boolean',
        'declined' => 'boolean',

        // file
        'file' => 'File',
        'image' => 'File',

        // complex
        'array' => 'any[]',
        'json' => 'any',
        'object' => 'Record<string, any>',
    ];
}
