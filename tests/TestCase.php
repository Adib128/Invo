<?php

namespace Tests;

use App\Models\User;
use Artisan;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        Artisan::call('passport:install');        
    }

    public function authenticate()
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        auth()->attempt([
            'email' => $user->email,
            'password' => 'password',
        ]);
        return $accessToken = auth()
            ->user()
            ->createToken('authToken')->accessToken;
    }
}
