<?php

namespace Jdiassdev\LaravelTypesGen\Commands;

use Illuminate\Console\Command;
use Jdiassdev\LaravelTypesGen\Parsers\RequestParser;
use Jdiassdev\LaravelTypesGen\Generators\TypeScriptGenerator;
use Illuminate\Support\Facades\File;
use Jdiassdev\LaravelTypesGen\Mappers\TypeMapper;
use Jdiassdev\LaravelTypesGen\Parsers\RuleParser;
use Jdiassdev\LaravelTypesGen\Scanners\RequestScanner;

class GenerateRequestTypesCommand extends Command
{
  protected $signature = 'ts:generate-requests';
  protected $description = 'Generate TypeScript types from Laravel FormRequests';

  public function handle()
  {
    $path = $this->laravel->basePath('app/Http/Requests');

    $scanner = new RequestScanner();
    $parser = new RequestParser();

    $files = $scanner->scan($path);

    $types = [];

    foreach ($files as $file) {

      $parsed = $parser->parse($file);

      if (!$parsed) {
        continue;
      }

      $className = $parsed['name'];
      $rules = $parsed['rules'];

      $fields = [];

      foreach ($rules as $field => $ruleString) {

        $parsedRules = RuleParser::parse($ruleString);

        $fields[$field] = TypeMapper::map($parsedRules);
      }

      $types[$className] = $fields;
    }

    $generator = new TypeScriptGenerator();
    $tsContent = $generator->generate($types);
    dump("Generated TypeScript content:\n$tsContent");

    $outputPath = $this->laravel->basePath('resources/types');

    if (!File::exists($outputPath)) {
      File::makeDirectory($outputPath, 0755, true);
    }

    File::put($outputPath . '/api-request.ts', $tsContent);

    $this->info('Types generated!');
    dump('Generation complete.');
  }
}
