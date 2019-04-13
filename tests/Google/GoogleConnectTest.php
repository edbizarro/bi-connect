<?php

namespace Edbizarro\BiConnect\Tests\Google;

use Bi\Connect\Base\Oauth2Connect;
use Bi\Connect\Google\Auth\CredentialsFileAuth;
use Bi\Connect\Google\GoogleConnect;
use PHPUnit\Framework\TestCase;

class GoogleConnectTest extends TestCase
{
    /** @test */
    public function it_can_instantiate(): void
    {
        $googleConnect = new GoogleConnect(
            new CredentialsFileAuth(
                dirname(__DIR__) . '/'.getenv('GOOGLE_CREDENTIALS')
            )
        );

        $this->assertInstanceOf(Oauth2Connect::class, $googleConnect);
    }
}
