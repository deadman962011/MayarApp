<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MayarOrder extends Model
{
    
    // 	 	 	 
    protected $fillable=['OrderServiceId','OrderUpgradesId','OrderCustomerId','OrderStatus','OrderPrice','OrderTargetId','OrderFolder','OrderQr','OrderQrToken','OrderDesc','OrderPayStatus'];

    //Relations

    //Services
    public function Service()
    {
        return $this->hasOne('App\MayarService', 'id', 'OrderServiceId');
    }

    //Customer
    public function Customer()
    {
        return $this->hasOne('App\MayarCustomer', 'id', 'OrderCustomerId');
    }

    //Files
    public function Files()
    {
        return $this->hasMany('App\MayarFile', 'OrderId', 'id');
    }
}
