<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CourseClass;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Trang chọn lớp + ngày điểm danh
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $classes = $teacher->classes;

        $selectedClass      = null;
        $students           = collect();
        $existingAttendances = collect();
        $leaveRequests      = collect();   // <-- đơn báo nghỉ

        // Ngày mặc định = hôm nay, không cho chọn ngày tương lai
        $today          = now()->toDateString();
        $attendanceDate = $request->get('attendance_date', $today);

        // Nếu ngày được chọn là tương lai → đặt về hôm nay
        if ($attendanceDate > $today) {
            $attendanceDate = $today;
        }

        if ($request->filled('class_id')) {
            $selectedClass = CourseClass::where('id', $request->class_id)
                ->whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
                ->firstOrFail();

            $students = $selectedClass->students;

            // Điểm danh đã lưu của ngày được chọn
            $existingAttendances = Attendance::where('course_class_id', $selectedClass->id)
                ->where('attendance_date', $attendanceDate)
                ->pluck('status', 'user_id');

            // Đơn báo nghỉ của ngày được chọn trong lớp này
            $leaveRequests = LeaveRequest::where('course_class_id', $selectedClass->id)
                ->where('request_date', $attendanceDate)
                ->pluck('reason', 'user_id');  // key = user_id, value = lý do
        }

        return view('teacher.attendance.index', compact(
            'classes', 'selectedClass', 'students',
            'attendanceDate', 'today',
            'existingAttendances', 'leaveRequests'
        ));
    }

    // Lưu điểm danh
    public function store(Request $request)
    {
        $request->validate([
            'class_id'        => 'required|exists:course_classes,id',
            'attendance_date' => 'required|date|before_or_equal:today',
            'attendance'      => 'required|array',
        ], [
            'attendance_date.before_or_equal' => 'Không thể điểm danh cho ngày trong tương lai.',
        ]);

        $teacher = Auth::user();

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
