<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationsUser extends Model
{
    protected $table = 'integrations_user';
    public $timestamps = true;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' ,'user_id' );
    }

    public function type()
    {
        return $this->belongsTo( 'MobileOptin\Models\IntegrationsType' ,'type_id' );
    }

    public function zapier_webhooks()
    {
        return $this->hasMany('MobileOptin\Models\ZapierWebhook', 'integrations_user_id');
    }

}
