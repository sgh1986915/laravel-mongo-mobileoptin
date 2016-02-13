<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertAnswers extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question', 'answer', 'faq_category_id', 'pdf_file'];
	
    protected $table         = 'expert_traffic_answers';
    public    $timestamps    = true;
    protected $primaryKey    = 'id';
}




