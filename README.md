<p align="center">
  <h2>BI - Connect</h2>
</p>
Connect with 3rd party sources to get data.

---

<p align="center">
    <a href="https://styleci.io/repos/123524313"><img src="https://styleci.io/repos/123524313/shield?branch=master" alt="StyleCI"></a>
</p>

Supported sources:

* Google Analytics
* Adobe Analytics
* Facebook Ads (WIP)
* Instagram Ads (WIP)
* Twitter (WIP)
* TailTarget (WIP)
---

Here are a few examples on how you can use the package:

```php
use Bi\Connect\Google\GoogleConnect;
use Bi\Connect\Google\Auth\CredentialsFileAuth;

$googleConnect = new GoogleConnect(
    new CredentialsFileAuth('path/to/ga/credentials.json) // https://developers.google.com/analytics/devguides/reporting/core/v4/authorization#common_oauth_20_flows
);

$googleConnect->addScope('analytics');
$googleConnect->setRedirectUrl('registered callback url'); // Se link above
$googleConnect->getLoginUrl(); // Get google login auth url

// OAuth2 flow
```

After the OAuth2 flow you can access GA API

```php
// Retrieve all analytics accounts
$accounts = $googleConnect->analytics()->getAccounts();
```

## Installation

You can install the package via composer:

``` bash
composer require edbizarro/bi-connect
```

---

[![forthebadge](http://forthebadge.com/images/badges/contains-cat-gifs.svg)](http://forthebadge.com)
