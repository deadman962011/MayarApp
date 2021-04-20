<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MayarReport extends Model
{
    // 							
   protected $fillable=['ReportTotalOrders','ReportPaidOrders','ReportCanceldOrders','ReportIncome','ReportDate','ReportFile','ReportType'];
}
