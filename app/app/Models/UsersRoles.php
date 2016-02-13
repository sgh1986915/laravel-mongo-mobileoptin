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

class UsersRoles extends Model
{
    protected $table = 'users_permissions';
    public $timestamps = true;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo( 'user' );
    }
}




