<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    protected $table = 'package';
    public $timestamps = false;
    protected $primaryKey = 'id';
     protected $guarded = array();
    
        public function allowed_groups() {
        return $this->hasMany('MobileOptin\Models\PackageToTemplatesGroup', 'package_id', 'id');
    }

}




