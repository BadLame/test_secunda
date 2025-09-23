<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    function authorized(): static
    {
        $authCode = config('auth.auth_bearer_code');
        return $this->withHeader('Authorization', "Bearer $authCode");
    }
}
