<?php namespace ysd\tcp\demo2;

use swoole_client;
use ysd\lib\Helper as H;

class Client
{
    private $client;

    public function __construct()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->client->on('connect', [$this, 'onConnect']);
        $this->client->on('receive', [$this, 'onReceive']);
        $this->client->on('error', [$this, 'onError']);
        $this->client->on('close', [$this, 'onClose']);
    }

    public function connect()
    {
        $fp = $this->client->connect('127.0.0.1', 9352);
        if (!$fp) {
            H::log("Error: {$fp->errMsg}[{$fp->errCode}]");
            return;
        }
    }

    // 连接
    public function onConnect($cli)
    {
        fwrite(STDOUT, "Enter Msg:");
        swoole_event_add(STDIN, function ($fp) use ($cli) {
            fwrite(STDOUT, "Enter Msg:");
            $msg = trim(fgets(STDIN));
            $cli->send($msg);
        });
    }

    // 接收
    public function onReceive($cli, $data)
    {
        H::log("Get message from server: {$data}");
    }

    // 关闭
    public function onClose($cli)
    {
        H::log("Client close connection");
    }

    // 错误
    public function onError($cli)
    {
    }

    // 发送消息
    public function send($data)
    {
         $this->client->send($data);
    }

    //是否连接
    public function isConnected()
    {
        return $this->client->isConnected();
    }

}

