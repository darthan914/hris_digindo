<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $data = User::keypermission();

        foreach ($data as $list) {
            foreach ($list['data'] as $list2) {
                Gate::define( $list2['value'] , function($user) use ($list2) {
                    return $user->hasAccess( $list2['value'] );
                });
            }
        }

                
    }

    // public function accessUser()
    // {
    //     Gate::define('all-user', function($user) {
    //         return $user->hasAccess('all-user');
    //     });
    //     Gate::define('list-user', function($user) {
    //         return $user->hasAccess('list-user');
    //     });
    //     Gate::define('create-user', function($user) {
    //         return $user->hasAccess('create-user');
    //     });
    //     Gate::define('edit-user', function($user) {
    //         return $user->hasAccess('edit-user');
    //     });
    //     Gate::define('delete-user', function($user) {
    //         return $user->hasAccess('delete-user');
    //     });
    //     Gate::define('position-user', function($user) {
    //         return $user->hasAccess('position-user');
    //     });
    //     Gate::define('active-user', function($user) {
    //         return $user->hasAccess('active-user');
    //     });
    //     Gate::define('access-user', function($user) {
    //         return $user->hasAccess('access-user');
    //     });
    //     Gate::define('leader-user', function($user) {
    //         return $user->hasAccess('leader-user');
    //     });
    //     Gate::define('impersonate-user', function($user) {
    //         return $user->hasAccess('impersonate-user');
    //     });
    // }

    // public function accessEmployee()
    // {

    //     Gate::define('list-employee', function($user) {
    //         return $user->hasAccess('list-user');
    //     });
    //     Gate::define('create-employee', function($user) {
    //         return $user->hasAccess('create-user');
    //     });
    //     Gate::define('edit-employee', function($user) {
    //         return $user->hasAccess('edit-user');
    //     });
    //     Gate::define('delete-employee', function($user) {
    //         return $user->hasAccess('delete-user');
    //     });
    // }
}
