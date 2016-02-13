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

use MobileOptin\Models\User as Usermodel;
use League\Fractal;

class User extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'profile',
        'role',
        'Allowed_template_groups',
        'Owner',
        'UserChild'
    ];

    public function transform( Usermodel $user )
    {
        return [
            'id'         => (int) $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'role_id'    => $user->role_id
        ];
    }

    public function includeProfile( Usermodel $user )
    {
        $profile = $user->profile;

        return $this->item( $profile, new UserProfile );
    }

    public function includeRole( Usermodel $user )
    {
        $role = $user->role;

        return $this->item( $role, new UserRoles );
    }

    public function includeAllowedTemplateGroups( Usermodel $user )
    {
        $allowed_groups = $user->allowed_groups()->with( 'tplgroups' )->get();
        return $this->item( $allowed_groups, new UsersTemplatesGroup );


    }

    public function includeOwner( Usermodel $user )
    {
        $owner = $user->owner;

        return $this->item( $owner, new UsersOwner );
    }

    public function includeUserChild( Usermodel $user )
    {
        $parent_user = $user->users;

        return $this->item( $parent_user, new UserChild );
    }

}