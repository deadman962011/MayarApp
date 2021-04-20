<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MayarService extends Model
{
    //
    protected $fillable=['ServiceName','ServiceThumb','ServicePrice','ServiceProviderId','ServiceCatId','ServiceDesc','ServiceOrderdNum','ServiceStatus'];

    //Relations

    //Category
    public function Category()
    {
        return $this->hasOne('App\MayarCategory', 'id', 'ServiceCatId');
    }

    //ServiceProvider
    public function ServiceProvider()
    {
        return $this->hasOne('App\MayarProvider', 'id', 'ServiceProviderId');
    }

    //ServiceUpgrades
    public function Upgrades()
    {
        return $this->hasMany('App\ServiceUpgrades', 'ServiceId', 'id');
    }

    //Comments
    public function Comments()
    {
        return $this->hasMany('App\ServiceComment', 'ServiceId', 'id');
    }
}
