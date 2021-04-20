<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MayarMessage extends Model
{
    // 	 	
    protected $fillable=['MessageTarget','MessageTargetType','MessageSource','MessageSourceType','MessageValue','MessageStatus','MessageOrderId','MessageCh'];
}
