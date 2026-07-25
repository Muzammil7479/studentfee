<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $table = 'feestructure';
    protected $primaryKey = 'FeeStructureID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'ClassID', 'ClassID');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'TermID', 'TermID');
    }
}
