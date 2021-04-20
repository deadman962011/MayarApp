<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\carbon;
use App\MayarOrder;
use App\MayarReport;

class DailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Orders Report Generate';

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
      
        //Get Orders Where Created Today
        $DayOrders=MayarOrder::WhereDay("created_at",carbon::now()->day)->get();
        
        //Get Count of Total Orders Where Created Today
        $TotalCount=$DayOrders->count();
       

        //Get Count of  Paid Orders 
        $PaidOrders=MayarOrder::WhereDay("created_at",carbon::now()->day)->where([['OrderStatus','1'],['OrderPayStatus','1']])->get();
        $PaidOrdersCount=$PaidOrders->count();

        //Get Count Of Canceld orders 
        $CanceldOrders=MayarOrder::WhereDay("created_at",carbon::now()->day)->where('OrderStatus','2')->get();
        $CanceldOrdersCount=$CanceldOrders->count();


        //Get Total Income
        $PriceArr=array();

        foreach ($PaidOrders as $Order ) {

            $OrderPrice=$Order['OrderPrice'];

            array_push($PriceArr,$OrderPrice);
            
        }

        $TotalIncome=array_sum($PriceArr);
 

        //Generate Excel File 

        //Upload File To Drive



        //Save Report Data On DB

        $SaveReport=new MayarReport ([
            'ReportTotalOrders'=>$TotalCount,
            'ReportPaidOrders'=>$PaidOrdersCount,
            'ReportCanceldOrders'=>$CanceldOrdersCount,
            'ReportIncome'=>$TotalIncome,
            'ReportDate'=>'Date',
            'ReportFile'=>'FileBaseName',
            'ReportType'=>'Daily'
        ]);

        $SaveReport->save();

        error_log("Daily Report Generated");
    }
}
