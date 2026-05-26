<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    /**
     * Giao diện danh sách bảng điểm (Trang index)
     */
    public function index()
    {
        // Lấy danh sách các bài đã chấm của học viên đang đăng nhập
        $results = Submission::with('assignment.courseClass')
            ->where('user_id', Auth::id())
            ->where('status', 'Đã chấm') // Hoặc bỏ điều kiện này nếu muốn hiện cả bài "Đã nộp"
            ->get();

        return view('student.results.index', compact('results'));
    }

    /**
     * Giao diện chi tiết bài làm và lịch sử phản hồi
     */
    public function show($id)
    {
        $submission = Submission::with(['assignment.courseClass', 'feedbacks.user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('student.results.show', compact('submission'));
    }

    /**
     * Xử lý lưu phản hồi thắc mắc mới từ học viên
     */
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'feedback_content' => 'required|string|max:1000',
        ]);

        $submission = Submission::where('user_id', Auth::id())->findOrFail($request->submission_id);

        Feedback::create([
            'feedback_content' => $request->feedback_content,
            'old_grade' => $submission->grade, 
            'new_grade' => null,               
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Đã gửi thắc mắc thành công!');
    }
}