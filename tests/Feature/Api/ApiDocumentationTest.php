<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_swagger_json_documentation_is_generated_and_accessible(): void
    {
        Artisan::call('l5-swagger:generate');

        $this->getJson('/api/docs.json')
            ->assertOk()
            ->assertJsonPath('openapi', '3.0.0')
            ->assertJsonPath('info.title', 'LaraAction API')
            ->assertJsonFragment([
                'summary' => 'Authenticate a user and issue a Sanctum token.',
            ]);
    }
}
