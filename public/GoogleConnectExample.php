<?php

include dirname(__DIR__).'../vendor/autoload.php';

use Bi\Connect\Google\GoogleConnect;
use Bi\Connect\Google\Auth\CredentialsFileAuth;
use Bi\Connect\Exceptions\GoogleCredentialsException;

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

try {
    $googleConnect = new GoogleConnect(
        new CredentialsFileAuth(
            dirname(__DIR__).'/PATH/TO/CONFIG/JSON'
        )
    );
} catch (GoogleCredentialsException $e) {
    dd($e->getMessage());
} catch (Google_Exception $e) {
    dd($e->getMessage());
}

$googleConnect->addScope('analytics');
$googleConnect->addScope('webmaster');
$googleConnect->setRedirectUrl('REDIRECT_URL');
$googleConnect->forOffline();
$googleConnect->forceApprove();

echo '<h1> Login url </h1>';
echo '<a href="'.$googleConnect->getLoginUrl().'"> Efetuar login com sua conta do google</a>';

try {
    if (isset($_GET['code'])) {
        echo '<h1> Access token </h1>';
        var_dump($googleConnect->getAccess($_GET['code']));

        echo '<h1> GA Accounts </h1>';
        $accounts = $googleConnect->analytics()->getAccounts()->all();

        foreach ($accounts as $account) {
            echo 'ID '.$account['id'].' Name '.$account['name'].'<br />';
        }

        echo '<h1> Web Masters Tools - Sites</h1>';
        $sites = $googleConnect->webmaster()->sites->listSites()->getSiteEntry();

        foreach ($sites as $site) {
            echo 'Site URL: '.$site->siteUrl.'<br />';
        }
    }
} catch (Exception $e) {
    dd($e->getMessage());
}
