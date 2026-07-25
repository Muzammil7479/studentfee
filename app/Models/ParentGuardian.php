<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentGuardian extends Model
{
    protected $table = 'parent';
    protected $primaryKey = 'ParentID';
    public $timestamps = false;
    protected $guarded = [];
}
