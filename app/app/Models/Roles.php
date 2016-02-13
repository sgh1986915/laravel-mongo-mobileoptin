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

class Roles extends Model
{


    public function users()
    {
        return $this->hasMany( 'MobileOptin\Models\User' );
    }

    public function permissions()
    {
        return $this->belongsToMany( 'MobileOptin\ModelsPermission' );
    }
}




