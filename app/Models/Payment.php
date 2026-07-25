<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'PaymentID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class, 'StudentFeeID', 'StudentFeeID');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'PaymentID', 'PaymentID');
    }
}
