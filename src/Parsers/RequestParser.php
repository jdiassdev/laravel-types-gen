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
    preg_match('/return\s*\[(.*?)\]\s*;/s', $content, $matches);

    if (!isset($matches[1])) {
      return [];
    }

    return $this->convertRulesStringToMap($matches[1]);
  }

  private function convertRulesStringToMap(string $rulesRaw): array
  {
    $rules = [];

    $lines = explode("\n", str_replace("\r", "", $rulesRaw));

    foreach ($lines as $line) {
      if (preg_match('/[\'"](?<key>.*?)[\'"]\s*=>\s*[\'"](?<value>.*?)[\'"]/', $line, $match)) {
        $rules[$match['key']] = $match['value'];
      }
    }

    return $rules;
  }
}
