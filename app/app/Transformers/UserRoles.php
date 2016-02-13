<?php
/**
 *
 * User: Damir djikic
 * damir@cod3.me , ddjikic@gmail.com
 * website www.cod3.me
 * Date: 8.7.15.
 * Time: 13.48
 */

namespace MobileOptin\Transformers;

use MobileOptin\Models\Role as RolesModel;
use League\Fractal;

class UserRoles extends Fractal\TransformerAbstract
{

    public function transform( RolesModel $role )
    {
        return [

            'role_id'    => $role->id,
            'role_title' => $role->role_title,
            'role_slug'  => $role->role_slug
        ];
    }


}