<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JoinControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_join_endpoint_requires_csrf_token()
    {
        $response = $this->post('/api/v1/join', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertEquals(419, $response->status());
    }

    public function test_join_endpoint_works_with_csrf_token()
    {
        // Create test PDF files
        $resume = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');
        $license = UploadedFile::fake()->create('license.pdf', 100, 'application/pdf');
        $certifications = UploadedFile::fake()->create('certs.pdf', 100, 'application/pdf');

        $response = $this->withoutMiddleware()->post('/api/v1/join', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'location' => 'City',
            'coordinates' => '0,0',
            'profession' => 'Doctor',
            'specialization' => 'General',
            'yearsExperience' => 5,
            'licenseNumber' => 'LIC123',
            'workType' => ['Full-time', 'Part-time'],
            'shiftType' => ['Day', 'Night'],
            'preferredLocation' => 'City Center',
            'startDate' => '2025-09-01',
            'resume' => $resume,
            'license' => $license,
            'certifications' => $certifications
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertDatabaseHas('applications', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com'
        ]);
    }
}
