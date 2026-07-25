<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'section';
    protected $primaryKey = 'SectionID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];
}
