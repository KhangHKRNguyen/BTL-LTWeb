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
                'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,jpg,png,jpeg'
            ], [
                'file.max' => 'File không được vượt quá 10MB',
                'file.mimes' => 'File phải là PDF, DOC, DOCX, TXT hoặc hình ảnh'
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
                'status' => 'submitted'
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
                $filePath = $file->store('submissions/' . $assignmentId, 'private');
                $submission->file_path = $filePath;
            }

            $submission->status = 'submitted';
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

            $submission->status = 'submitted';
            $submission->save();
        }

        return redirect()->route('student.assignments.index')
            ->with('success', 'Bài tập nộp thành công!');
    }
}

// Truy xuất danh sách bài tập được giao cho sinh viên từ database dựa trên các lớp học.

// Khi sinh viên bấm "Làm bài": Controller này sẽ lấy nội dung chi tiết của đề bài (câu hỏi trắc nghiệm hoặc file đề bài tự luận) để hiển thị lên giao diện.

// Khi sinh viên nộp bài: Tiếp nhận dữ liệu câu trả lời.

// Nếu là trắc nghiệm: Xử lý lưu các phương án sinh viên chọn.

// Nếu là tự luận: Xử lý logic upload file bài làm (validate định dạng file, kích thước file, lưu file vào thư mục lưu trữ của hệ thống).

// Gọi Model StudentAnswer để ghi nhận bài nộp vào database và cập nhật trạng thái "Đã nộp".