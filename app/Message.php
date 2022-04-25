<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table='messages';
    protected $fillable = [
        'senderId',
        'receiverId',
        'messageText',
        'isReceipt',
        'isRead'
    ];
    
    #dış model - dış model anahtarı - iç model anahtarı
    public function msgToSnd()
    {
        return $this->hasOne('App\User','id','senderId');
    }
    public function msgToRcv()
    {
        return $this->hasOne('App\User','id','receiverId');
    }
    // test
    
    
    
}
