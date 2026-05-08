<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'class',
        'photo',
        'face_descriptor',
    ];

    // Pastikan face_descriptor diubah otomatis menjadi array saat ditarik dari database
    protected $casts = [
        'face_descriptor' => 'array',
    ];

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Satu siswa bisa memiliki banyak data absensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
