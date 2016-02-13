<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertCategory extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
	
    protected $table         = 'expert_traffic_category';
    public    $timestamps    = true;
    protected $primaryKey    = 'id';
    
    public function qa()
    {
    	return $this->hasMany( 'MobileOptin\Models\ExpertAnswers' ,'faq_category_id','id');
    }
}




