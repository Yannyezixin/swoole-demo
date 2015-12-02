<?php namespace ysd\tcp\timer;

use ysd\lib\Helper as H;
use swoole_server;

/**
 * TCP Server
 */
class Server
{
    // Server
    private $serv;

    // 端口
    private $port = 9352;

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', $this->port);
        $this->serv->set([
            'worker_num' => 1,
            'max_conn' => 2000,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode' => 1
        ]);

        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);
        $this->serv->on('Timer', [$this, 'onTimer']);
        $this->serv->start();
    }

    // 启动
    public function onStart($serv)
    {
        H::log("Start tcp server , listen on port: {$this->port}");
    }

    // 连接
    public function onConnect($serv, $fd, $from_id)
    {
        $serv->send($fd, "Hello {$fd}!");
    }

    // 接收
    public function onReceive($serv, $fd, $from_id, $data)
    {
        H::log("Get message from client {$fd}:{$data}");
    }

    // 断开连接
    public function onClose($serv, $fd, $from_id)
    {
        H::log("Client {$fd} close connection");
    }

    // 进程启动
    public function onWorkerStart($serv, $worker_id)
    {
        H::log("Worker {$worker_id} start");
        if ($worker_id == 0) {
            H::log("Worker 0 add timer");
            $serv->addTimer(500);
            $serv->addTimer(750);
            $serv->addTimer(1000);
            $serv->addTimer(1750);
        }
    }

    // Timer
    public function onTimer($serv, $interval)
    {
        switch ($interval) {
        case 500:
            H::log("Do A at interval 500");
            break;
        case 750:
            H::log("Do B at interval 750");
            break;
        case 1000:
            H::log("Do C at interval 1000");
            break;
        case 1750:
            H::log("Do D at interval 1750");
            break;
        }
    }

}
