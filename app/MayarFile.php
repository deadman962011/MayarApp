<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MayarFile extends Model
{
    // 	 	 	 	 	
    protected $fillable=['BaseName','FileName','StorageName','Ext','FileSource','OrderId'];
}
