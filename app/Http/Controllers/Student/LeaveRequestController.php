<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\CourseClass;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /**
     * Danh sách đơn xin nghỉ của học viên
     */
    public function index()
    {
        // Lấy danh sách đơn xin nghỉ của học viên hiện tại
        $leaveRequests = LeaveRequest::where('user_id', auth()->id())
            ->with('courseClass')
            ->orderBy('request_date', 'desc')
            ->paginate(10);

        return view('student.leave_requests.index', compact('leaveRequests'));
    }

    /**
     * Hiển thị form xin nghỉ
     */
    public function create()
    {
        // Lấy danh sách lớp mà học viên đang tham gia
        $classes = auth()->user()->courseClasses()->get();

        // Nếu không có quan hệ, có thể fetch từ table class_user
        if ($classes->isEmpty()) {
            $classes = CourseClass::all();
        }

        return view('student.leave_requests.create', compact('classes'));
    }

    /**
     * Lưu đơn xin nghỉ
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'request_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string',
            'course_class_id' => 'required|exists:course_classes,id'
        ], [
            'request_date.required' => 'Vui lòng chọn ngày xin nghỉ',
            'request_date.after_or_equal' => 'Ngày xin nghỉ phải từ hôm nay trở đi',
            'reason.required' => 'Vui lòng nhập lý do xin nghỉ',
  
            'course_class_id.required' => 'Vui lòng chọn lớp học'
        ]);

        // Thêm user_id
        $validated['user_id'] = auth()->id();

        // Tạo đơn xin nghỉ
        LeaveRequest::create($validated);

        return redirect()->route('student.leave_requests.index')
            ->with('success', 'Đơn xin nghỉ được gửi thành công!');
    }
}
