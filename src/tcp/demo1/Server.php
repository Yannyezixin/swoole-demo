<?php namespace ysd\tcp\demo1;

use ysd\lib\Helper;

/**
 * TCP Server
 */
class Server
{
    // Server
    private $serv;

    // 端口
    private $port = 9351;

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', $this->port);
        $this->serv->set([
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode' => 1
        ]);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connenct', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);

        $this->serv->start();
    }

    // 启动
    public function onStart($serv)
    {
        Helper::log("Start tcp server , listen on port: {$port}");
    }

    // 连接
    public function onConnect($serv, $fd, $from_id)
    {
        $serv->send($fd, "Hello {$fd}!");
    }

    // 接收
    public function onReceive($serv, $fd, $from_id, $data)
    {
        Helper::log("Get message from client {$fd}:{$data}");
    }

    // 断开连接
    public function onClose($serv, $fd, $from_id)
    {
        Helper::log("Client {$fd} close connection");
    }
}
