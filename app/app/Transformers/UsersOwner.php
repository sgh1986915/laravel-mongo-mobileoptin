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

 use League\Fractal;

class UsersOwner extends Fractal\TransformerAbstract
{

    public function transform(  $users )
    {

        $r = [ ];
        foreach ( $users as $user ) {

            $r[ ] = [
                'id' => $user->id,
            ];
        }
        return $r;

    }


}