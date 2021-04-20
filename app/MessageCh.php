<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageCh extends Model
{
    //

    protected $fillable=['ProviderId','CustomerId'];

    //Messages Relation

    public function Messages()
    {
        return $this->hasMany('App\MayarMessage', 'MessageCh', 'id');
    }

    //Customer Relation
    public function Customer()
    {
        return $this->hasOne('App\MayarCustomer', 'id', 'CustomerId');
    }

    public function Provider()
    {
        return $this->hasOne('App\MayarProvider', 'id', 'ProviderId');
    }
    
}
