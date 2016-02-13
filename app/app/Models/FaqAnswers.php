<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;

class FaqAnswers extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question', 'answer', 'faq_category_id', 'pdf_file'];
	
    protected $table         = 'faq_answers';
    public    $timestamps    = true;
    protected $primaryKey    = 'id';
}




