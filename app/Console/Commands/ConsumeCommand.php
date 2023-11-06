<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use function MongoDB\BSON\toJSON;

class ConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rmq:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description: Consumer acting';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection(
            config('services.rabbitmq.host'),
            config('services.rabbitmq.port'),
            config('services.rabbitmq.username'),
            config('services.rabbitmq.password')
        );
        $channel = $connection->channel();
        /** 1. No Exchange declared - using default. Send by queue name */
//        $channel->queue_declare('queue#1', false, true, false, false);

        /** 2 Use named Exchange */
        // There is no need to $channel->queue_declare()

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo ' [x] Received: '. "\n";
            print_r(json_decode($msg->body, true));
        };

        // Consumer getting message
        $channel->basic_consume(
//            'queue#1', with no Exchange declaration
            'laravel_queue#1',
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }

//        return Command::SUCCESS;
    }
}
