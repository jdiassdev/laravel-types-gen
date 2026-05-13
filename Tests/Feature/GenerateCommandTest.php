<?php

namespace Jdiassdev\LaravelTypesGen\Tests\Feature;

use Illuminate\Support\Facades\File;
use Jdiassdev\LaravelTypesGen\Tests\BaseTestCase;

class GenerateCommandTest extends BaseTestCase
{
    public function test_it_generates_request_types_with_string_style_rules()
    {
        $this->createRequest('UserRequest', "
            return [
                'email' => 'required|email',
                'date'  => 'required|boolean',
                'age'   => 'required|integer|nullable',
                'bio'   => 'string',
            ];
        ");

        $this->artisan('ts:generate')->assertExitCode(0);

        $content = File::get(resource_path('types/api-request.ts'));

        $this->assertStringContainsString('export interface UserRequest', $content);
        $this->assertStringContainsString('email: string', $content);
        $this->assertStringContainsString('date: boolean', $content);
        $this->assertStringContainsString('age: number | null', $content);
        $this->assertStringContainsString('bio?: string', $content);
    }

    public function test_it_generates_request_types_with_array_style_rules()
    {
        $this->createRequest('ProductRequest', "
            return [
                'name' => ['required', 'string'],
                'tags' => ['required', 'array'],
                'note' => ['string'],
            ];
        ");

        $this->artisan('ts:generate')->assertExitCode(0);

        $content = File::get(resource_path('types/api-request.ts'));

        $this->assertStringContainsString('export interface ProductRequest', $content);
        $this->assertStringContainsString('name: string', $content);
        $this->assertStringContainsString('tags: any[]', $content);
        $this->assertStringContainsString('note?: string', $content);
    }

    public function test_it_generates_resource_types()
    {
        $this->createResource('UserResource', "
            return [
                'id'    => \$this->id,
                'name'  => \$this->name,
                'email' => \$this->email,
            ];
        ");

        $this->artisan('ts:generate')->assertExitCode(0);

        $content = File::get(resource_path('types/api-resource.ts'));

        $this->assertStringContainsString('export interface UserResource', $content);
        $this->assertStringContainsString('id: any', $content);
        $this->assertStringContainsString('name: any', $content);
        $this->assertStringContainsString('email: any', $content);
    }

    public function test_it_generates_nested_types_from_dot_notation()
    {
        $this->createRequest('AddressRequest', "
            return [
                'address.city'   => 'required|string',
                'address.street' => 'string',
            ];
        ");

        $this->artisan('ts:generate')->assertExitCode(0);

        $content = File::get(resource_path('types/api-request.ts'));

        $this->assertStringContainsString('export interface AddressRequest', $content);
        $this->assertStringContainsString('address:', $content);
        $this->assertStringContainsString('city: string', $content);
        $this->assertStringContainsString('street?: string', $content);
    }

    private function createRequest(string $name, string $rulesBody): void
    {
        $path = app_path('Http/Requests');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        File::put("{$path}/{$name}.php", "<?php
            namespace App\Http\Requests;
            use Illuminate\Foundation\Http\FormRequest;
            class {$name} extends FormRequest {
                public function rules(): array { {$rulesBody} }
            }
        ");
    }

    private function createResource(string $name, string $toArrayBody): void
    {
        $path = app_path('Http/Resources');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        File::put("{$path}/{$name}.php", "<?php
            namespace App\Http\Resources;
            use Illuminate\Http\Resources\Json\JsonResource;
            class {$name} extends JsonResource {
                public function toArray(\$request): array { {$toArrayBody} }
            }
        ");
    }
}
