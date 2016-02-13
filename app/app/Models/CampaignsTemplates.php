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

class CampaignsTemplates extends Model
{
    protected $table = 'campaigns_templates';
    public $timestamps = true;
    protected $primaryKey = 'id';


    public function user_templates()
    {
        return $this->hasMany( 'MobileOptin\Models\UserTemplates', 'original_template_id', 'id' );
    }
    public static function findOrCreate($id)
    {
        $obj = static::find($id);
        return $obj ?: new static;
    }
}




