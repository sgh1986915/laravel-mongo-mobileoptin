<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessagesRead extends Model
{

    use SoftDeletes;

    protected $table = 'messages_read';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $dates = [ 'deleted_at' ];

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' ,'user_id','id' );
    }

    public function message()
    {
        return $this->belongsTo( 'MobileOptin\Models\Messages' ,'message_id','id');
    }


}




