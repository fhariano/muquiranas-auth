<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Se tem permissão de gerenciar usuários
        Gate::define('users', function ($user) {
            return $user->hasPermisson('users');
        });

        // Se tem permissão de adicionar permissões a usuários
        Gate::define('add_permissions_user', function ($user) {
            return $user->hasPermisson('add_permissions_user');
        });

        // Se é Super Admin verifica antes e libera geral
        Gate::before(function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
