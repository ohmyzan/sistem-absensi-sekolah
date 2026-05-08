<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class FaceRegistrationController extends Controller
{
    // Menampilkan halaman rekam wajah
    public function create(Student $student)
    {
        return view('face-registration', compact('student'));
    }

    // Menerima data JSON dari face-api.js dan menyimpannya ke database
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'face_descriptor' => 'required|array',
        ]);

        $student->update([
            'face_descriptor' => $request->face_descriptor,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wajah berhasil direkam!'
        ]);
    }
}
