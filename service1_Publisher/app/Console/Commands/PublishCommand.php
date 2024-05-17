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
    protected $signature = 'rabbitmq:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection('rabbitmq_test-rabbitmq-1', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('my_exchange_laravel', 'fanout', false, true, false);
        $channel->queue_declare('my_queue_laravel', false, true, false, false);

        $channel->queue_bind('my_queue_laravel', 'my_exchange_laravel');


        $data = [
            'title' => 'Some title',
            'content' => 'some content'
        ];

        $data = json_encode($data);

        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, '', 'my_exchange_laravel');

        echo " [x] Sent 'Hello World!'\n";

        $channel->close();
        $connection->close();
    }
}
