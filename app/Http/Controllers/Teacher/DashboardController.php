<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CalculationLog;


class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::where('is_active', true)->count();
        $totalLogs = CalculationLog::count();
        $latestLog = CalculationLog::orderBy('created_at', 'desc')->first();

        return view('teacher.dashboard', compact('totalStudents', 'totalLogs', 'latestLog'));
    }
}
