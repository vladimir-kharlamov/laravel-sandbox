<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rmq:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description: Publish';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        dd (
//            config('services.rabbitmq.host'),
//            config('services.rabbitmq.port'),
//            config('services.rabbitmq.username'),
//            config('services.rabbitmq.password'),
//            config('services.rabbitmq.vhost'));

//        https://stackoverflow.com/questions/42137216/i-cant-connect-to-rabbitmq-server-port-5672
//        stream_socket_client(): Unable to connect to tcp://localhost:5672 (Cannot assign requested address)
//        $connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
//        fixed connection Exception with docker container name
//        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $connection = new AMQPStreamConnection(
            config('services.rabbitmq.host'),
            config('services.rabbitmq.port'),
            config('services.rabbitmq.username'),
            config('services.rabbitmq.password')
        );

        $channel = $connection->channel();

        /** 1. No Exchange declared - using default. Send by queue name */
        /*$channel->queue_declare('queue#1', false, true, false, false);
        $msg = new AMQPMessage('Publisher says: Hello World.');

        //$routingKey = 'routingKey#1';
        $routingKey = 'queue#1'; // some strange naming - routingKey is queue name in fact
        $channel->basic_publish($msg, '', $routingKey);// send to default exchange: ''*/

        /** 2 Use named Exchange */
        $queueName = 'laravel_queue#1';
        $exchange = 'laravel_exchange#1';
        $channel->exchange_declare($exchange, 'fanout', false, true,false);
//        sleep(5);
        $channel->queue_declare($queueName, false, true, false, false);
//        sleep(16); try to wait the bind.
       // $channel->exchange_bind($queueName, $exchange); // NOT binding! Solved by hand only with rmq web manager
//        sleep(5);
        $msg = new AMQPMessage(json_encode(['param1'=>'data','param2'=>'data']));

        $channel->basic_publish($msg, $exchange);// no router_key param

        echo " [x] Sent to(fanout) : 'Publisher says: catch json.'\n";

        $channel->close();
        $connection->close();

//        return Command::SUCCESS;
    }
}
