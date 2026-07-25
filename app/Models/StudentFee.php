<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $table = 'studentfee';
    protected $primaryKey = 'StudentFeeID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'StudentID', 'StudentID');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'StudentFeeID', 'StudentFeeID');
    }
}
