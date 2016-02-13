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

class AdminToTemplatesGroup extends Model
{
    protected $table = 'admin_to_templates_group';
    public $timestamps = true;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' );
    }

    public function template()
    {
        return $this->belongsTo( 'MobileOptin\Models\Campaigns' );
    }

}




