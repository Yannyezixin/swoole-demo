<?php
require(__DIR__ . '/../../../vendor/autoload.php');

use ysd\tcp\demo2\Client;

$client =  new Client();
$client->connect();
