<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MayarPayment extends Model
{
    // 	 	 	
    protected $fillable=['OrderId','PaymentMethod','PaymentValue','PaymentToken'];
}
