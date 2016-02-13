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

//use MobileOptin\Models\UsersToTemplatesGroup as UsersToTemplatesGroupModel;
use League\Fractal;

class UsersTemplatesGroup extends Fractal\TransformerAbstract
{

    public function transform( $groups )
    {

        $r = [ ];
        foreach ( $groups as $group ) {

            $idata=$group->tplgroups->first();
            $r[ ] = [
                'group_id'   => $group[ 'template_group_id' ],

                'group_name' => (isset($idata->name ))?($idata->name ):(''),
            ];
        }
        return $r;
    }

}