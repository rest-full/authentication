<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config/pathServer.php';

use Restfull\Authentication\TwoSteps;
use Restfull\Authentication\Auth;

$auth = new Auth();
$auth->write('test',['test1'=>'aprovado','test2'=>'reprovado']);
print_r($auth->getsession('test'));
$twofactor = new TwoSteps($auth);
echo $twofactor->qrcodeValid()->getQrcode();