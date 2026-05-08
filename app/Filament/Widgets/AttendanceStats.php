<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AttendanceStats extends BaseWidget
{
    // Mengatur widget ini agar berada di urutan paling atas
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();

        $totalStudents = Student::count();
        $presentToday = Attendance::whereDate('attendance_date', $today)->count();
        $absentToday = $totalStudents - $presentToday; // Logika sederhana yang belum hadir

        return [
            Stat::make('Total Siswa', $totalStudents)
                ->description('Seluruh siswa terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Hadir Hari Ini', $presentToday)
                ->description('Siswa yang sudah scan wajah')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Grafik dummy agar UI lebih manis

            Stat::make('Belum Absen', $absentToday)
                ->description('Siswa yang belum datang')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
