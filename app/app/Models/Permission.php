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

class Permission extends Model
{



    public function roles()
    {
        return $this->belongsToMany( 'MobileOptin\Models\Role' );
    }
}


