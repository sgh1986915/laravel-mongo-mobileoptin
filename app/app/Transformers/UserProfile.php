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

use MobileOptin\Models\UserProfile as UserProf;
use League\Fractal;

class UserProfile extends Fractal\TransformerAbstract
{

    public function transform( UserProf $profile )
    {
        return [

            'maximum_number_of_campaigns' => $profile->max_campaigns,
            'allowed_split_testing'       => $profile->split_testing,
            'allowed_redirects'           => $profile->redirect_page,
            'allowed_embed'               => $profile->embed,
            'allowed_hosted'              => $profile->hosted,
            'analytics_retargeting'              => $profile->analytics_retargeting,
        ];
    }


}