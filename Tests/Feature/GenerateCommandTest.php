<?php

namespace Jdiassdev\LaravelTypesGen\Tests\Feature;

use Jdiassdev\LaravelTypesGen\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Jdiassdev\LaravelTypesGen\Tests\BaseTestCase;

class GenerateCommandTest extends BaseTestCase
{
  public function test_it_runs_the_command_successfully()
  {
    $tempPath = app_path('Http/Requests');

    if (!File::isDirectory($tempPath)) {
      File::makeDirectory($tempPath, 0755, true);
    }

    File::put(
      $tempPath . '/UserRequest.php',
      "<?php

        namespace App\Http\Requests;

        use Illuminate\Foundation\Http\FormRequest;

        class UserRequest extends FormRequest {

            public function rules(): array
            {
                return [
                    'email' => 'required|email',
                    'date' => 'required|boolean',
                    'age' => 'required|integer|nullable',
                ];
            }
        }"
    );

    $this->artisan('ts:generate-requests')
      ->assertExitCode(0);

    $file = resource_path('types/api-request.ts');

    $this->assertTrue(File::exists($file));

    $content = File::get($file);

    $this->assertStringContainsString('export interface UserRequest', $content);
    $this->assertStringContainsString('email: string', $content);
    $this->assertStringContainsString('date: boolean', $content);
    $this->assertStringContainsString('age: number | null', $content);
  }
}
