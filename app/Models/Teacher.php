<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $fillable = [
        'teacher_id',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'cnic',
        'phone',
        'email',
        'address',
        'qualification',
        'experience',
        'joining_date',
        'salary',
        'class_id',
        'section_id',
        'subject',
        'photo',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Full name accessor: $teacher->full_name
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Public URL of the teacher's photo, or null if none uploaded.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    /**
     * Human readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
