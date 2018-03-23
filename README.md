<p align="center">
  <h2>BI - Connect</h2>
</p>
Connect with 3rd party sources to get data.

---

  <a href="https://styleci.io/repos/126517642"><img src="https://styleci.io/repos/126517642/shield?branch=master" alt="StyleCI"></a>
  <a href="https://packagist.org/packages/edbizarro/bi-connect"><img src="https://poser.pugx.org/edbizarro/bi-connect/v/stable.svg" alt="Latest Stable Version"></a>
  <a href="https://codeclimate.com/github/edbizarro/bi-connect/maintainability"><img src="https://api.codeclimate.com/v1/badges/ddf30fc607aa58ea232f/maintainability" /></a>
  <a class="badge-align" href="https://www.codacy.com/app/edbizarro/bi-connect?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edbizarro/bi-connect&amp;utm_campaign=Badge_Grade"><img src="https://api.codacy.com/project/badge/Grade/c79c2086e1614547bfa979c0004a6357"/></a>
  <a href="https://packagist.org/packages/edbizarro/bi-connect"><img src="https://poser.pugx.org/edbizarro/bi-connect/license.svg" alt="License"></a>
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
