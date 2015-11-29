<?php namespace ysd\tcp\demo2;

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
            'worker_num' => 8,
            'task_worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode' => 1
        ]);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);

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
        // 发送task 至 task Server
        $param = [
             'fd' => $fd,
        ];

        // 启动task
        $serv->task(json_encode($param));
        H::log('Continue handle Worker!');
    }

    // 断开连接
    public function onClose($serv, $fd, $from_id)
    {
        H::log("Client {$fd} close connection");
    }

    // 任务
    public function onTask($serv, $task_id, $from_id, $data)
    {
        H::log("This Task {$task_id} from worker {$from_id}");
        H::log("Data: {$data}");
        for ($i = 0; $i < 10; $i++) {
             sleep(1);
             H::log("Task {$task_id} handle {$i} times..");
        }

        $fd = json_decode($data, true)['fd'];
        $serv->send($fd, "Data is task {$task_id}");

        return "Task {$task_id}'s result";
    }

    // Task 结束回调
    public function onFinish($serv, $task_id, $data)
    {
        H::log("Task {$task_id} finish");
        H::log("Result: {$data}");
    }
}
