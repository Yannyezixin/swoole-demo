<?php namespace ysd\tcp\demo1;

use swoole_client;
use ysd\lib\Helper;

class Client
{
    private $client;

    public function __construct()
    {
        $this->client =  new swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect()
    {
        if (!$this->client->connect("127.0.0.1", 9351, 1)) {
            Helper::log("{$fp->errMsg}[$fp->errCode]", LOG_ERROR);
        }

        $message = $this->client->recv();
        Helper::log("Get message from server: {$message}");

        fwrite(STDOUT, "Please input message:");
        $msg = trim(fgets(STDIN));
        $this->client->send($msg);
    }
}

