<?php

namespace AllInPay\AllInPay\Console;

use Illuminate\Console\Command;

class TestAllInPayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:test-allinpay';

    public function handle()
    {
        $rest = app('single-allinpay')->balance->queryReserveFundBalance(['bizUserId' => '2']);

        dump($rest);
    }
}
