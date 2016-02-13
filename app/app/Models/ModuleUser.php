<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleUser extends Model
{
    protected $table = 'module_user';
    public $timestamps = true;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' ,'user_id' );
    }
    public function module()
    {
        return $this->belongsTo( 'MobileOptin\Models\Modules' ,'module_id' );
    }

}




