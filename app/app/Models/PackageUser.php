<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageUser extends Model
{


    protected $table = 'package_user';
    public $timestamps = true;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' ,'user_id' );
    }
    public function package()
    {
        return $this->belongsTo( 'MobileOptin\Models\Packages' ,'package_id' );
    }

}




