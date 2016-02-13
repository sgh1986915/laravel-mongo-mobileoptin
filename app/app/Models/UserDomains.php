<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class UserDomains extends Model
{


    protected $table = 'user_domains';
    public $timestamps = true;
    protected $primaryKey = 'id';


    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' );
    }

    public function domain()
    {
        return $this->belongsTo( 'MobileOptin\Models\Domains' );
    }


    public static function cleanOld()
    {
        static::where( 'campaign_id', '=', 0 )->where( 'created_at', '<', strtotime( '-3days' ) )->limit( 20 )->delete();

    }
}







