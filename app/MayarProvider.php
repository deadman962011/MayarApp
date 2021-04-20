<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MayarProvider extends Authenticatable
{
    //
    protected $fillable=['ProviderName','ProviderUserName','ProviderPass','ProviderIconSrc','ProviderDesc','ProviderServiceNum'];

    protected $hidden=['ProviderPass'];


    public function getAuthPassword()
    {
        return $this->ProviderPass;
    }
}
	
