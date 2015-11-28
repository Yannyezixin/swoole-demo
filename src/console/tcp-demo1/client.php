<?php
require(__DIR__ . '/../../../vendor/autoload.php');

use ysd\tcp\demo1\Client;

$client =  new Client();
$client->connect();
