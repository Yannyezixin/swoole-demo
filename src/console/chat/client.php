<?php

require(__DIR__ . '/../../../vendor/autoload.php');

use ysd\tcp\chat\Client;

$client = new Client();
$client->connect();
