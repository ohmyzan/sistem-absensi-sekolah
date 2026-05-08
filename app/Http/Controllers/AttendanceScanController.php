<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceScanController extends Controller
{
    // Menampilkan halaman utama mesin absensi
    public function index()
    {
        // Ambil HANYA siswa yang sudah merekam wajah
        $students = Student::with('user')->whereNotNull('face_descriptor')->get();

        return view('attendance-scan', compact('students'));
    }

    // Menerima request saat wajah dikenali
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // Cek apakah siswa sudah absen hari ini
        $alreadyCheckedIn = Attendance::where('student_id', $request->student_id)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyCheckedIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.'
            ]);
        }

        // Catat absensi baru
        Attendance::create([
            'student_id' => $request->student_id,
            'attendance_date' => $today,
            'check_in' => $currentTime,
            'status' => 'hadir',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!'
        ]);
    }
}
