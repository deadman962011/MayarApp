<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MayarRate extends Model
{
    //
    //   

    protected $fillable=['MayarRateCustId','MayarRateValue','MayarRateBody'];

    public function Customer()
    {
        return $this->hasOne('App\MayarCustomer', 'id', 'MayarRateCustId');
    }


}
