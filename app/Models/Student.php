<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'StudentID';
    public $timestamps = false;
    protected $guarded = [];

    public function parentGuardian()
    {
        return $this->belongsTo(ParentGuardian::class, 'ParentID', 'ParentID');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'ClassID', 'ClassID');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'SectionID', 'SectionID');
    }

    public function fees()
    {
        return $this->hasMany(StudentFee::class, 'StudentID', 'StudentID');
    }
}
