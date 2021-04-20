<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\mayarCustomer;

class DailyGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Daily:Generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Activation And Rest Passowrd Tokens For Customers Each Hour';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Get All Customers
        $getCustomers=MayarCustomer::all();

        foreach ($getCustomers as $Customer ) {

            //generate New token For RestPass And Activation 
            $AcivationToken= md5(rand(1, 10) . microtime());
            $PassRestToken=md5(rand(1, 12) . microtime());

            //Get Customer One
            $getCustomer=MayarCustomer::find($Customer['id']);

            //Update Customer
            $UpdateCustomer=$getCustomer->update([
                'CustActivationToken'=>$AcivationToken,
                'CustPassRestToken'=>$PassRestToken
            ]);
        }

    }
}
