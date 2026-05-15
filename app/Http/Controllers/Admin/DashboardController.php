<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Criterion;
use App\Models\CalculationLog;


class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung statistik untuk ditampilkan di kartu
        $totalStudents = Student::count();
        $totalCriteria = Criterion::count();
        $totalLogs = CalculationLog::count();

        return view('admin.dashboard', compact('totalStudents', 'totalCriteria', 'totalLogs'));
    }
}
