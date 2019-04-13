<?php

namespace Edbizarro\BiConnect\Tests\Google;

use Bi\Connect\Google\Auth\CredentialsFileAuth;
use Bi\Connect\Google\GoogleConnect;
use PHPUnit\Framework\TestCase;

class GoogleConnectTest extends TestCase
{
    /** @test */
    public function can_instantiate()
    {
        $googleConnect = new GoogleConnect(
            new CredentialsFileAuth(
                dirname(__DIR__) . '/PATH/TO/CONFIG/JSON'
            )
        );
    }
}
