<?php
namespace Sercul\Messages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messages extends Model
{

    use SoftDeletes;

    protected $table = 'messages';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $dates = [ 'deleted_at' ];



}




