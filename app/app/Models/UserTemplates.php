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

class UserTemplates extends Model
{


    protected $table = 'user_templates';
    public $timestamps = true;
    protected $primaryKey = 'id';


    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' );
    }

    public function campaign()
    {
        return $this->belongsTo( 'MobileOptin\Models\Campaigns' );
    }

    public function org_template()
    {
        return $this->belongsTo( 'MobileOptin\Models\CampaignsTemplates', 'original_template_id', 'id' );

    }

    public static function cleanOld()
    {
        static::where( 'campaign_id', '=', 0 )->where( 'created_at', '<', strtotime( '-3days' ) )->limit( 20 )->delete();

    }

    public function integrations_user()
    {
        return $this->hasOne( 'MobileOptin\Models\IntegrationsUser', 'id', 'contact_type');
    }
}