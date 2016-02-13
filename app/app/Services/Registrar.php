<?php namespace MobileOptin\Services;

use MobileOptin\Models\User;
use MobileOptin\Models\UserProfile;
use MobileOptin\Models\UsersToTemplatesGroup;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class Registrar implements RegistrarContract
{

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator( array $data )
    {
        return Validator::make( $data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ] );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    public function create( array $data )
    {
        $user = User::create( [
            'name'     => $data[ 'name' ],
            'email'    => $data[ 'email' ],
            'password' => bcrypt( $data[ 'password' ] ),
        ] );
        UserProfile::create( [
            'user_id'       => $user->id,
            'max_campaigns' => getenv( 'defautl_number_of_campaign' ),
            'split_testing' => 0,
            'redirect_page' => 0,
            'embed'         => 0,
            'hosted'        => 0,
        ] );
        UsersToTemplatesGroup::create( [ 'user_id' => $user->id, 'template_group_id' => 1 ] );
        return $user;
    }

}
