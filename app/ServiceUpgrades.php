<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceUpgrades extends Model
{
    //		
    protected $fillable=['ServiceId','UpgradeTitle','UpgradeDesc','UpgradePrice'];	
}
