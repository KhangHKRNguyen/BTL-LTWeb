<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoAssignmentController extends Controller
{
    /**
     * Danh sách bài tập
     */
    public function index()
    {
        // Lấy danh sách lớp của học viên
        $classIds = auth()->user()->courseClasses()->pluck('course_classes.id');

        // Nếu không có quan hệ, lấy từ class_user table
        if ($classIds->isEmpty()) {
            $classIds = \DB::table('class_user')
                ->where('user_id', auth()->id())
                ->pluck('course_class_id');
        }

        // Lấy danh sách assignments của các lớp
        $assignments = Assignment::whereIn('course_class_id', $classIds)
            ->with(['courseClass', 'submissions' => function($q) {
                $q->where('user_id', auth()->id());
            }])
            ->orderBy('due_time', 'asc')
            ->get();

        return view('student.assignments.index', compact('assignments'));
    }

    /**
     * Hiển thị form làm bài
     */
    public function show($assignmentId)
    {
        $assignment = Assignment::with(['questions', 'courseClass'])->findOrFail($assignmentId);

        // Kiểm tra học viên có quyền làm bài này không
        $userClassIds = auth()->user()->courseClasses()->pluck('course_classes.id');
        
        if ($userClassIds->isEmpty()) {
            $userClassIds = \DB::table('class_user')
                ->where('user_id', auth()->id())
                ->pluck('course_class_id');
        }

        if (!$userClassIds->contains($assignment->course_class_id)) {
            abort(403, 'Không có quyền truy cập bài tập này');
        }

        // Kiểm tra bài tập đã mở chưa
        if (now() < $assignment->open_time) {
            abort(403, 'Bài tập này chưa được mở');
        }

        // Lấy submission cũ nếu có
        $submission = Submission::where('assignment_id', $assignmentId)
            ->where('user_id', auth()->id())
            ->with('studentAnswers')
            ->first();

        return view('student.assignments.do', compact('assignment', 'submission'));
    }

  public function viewFile(Submission $submission)
{
    if (!$submission->file_path) {
        abort(404);
    }

    return Storage::disk('local')
        ->response($submission->file_path);
}

    /**
     * Lưu bài nộp
     */
    public function store(Request $request, $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Validate theo loại bài tập
        if ($assignment->type === 'Trắc nghiệm') {
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|exists:questions,id',
                'answers.*.selected_option' => 'required|in:A,B,C,D'
            ], [
                'answers.required' => 'Vui lòng trả lời tất cả các câu hỏi',
                'answers.*.selected_option.required' => 'Vui lòng chọn đáp án',
            ]);
        } else {
            // Tự luận
            $validated = $request->validate([
                'submission_content' => 'nullable|string',
                'file' => 'nullable|file|max:20480|mimes:pdf,doc,docx,txt,jpg,png,jpeg,mp3,m4a,wav'
            ], [
                'file.max' => 'File không được vượt quá 20MB',
                'file.mimes' => 'File phải là PDF, DOC, DOCX, TXT, JPG, PNG, MP3, M4A hoặc WAV'
            ]);

            // Kiểm tra ít nhất phải có 1 trong 2: nội dung hoặc file
            if (empty($validated['submission_content']) && !$request->hasFile('file')) {
                return back()->withErrors(['submission' => 'Vui lòng nhập nội dung hoặc upload file']);
            }
        }

        // Lấy hoặc tạo submission
        $submission = Submission::firstOrCreate(
            [
                'assignment_id' => $assignmentId,
                'user_id' => auth()->id()
            ],
            [
                'status' => 'Đã nộp'
            ]
        );

        // Cập nhật nếu là tự luận
        if ($assignment->type === 'Tự luận') {
            $submission->submission_content = $validated['submission_content'] ?? null;

            // Xử lý upload file
            if ($request->hasFile('file')) {
                // Xóa file cũ nếu có
                if ($submission->file_path && Storage::exists($submission->file_path)) {
                    Storage::delete($submission->file_path);
                }

                // Lưu file mới
                $file = $request->file('file');
                $filePath = $file->store('submissions/' . $assignmentId, 'local');
                $submission->file_path = $filePath;
            }

            $submission->status = 'Đã nộp';
            $submission->save();
        } else {
            // TRẮC NGHIỆM: Lưu student answers
            // Xóa câu trả lời cũ nếu có
            $submission->studentAnswers()->delete();

            // Tạo câu trả lời mới
            foreach ($validated['answers'] as $answer) {
                StudentAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => $answer['question_id'],
                    'selected_option' => $answer['selected_option']
                ]);
            }

            $submission->status = 'Đã nộp';
            $submission->save();
        }

        return redirect()->route('student.assignments.index')
            ->with('success', 'Bài tập nộp thành công!');
    }
}

// Gọi Model StudentAnswer để ghi nhận bài nộp vào database và cập nhật trạng thái "Đã nộp".