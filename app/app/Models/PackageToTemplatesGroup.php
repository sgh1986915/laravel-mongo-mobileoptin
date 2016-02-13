<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class PackageToTemplatesGroup extends Model
{
    protected $table = 'package_to_templates_group';
    public $timestamps = true;
    protected $primaryKey = 'id';

    public function package()
    {
        return $this->belongsTo( 'MobileOptin\Models\Package' );
    }


    public function tplgroups()
    {
        return $this->hasMany( 'MobileOptin\Models\TemplatesGroups', 'id', 'template_group_id' );
    }
}




