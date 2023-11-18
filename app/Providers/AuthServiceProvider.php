<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::tokensCan([
            'warehouse' => 'warehouse',
            'supplier' => 'supplier',
            'driver' => 'driver',
        ]);


        \Illuminate\Support\Facades\Auth::provider('schoolmasters_provider', function($app, array $config) {
            return new SchoolmasterAuthProvider($app['hash'], $config['model']);
        });
        \Illuminate\Support\Facades\Auth::provider('warehouse_provider', function($app, array $config) {
            return new WarehouseAuthProvider($app['hash'], $config['model']);
        });
        \Illuminate\Support\Facades\Auth::provider('supplier_provider', function($app, array $config) {
            return new SupplierAuthProvider($app['hash'], $config['model']);
        });
        \Illuminate\Support\Facades\Auth::provider('dirver_provider', function($app, array $config) {
            return new DriverAuthProvider($app['hash'], $config['model']);
        });
    }
}
