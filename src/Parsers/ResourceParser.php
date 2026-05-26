<?php

namespace Jdiassdev\LaravelTypesGen\Parsers;

class ResourceParser
{
    private const PHPDOC_TYPE_MAP = [
        'int'     => 'number',
        'integer' => 'number',
        'float'   => 'number',
        'double'  => 'number',
        'decimal' => 'number',
        'bool'    => 'boolean',
        'boolean' => 'boolean',
        'string'  => 'string',
        'array'   => 'any[]',
        'object'  => 'Record<string, any>',
        'mixed'   => 'any',
    ];

    private const CAST_TYPE_MAP = [
        'int'     => 'number',
        'integer' => 'number',
        'float'   => 'number',
        'double'  => 'number',
        'bool'    => 'boolean',
        'boolean' => 'boolean',
        'string'  => 'string',
        'array'   => 'any[]',
        'object'  => 'Record<string, any>',
    ];

    public function parse(string $file): ?array
    {
        $className = basename($file, '.php');
        $content = file_get_contents($file);

        $fields = $this->extractFields($content);

        if (empty($fields)) {
            return null;
        }

        return [
            'name'   => $className,
            'fields' => $fields,
        ];
    }

    private function extractFields(string $content): array
    {
        $block = $this->extractToArrayBlock($content);

        if ($block === null) {
            return [];
        }

        // Strategy 1: @return array{key: type, ...} no PHPDoc do toArray
        $phpDocTypes = $this->extractPhpDocTypes($content);
        if (!empty($phpDocTypes)) {
            return $phpDocTypes;
        }

        // Strategy 2: casts explícitos nos valores — (int), (string), etc.
        return $this->parseFieldsWithCasts($block);
    }

    private function extractToArrayBlock(string $content): ?string
    {
        $methodPos = strpos($content, 'function toArray');

        if ($methodPos === false) {
            return null;
        }

        $returnPos = strpos($content, 'return [', $methodPos);

        if ($returnPos === false) {
            return null;
        }

        $start = $returnPos + strlen('return [');
        $depth = 1;
        $i = $start;
        $len = strlen($content);

        while ($i < $len && $depth > 0) {
            if ($content[$i] === '[') $depth++;
            if ($content[$i] === ']') $depth--;
            $i++;
        }

        return substr($content, $start, $i - $start - 1);
    }

    private function extractPhpDocTypes(string $content): array
    {
        $methodPos = strpos($content, 'function toArray');
        if ($methodPos === false) {
            return [];
        }

        $before = substr($content, 0, $methodPos);

        if (!preg_match_all('/\/\*\*[\s\S]*?\*\//', $before, $matches)) {
            return [];
        }

        $phpDoc = end($matches[0]);

        if (!preg_match('/@return\s+array\{([^}]+)\}/', $phpDoc, $arrayMatch)) {
            return [];
        }

        return $this->parseArrayShape($arrayMatch[1]);
    }

    private function parseArrayShape(string $shape): array
    {
        // remove o * de cada linha do PHPDoc multiline
        $shape = preg_replace('/^\s*\*\s?/m', '', $shape);

        $fields = [];
        $entries = $this->splitShapeEntries($shape);

        foreach ($entries as $entry) {
            $entry = trim($entry);
            if ($entry === '') {
                continue;
            }

            if (!preg_match('/^(\??)(\w+):\s*(.+)$/', $entry, $m)) {
                continue;
            }

            $nullable = $m[1] === '?';
            $key      = $m[2];
            $phpType  = trim($m[3]);

            [$tsType, $isNullable] = $this->phpTypeToTs($phpType);

            if ($nullable || $isNullable) {
                $tsType .= ' | null';
            }

            $fields[$key] = ['type' => $tsType, 'optional' => false];
        }

        return $fields;
    }

    private function splitShapeEntries(string $shape): array
    {
        $entries = [];
        $current = '';
        $depth   = 0;

        for ($i = 0; $i < strlen($shape); $i++) {
            $char = $shape[$i];

            if (in_array($char, ['<', '{', '('], true)) {
                $depth++;
                $current .= $char;
            } elseif (in_array($char, ['>', '}', ')'], true)) {
                $depth--;
                $current .= $char;
            } elseif ($char === ',' && $depth === 0) {
                $entries[] = $current;
                $current   = '';
            } else {
                $current .= $char;
            }
        }

        if (trim($current) !== '') {
            $entries[] = $current;
        }

        return $entries;
    }

    private function phpTypeToTs(string $phpType): array
    {
        $parts        = array_map('trim', explode('|', $phpType));
        $nonNullParts = array_values(array_filter($parts, fn($p) => $p !== 'null'));
        $isNullable   = count($nonNullParts) < count($parts);

        $primary = $nonNullParts[0] ?? 'any';
        $tsType  = self::PHPDOC_TYPE_MAP[$primary] ?? 'any';

        return [$tsType, $isNullable];
    }

    private function parseFieldsWithCasts(string $block): array
    {
        $fields = [];

        preg_match_all(
            '/[\'"](?<key>[^\'"]+)[\'"]\s*=>\s*(?:\((?<cast>\w+)\)\s*)?\$this->/',
            $block,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $key  = $match['key'];
            $cast = $match['cast'] ?? '';

            $type = ($cast !== '' && isset(self::CAST_TYPE_MAP[$cast]))
                ? self::CAST_TYPE_MAP[$cast]
                : 'any';

            $fields[$key] = ['type' => $type, 'optional' => false];
        }

        return $fields;
    }
}
