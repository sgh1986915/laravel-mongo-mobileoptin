<?php namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class ZapierWebhook extends Model {

	public function integrations_user()
    {
        return $this->belongsTo('MobileOptin\Models\IntegrationsUser');
    }

}
