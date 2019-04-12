<?php

namespace Bi\Connect\Google\Auth;

use Bi\Connect\Exceptions\GoogleCredentialsException;

/**
 * Class CredentialsFileAuth.
 */
class CredentialsFileAuth implements GoogleAuth
{
    /**
     * @var string
     */
    protected $credentials;

    /**
     * CredentialsFileAuth constructor.
     *
     * @param string $credentialsFile Path to google's credentials json file
     *
     * @throws GoogleCredentialsException
     */
    public function __construct($credentialsFile)
    {
        $fileExists = file_exists($credentialsFile) ? $credentialsFile : false;

        if ($fileExists === false) {
            throw new GoogleCredentialsException('Wrong credentials file path');
        }

        $this->credentials = $credentialsFile;
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
}
