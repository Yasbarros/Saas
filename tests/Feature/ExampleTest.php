<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    protected $tenancy = true;

    public function test_returns_a_successful_response()
    {
        $response = $this->get('/');
        
        //TODO: Corrigir esse teste quando existir uma landing page e remover o tenancy = true
        //Temporariamente o status é 302 porque a rota redireciona para a página de login ou dashboard
        $response->assertStatus(302);
    }
}
