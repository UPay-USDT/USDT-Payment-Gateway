<?php
include_once '../vendor/autoload.php';

$tron = new \IEXBase\TronAPI\Tron();

/**
 * WARNING: When sending funds, you should not specify these parameters
 *
 * P.S: In the process of payment are automatically converted
*/

$from = $tron->toTron(1.15); //1150000
$to = $tron->fromTron(11500000); //11.5000000
