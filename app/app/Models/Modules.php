<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{
    protected $table = 'modules';
    public $timestamps = false;
    protected $primaryKey = 'id';

}




