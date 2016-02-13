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
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaigns extends Model
{

    use SoftDeletes;

    protected $table = 'campaigns';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $dates = [ 'deleted_at' ];

    public function template()
    {
        return $this->hasMany( 'MobileOptin\Models\UserTemplates','campaign_id','id');
    }
    
        public function domain()
    {
            return $this->belongsTo( 'MobileOptin\Models\Domains' ,'domain_id');
      
    }

    public function user()
    {
        return $this->belongsTo( 'MobileOptin\Models\User' );
    }

    public function assinged()
    {
        return $this->belongsToMany( 'MobileOptin\Models\Campaigns', 'user_allowed_campaigns' ,'user_id','campaign_id' );
    }
    
    public function assigned()
    {
    	return $this->belongsToMany( 'MobileOptin\Models\Campaigns', 'user_allowed_campaigns' ,'campaign_id','id' );
    }
}




