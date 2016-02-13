<?php
/**
 *
 * User: Damir djikic
 * damir@cod3.me , ddjikic@gmail.com
 * website www.cod3.me
 * Date: 5/11/15
 * Time: 9:53 PM
 */
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class UsersToTemplatesGroup extends Model
{
    protected $table = 'users_to_templates_group';
    public $timestamps = true;
    protected $primaryKey = 'user_id';
    protected $fillable = [ 'user_id', 'template_group_id' ];

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' );
    }


    public function tplgroups()
    {
        return $this->hasMany( 'MobileOptin\Models\TemplatesGroups', 'id', 'template_group_id' );
    }
}




