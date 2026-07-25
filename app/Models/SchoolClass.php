<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'class';
    protected $primaryKey = 'ClassID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    public function sections()
    {
        return $this->hasMany(Section::class, 'ClassID', 'ClassID');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'ClassID', 'ClassID');
    }
}
