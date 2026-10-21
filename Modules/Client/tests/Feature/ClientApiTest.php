<?php

namespace Modules\Client\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Client\Models\Client;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_client_and_calculates_the_pricing_fields(): void
    {
        $response = $this->postJson('/api/v1/clients', [
            ...$this->clientData(),
            'company_employee_count' => 101,
            'project_budget' => '1000000.00',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.service_charge', '100000.00')
            ->assertJsonPath('data.discount_value', '50000.00')
            ->assertJsonPath('data.final_project_value', '1050000.00');

        $this->assertDatabaseHas('clients', [
            'phone' => '5551234567',
            'project_budget' => '1000000.00',
            'final_project_value' => '1050000.00',
        ]);
    }

    public function test_service_charge_bands_and_employee_discount_boundaries_are_applied(): void
    {
        $cases = [
            ['budget' => '1000000.00', 'employees' => 100, 'charge' => '100000.00', 'discount' => '0.00'],
            ['budget' => '500000.00', 'employees' => 99, 'charge' => '25000.00', 'discount' => '10000.00'],
            ['budget' => '499999.99', 'employees' => 101, 'charge' => '10000.00', 'discount' => '25000.00'],
        ];

        foreach ($cases as $index => $case) {
            $response = $this->postJson('/api/v1/clients', [
                ...$this->clientData(),
                'email' => "client{$index}@example.com",
                'phone' => "555123456{$index}",
                'company_employee_count' => $case['employees'],
                'project_budget' => $case['budget'],
            ]);

            $response
                ->assertCreated()
                ->assertJsonPath('data.service_charge', $case['charge'])
                ->assertJsonPath('data.discount_value', $case['discount']);
        }
    }

    public function test_it_lists_searches_updates_and_deletes_clients(): void
    {
        $client = Client::query()->create([
            ...$this->clientData(),
            'company_employee_count' => 101,
            'project_budget' => '600000.00',
            'service_charge' => '30000.00',
            'discount_value' => '30000.00',
            'final_project_value' => '600000.00',
        ]);

        $this->getJson('/api/v1/clients')
            ->assertOk()
            ->assertJsonPath('data.0.id', $client->id);

        $this->getJson('/api/v1/clients/search?phone=5551234567')
            ->assertOk()
            ->assertJsonPath('data.id', $client->id);

        $this->getJson('/api/v1/clients/search?phone=0000000000')->assertNotFound();

        $this->patchJson("/api/v1/clients/{$client->id}", [
            'phone' => '5557654321',
            'address' => 'Updated address',
            'project_budget' => '1000000.00',
        ])
            ->assertOk()
            ->assertJsonPath('data.phone', '5557654321')
            ->assertJsonPath('data.service_charge', '100000.00')
            ->assertJsonPath('data.discount_value', '50000.00')
            ->assertJsonPath('data.final_project_value', '1050000.00');

        $this->deleteJson("/api/v1/clients/{$client->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_it_validates_required_fields_and_unique_phone_numbers(): void
    {
        $this->postJson('/api/v1/clients', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'phone',
                'company',
                'company_employee_count',
                'address',
                'project_budget',
            ]);

        $this->postJson('/api/v1/clients', $this->clientData())
            ->assertCreated();

        $this->postJson('/api/v1/clients', [
            ...$this->clientData(),
            'email' => 'another@example.com',
        ])->assertUnprocessable()->assertJsonValidationErrors(['phone']);

        $this->postJson('/api/v1/clients', [
            ...$this->clientData(),
            'phone' => '5557654321',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->postJson('/api/v1/clients', [
            ...$this->clientData(),
            'email' => 'decimal@example.com',
            'phone' => '5550000001',
            'company_employee_count' => 1,
            'project_budget' => '1.001',
        ])->assertUnprocessable()->assertJsonValidationErrors(['project_budget']);

        $this->postJson('/api/v1/clients', [
            ...$this->clientData(),
            'email' => 'trailing-decimal@example.com',
            'phone' => '5550000002',
            'company_employee_count' => 100,
            'project_budget' => '1.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.project_budget', '1.00')
            ->assertJsonPath('data.final_project_value', '1.02');
    }

    public function test_it_allows_configured_frontend_origins_and_preflight_requests(): void
    {
        config(['cors.allowed_origins' => ['http://localhost:5173']]);

        $this->withHeader('Origin', 'http://localhost:5173')
            ->getJson('/api/v1/clients')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');

        $this->call('OPTIONS', '/api/v1/clients', [], [], [], [
            'HTTP_ORIGIN' => 'http://localhost:5173',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'content-type',
        ])
            ->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_it_does_not_allow_unconfigured_frontend_origins(): void
    {
        config(['cors.allowed_origins' => ['http://localhost:5173']]);

        $this->withHeader('Origin', 'http://untrusted.example')
            ->getJson('/api/v1/clients')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
    }

    private function clientData(): array
    {
        return [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '5551234567',
            'company' => 'Analytical Engines Ltd',
            'company_employee_count' => 50,
            'address' => '10 Example Street',
            'project_budget' => '400000.00',
        ];
    }
}
