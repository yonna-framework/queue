<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;


class mq2
{

    function __construct($utime = 1000)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function ($msg) use ($utime) {
            // usleep($utime);
            echo " [x] Received ", $msg->body, "\n";
        };

        //在接收消息的时候调用$callback函数
        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

}

$params = getopt('u:');
$u = $params['u'] ?? 1000;
(new mq2($u));