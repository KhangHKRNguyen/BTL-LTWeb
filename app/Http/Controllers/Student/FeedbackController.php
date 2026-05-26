<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    protected $resultService;

    public function __construct(ResultService $resultService)
    {
        $this->resultService = $resultService;
    }

    // Lưu phản hồi mới của học viên
    public function store(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'message' => 'required|string|max:1000',
        ]);

        $this->resultService->sendFeedback(
            $request->submission_id,
            Auth::id(),
            $request->message
        );

        return redirect()->back()->with('success', 'Đã gửi phản hồi thắc mắc thành công tới giáo viên!');
    }
}