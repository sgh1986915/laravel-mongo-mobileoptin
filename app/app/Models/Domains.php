<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domains extends Model
{

    use SoftDeletes;

    protected $table = 'domains';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $dates = [ 'deleted_at' ];

    public function domains()
    {
        return $this->hasMany( 'MobileOptin\Models\UserDomains','domain_id','id');
    }

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' );
    }
        public function campaign()
    {
        return $this->hasMany( 'MobileOptin\Models\Campaigns','domain_id','id' );
    }

    public static function textStatus($id){
        switch ($id) {
            case 1:
                return 'Unconfirmed';
            break;
            case 2:
                return 'Confirmed';
            break;
            case 3:
                return 'Declined';
            break;

            default:
                return 'Unconfirmed';
            break;
        }
    }

}




