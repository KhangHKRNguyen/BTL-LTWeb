<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Trang chọn lớp + ngày điểm danh
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $classes = $teacher->classes;

        $selectedClass = null;
        $students = collect();
        $attendanceDate = $request->get('attendance_date', now()->toDateString());
        $existingAttendances = collect();

        if ($request->filled('class_id')) {
            $selectedClass = CourseClass::where('id', $request->class_id)
                ->whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
                ->firstOrFail();

            $students = $selectedClass->students;

            $existingAttendances = Attendance::where('course_class_id', $selectedClass->id)
                ->where('attendance_date', $attendanceDate)
                ->pluck('status', 'user_id');
        }

        return view('teacher.attendance.index', compact(
            'classes', 'selectedClass', 'students',
            'attendanceDate', 'existingAttendances'
        ));
    }

    // Lưu điểm danh
    public function store(Request $request)
    {
        $request->validate([
            'class_id'        => 'required|exists:course_classes,id',
            'attendance_date' => 'required|date',
            'attendance'      => 'required|array',
        ]);

        $teacher = Auth::user();

        // Đảm bảo giáo viên thuộc lớp này
        $class = CourseClass::whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
            ->findOrFail($request->class_id);

        foreach ($request->attendance as $userId => $status) {
            Attendance::updateOrCreate(
                [
                    'course_class_id' => $class->id,
                    'user_id'         => $userId,
                    'attendance_date' => $request->attendance_date,
                ],
                ['status' => $status]
            );
        }

        return redirect()->back()->with('success', 'Điểm danh đã được lưu thành công!');
    }
}
