<?php

namespace Jdiassdev\LaravelTypesGen\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Jdiassdev\LaravelTypesGen\Generators\TypeScriptGenerator;
use Jdiassdev\LaravelTypesGen\Mappers\TypeMapper;
use Jdiassdev\LaravelTypesGen\Parsers\RequestParser;
use Jdiassdev\LaravelTypesGen\Parsers\ResourceParser;
use Jdiassdev\LaravelTypesGen\Parsers\RuleParser;
use Jdiassdev\LaravelTypesGen\Scanners\RequestScanner;
use Jdiassdev\LaravelTypesGen\Scanners\ResourceScanner;

class GenerateRequestTypesCommand extends Command
{
    protected $signature = 'ts:generate';
    protected $description = 'generate typescript types from laravel formrequests and api resources';

    public function handle()
    {
        $outputPath = config('laravel-types-gen.output_path', $this->laravel->basePath('resources/types'));
        $generator = new TypeScriptGenerator();

        if (!File::exists($outputPath)) {
            File::makeDirectory($outputPath, 0755, true);
        }

        File::put($outputPath . '/api-request.ts', $generator->generate($this->buildRequestTypes()));
        File::put($outputPath . '/api-resource.ts', $generator->generate($this->buildResourceTypes()));

        $this->info('types generated!');
    }

    private function buildRequestTypes(): array
    {
        $files = (new RequestScanner())->scan($this->laravel->basePath('app/Http/Requests'));
        $parser = new RequestParser();
        $types = [];

        foreach ($files as $file) {
            $parsed = $parser->parse($file);

            if (!$parsed) {
                continue;
            }

            $fields = [];

            foreach ($parsed['rules'] as $field => $ruleString) {
                $fields[$field] = TypeMapper::map(RuleParser::parse($ruleString));
            }

            $types[$parsed['name']] = $fields;
        }

        return $types;
    }

    private function buildResourceTypes(): array
    {
        $files = (new ResourceScanner())->scan($this->laravel->basePath('app/Http/Resources'));
        $parser = new ResourceParser();
        $types = [];

        foreach ($files as $file) {
            $parsed = $parser->parse($file);

            if (!$parsed) {
                continue;
            }

            $types[$parsed['name']] = $parsed['fields'];
        }

        return $types;
    }
}
