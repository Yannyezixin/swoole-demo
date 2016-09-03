<?php namespace ysd\ws\demo1;

use ysd\lib\Helper as H;
use swoole_websocket_server;
use Redis;

/**
 * TCP Server
 */
class Server
{
    // Server
    private $serv;

    // 端口
    private $port = 9351;

    // Redis
    private $redis;

    // 事件
    private $event = [
        "recommend" => 10001,
    ];

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->redis->select(10);
        $this->serv = new swoole_websocket_server('127.0.0.1', $this->port);

        $this->serv->on('start', [$this, 'onStart']);
        $this->serv->on('open', [$this, 'onOpen']);
        $this->serv->on('message', [$this, 'onMessage']);
        $this->serv->on('close', [$this, 'onClose']);

        $this->serv->start();
    }

    // 启动
    public function onStart($serv)
    {
        H::log("Start tcp server , listen on port: {$this->port}");
    }


    // 连接
    public function onOpen($server, $request)
    {
        H::log("Server: 与客户端 fd{$request->fd} 握手成功");
    }

    // 接受消息
    public function onMessage($server, $frame)
    {
        H::log("接受消息: {$frame->fd}:{$frame->data}, opcode: {$frame->opcode}, fin: {$frame->finish}");

        // 更新队列 ws.group[groupId] [fd]
        if (strpos($frame->data, 'group') !== false) {
            $this->redis->lPush('ws.'.$frame->data, $frame->fd);
            $this->redis->hSet('ws.fd', $frame->fd, substr($frame->data, 5));
        } else if (strpos($frame->data, 'user') !== false) {
            $this->redis->lPush('ws.'.$frame->data, $frame->fd);
        } else if (strpos($frame->data, 'event') !== false) {
            $code = substr($frame->data, 6, 5);
            $data = substr($frame->data, 12);
            $this->dealEvent($code, $data);
        }

        //$clis = $server->connection_list();
        //if (($idx = array_search($frame->fd, $clis)) !== false) unset($clis[$idx]);
        //foreach ($clis as $cli) {
            //$server->push($cli, "新的客户端连接");
        //}
    }

    // 断开连接
    public function onClose($server, $fd)
    {
        $groupId = $this->redis->hget('ws.fd', $fd);
        $result = $this->redis->lRem('ws.group'.$groupId, $fd, 0);
        H::log("客户端 fd{$fd} 断开连接");
    }

    // 处理事件
    public function dealEvent($code, $data)
    {
        // 推荐通知事件
        if ($code == $this->event['recommend']) {
            $fd = [];
            $groups = explode(',', $data);
            foreach ($groups as $group) {
                $fd = array_merge($fd, $this->redis->lRange('ws.group'.$group, 0, -1));
            }
            // 发送通知
            array_unique($fd);
            foreach ($fd as $cli) {
                $this->serv->push($cli, '10001:您有新的推荐文章');
            }
        }
    }

}
