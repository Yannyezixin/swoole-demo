<?php namespace ysd\tcp\chat;

use swoole_server;
use ysd\lib\Helper as H;

class Server
{
    /**
     * 服务
     */
    private $serv;

    /**
     * 端口
     */
    private $port = 9353;

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', $this->port);
        $this->serv->set([
            'worker_num' => 8,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'daemonize' => 0,
            'debug_mode' => 1,
        ]);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);

        $this->serv->start();
    }

    /**
     * 启动
     */
    public function onStart($serv)
    {
        H::log("Start tcp server, listen, listen on port {$this->port}");
    }

    /**
     * 连接
     */
    public function onConnect($serv, $fd, $from_id)
    {
        $serv->send($fd, "Hello Client {$fd}\n");
        $serv->send($fd, "Current server has: ". count($serv->connection_list()). " client");
    }

    /**
     * 接受
     */
    public function onReceive($serv, $fd, $from_id, $data)
    {
        H::log("Get message from client {$fd}: {$data}");

        $clis = $serv->connection_list();
        if (($idx = array_search($fd, $clis)) !== false) unset($clis[$idx]);
        foreach ($clis as $cli) {
            $serv->send($cli, $data);
        }
    }

    /**
     * 关闭
     */
    public function onClose($serv, $fd, $from_id)
    {
        H::log("Client {$fd} close connect");
    }

    /**
     * 发送信息
     */
    public function send($fd, $data)
    {
        $this->serv->send($fd, $data);
    }

}
