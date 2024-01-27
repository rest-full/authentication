# Rest-full Authentication

## About Rest-full Authentication

Rest-full Authentication is a small part of the Rest-Full framework.

You can find the application at: [rest-full/app](https://github.com/rest-full/app) and you can also see the framework skeleton at: [rest-full/rest-full](https://github.com/rest-full/rest-full).

## Installation

* Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
* Run `php composer.phar require rest-full/authentication` or composer installed globally `compser require rest-full/authentication` or composer.json `"rest-full/autentication": "1.0.0"` and install or update.

## Usage

This Session
```
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__.'/../config/pathServer.php';

use Restfull\Authentication\Auth;

$auth = new Auth();
$auth->write('test',['test1'=>'aprovado','test2'=>'reprovado']);
print_r($auth->getsession('test'));
```
This Two Factor
```
<?php

require __DIR__ . '/vendor/autoload.php';

use Restfull\Authentication\Auth;
use Restfull\Authentication\TwoSteps;

$auth = new Auth();
$twofactor = new TwoSteps($auth);
echo $twofactor->qrcodeValid()->getQrcode();
```

## License

The rest-full framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

