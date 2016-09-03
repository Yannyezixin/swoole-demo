<?php namespace ysd\tcp\chat;

use swoole_client;
use ysd\lib\Helper as H;

class Client
{
    /**
     * client
     */
    private $cli;

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->cli = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->cli->on('connect', [$this, 'onConnect']);
        $this->cli->on('receive', [$this, 'onReceive']);
        $this->cli->on('close', [$this, 'onClose']);
        $this->cli->on('error', [$this, 'onError']);
    }

    /**
     * 连接Server
     */
    public function connect()
    {
        $fp = $this->cli->connect('127.0.0.1', 9353);
        if (!$fp) {
            H::log("Error: {$fp->errMsg}[$fp->errCode]");
            return;
        }
    }

    /**
     * 连接成功
     */
    public function onConnect($cli)
    {
        fwrite(STDOUT, "");
        swoole_event_add(STDIN, function ($fp) use ($cli) {
            fwrite(STDOUT, "");
            $msg = trim(fgets(STDIN));
            if (!empty($msg)) {
                $cli->send($msg);
            }
        });
    }

    /**
     * 获取消息
     */
    public function onReceive($cli, $data)
    {
        H::log("{$data}");
    }

    /**
     * Close
     */
    public function onClose($cli)
    {
        H::log("Close connect with server");
    }

    /**
     * Err
     */
    public function onError($cli)
    {
    }

}
