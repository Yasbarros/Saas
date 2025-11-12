<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Tenant;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

abstract class TestCase extends BaseTestCase
{
    protected $tenancy = false;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();

            $this->withoutMiddleware([
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ]);
        }
    }

    protected function tearDown(): void
    {
        if (isset($this->tenant)) {
            tenancy()->initialize($this->tenant);
    
            $this->tenant->delete();
    
            tenancy()->end();
        }
    
        parent::tearDown();
    }
    

    public function initializeTenancy()
    {
        $this->tenant = Tenant::create();

        tenancy()->initialize($this->tenant);
    }
}
