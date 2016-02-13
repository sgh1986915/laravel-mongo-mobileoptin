<?php
namespace MobileOptin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packages extends Model
{


    protected $table = 'packages';
    public $timestamps = false;
    protected $primaryKey = 'id';



}




