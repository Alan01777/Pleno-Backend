<?php

namespace Tests\Feature\routes;

use Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class ApiTest extends TestCase
{
    use MakesHttpRequests;

    // Healthcheck Test
    public function test_Healthcheck()
    {
        $response = $this->get('/api/healthcheck');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }
}
