<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
	
    protected $table         = 'faq_category';
    public    $timestamps    = true;
    protected $primaryKey    = 'id';
    
    public function qa()
    {
    	return $this->hasMany( 'MobileOptin\Models\FaqAnswers' ,'faq_category_id','id');
    }
}




