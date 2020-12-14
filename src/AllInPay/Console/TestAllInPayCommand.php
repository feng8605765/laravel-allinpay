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

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test allinpay case';

    public function handle()
    {
        $rest = app('allinpay')->balance->queryReserveFundBalance(['bizUserId' => '2']);

        var_dump($rest);
    }
}
