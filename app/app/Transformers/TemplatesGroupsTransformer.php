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
use MobileOptin\Models\TemplatesGroups;

class TemplatesGroupsTransformer extends Fractal\TransformerAbstract
{


    public function transform( TemplatesGroups  $role )
    {
        return [
            'id'    => (int) $role->id,
            'name' => $role->name,
        ];
    }

}