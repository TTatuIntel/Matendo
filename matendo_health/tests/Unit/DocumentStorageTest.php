<?php

namespace Tests\Unit;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_documents_are_saved_and_retrieved_from_database(): void
    {
        $binary = random_bytes(16);

        $app = Application::create([
            'reference_number' => 'REFTEST',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Street',
            'profession' => 'Doctor',
            'years_experience' => 5,
            'resume' => $binary,
        ]);

        $this->assertNotNull($app->id);
        $this->assertEquals($binary, $app->fresh()->resume);
    }
}
