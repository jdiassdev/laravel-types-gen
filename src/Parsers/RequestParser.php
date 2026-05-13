<?php

namespace Jdiassdev\LaravelTypesGen\Parsers;

class RequestParser
{
  public function parse(string $file): ?array
  {
    $className = basename($file, '.php');
    $content = file_get_contents($file);

    return [
      'name' => $className,
      'rules' => $this->extractRules($content)
    ];
  }

  private function extractRules(string $content): array
  {
    $block = $this->extractRulesBlock($content);

    if ($block === null) {
      return [];
    }

    return $this->parseRulesBlock($block);
  }

  private function extractRulesBlock(string $content): ?string
  {
    $pos = strpos($content, 'return [');

    if ($pos === false) {
      return null;
    }

    $start = $pos + strlen('return [');
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

  private function parseRulesBlock(string $block): array
  {
    $rules = [];

    // array-style: 'field' => ['rule1', 'rule2']
    preg_match_all('/[\'"](?<key>[^\'"]+)[\'"]\s*=>\s*\[(?<value>[^\]]*)\]/s', $block, $arrayMatches, PREG_SET_ORDER);

    foreach ($arrayMatches as $match) {
      preg_match_all('/[\'"]([^\'"]+)[\'"]/', $match['value'], $items);
      $rules[$match['key']] = implode('|', $items[1]);
    }

    // string-style: 'field' => 'rule1|rule2'
    preg_match_all('/[\'"](?<key>[^\'"]+)[\'"]\s*=>\s*[\'"](?<value>[^\'"]*)[\'"]/', $block, $stringMatches, PREG_SET_ORDER);

    foreach ($stringMatches as $match) {
      if (!isset($rules[$match['key']])) {
        $rules[$match['key']] = $match['value'];
      }
    }

    return $rules;
  }
}
