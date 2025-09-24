<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    function testGrantedWithAuthCode(): void
    {
        $authCode = config('auth.auth_bearer_code');
        $this->getJson(route('api.auth-test'), ['Authorization' => "Bearer $authCode"])
            ->assertSuccessful();
    }

    function testForbiddenWithoutRightAuthCode(): void
    {
        $this->getJson(route('api.auth-test'))
            ->assertForbidden();

        $randStr = Str::random();
        $this->getJson(route('api.auth-test'), ['Authorization' => "Bearer $randStr"])
            ->assertForbidden();
    }
}
