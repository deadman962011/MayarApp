<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceComment extends Model
{
    // 	 	 	 
    protected $fillable=['ServiceId','CustomerId','CommentVoteNum','CommentValue'];

    //Relations

    //Customer
    public function Customer()
    {
        return $this->hasOne('App\MayarCustomer', 'id', 'CustomerId');
    }
}
