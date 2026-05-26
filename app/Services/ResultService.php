<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class ResultService
{
    /**
     * Lấy toàn bộ danh sách bài tập đã nộp của Học viên kèm theo điểm số và thông tin bài tập
     */
    public function getStudentResults($studentId)
    {
        return Submission::with(['assignment.courseClass'])
            ->where('user_id', $studentId)
            ->whereNotNull('grade') // Chỉ lấy những bài đã được chấm điểm (Tự động hoặc Giáo viên chấm)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Lấy chi tiết một bài nộp cụ thể kèm chuỗi hội thoại phản hồi (Feedback)
     */
    public function getSubmissionDetails($submissionId, $studentId)
    {
        return Submission::with(['assignment', 'feedbacks.user'])
            ->where('id', $submissionId)
            ->where('user_id', $studentId)
            ->firstOrFail();
    }

    /**
     * Xử lý lưu tin nhắn phản hồi thắc mắc của học viên gửi tới giáo viên
     */
    public function sendFeedback($submissionId, $studentId, $message)
    {
        // Kiểm tra tính hợp lệ xem bài nộp này có đúng của học viên đó không
        $submission = Submission::where('id', $submissionId)
            ->where('user_id', $studentId)
            ->firstOrFail();

        return Feedback::create([
            'submission_id' => $submission->id,
            'user_id' => $studentId,
            'message' => $message,
        ]);
    }
}