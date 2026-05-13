<?php

namespace Jdiassdev\LaravelTypesGen\Parsers;

class ResourceParser
{
    public function parse(string $file): ?array
    {
        $className = basename($file, '.php');
        $content = file_get_contents($file);

        $fields = $this->extractFields($content);

        if (empty($fields)) {
            return null;
        }

        return [
            'name' => $className,
            'fields' => $fields,
        ];
    }

    private function extractFields(string $content): array
    {
        $block = $this->extractToArrayBlock($content);

        if ($block === null) {
            return [];
        }

        return $this->parseFieldKeys($block);
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

    private function parseFieldKeys(string $block): array
    {
        $fields = [];

        preg_match_all('/[\'"](?<key>[^\'"]+)[\'"]\s*=>/', $block, $matches);

        foreach ($matches['key'] as $key) {
            $fields[$key] = ['type' => 'any', 'optional' => false];
        }

        return $fields;
    }
}
