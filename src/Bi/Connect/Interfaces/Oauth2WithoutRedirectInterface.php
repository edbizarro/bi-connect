<?php

namespace Bi\Connect\Interfaces;

/**
 * Interface Oauth2WithoutRedirectInterface.
 */
interface Oauth2WithoutRedirectInterface
{
    public function auth();
    public function getAccessToken();
}
