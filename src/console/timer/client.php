<?php

require(__DIR__ . '/../../../vendor/autoload.php');

use ysd\tcp\timer\Client;

$client = new Client();
$client->connect();
