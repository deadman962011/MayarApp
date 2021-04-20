<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MayarCustomer extends Authenticatable implements JWTSubject
{
    use Notifiable;
    // 	 	 
    
        /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    protected $fillable=['CustFirstName','CustLastName','CustUserName','CustPass','CustMail','CustStatus','CustCountry','CustActivationToken','CustAddress','CustPassRestToken','CustPassRestExpire'];

    protected $hidden=['CustPass'];

    public function getAuthPassword()
    {
        return $this->CustPass;
    }
}
