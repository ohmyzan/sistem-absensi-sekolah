<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_date',
        'check_in',
        'status',
    ];

    // Relasi balik ke Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
