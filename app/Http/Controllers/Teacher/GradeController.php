<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Assignment;
use App\Models\Feedback;
use App\Models\Submission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index()
    {
        $assignments = Assignment::query()
            ->with('courseClass')
            ->withCount([
                'submissions',
                'submissions as pending_submissions_count' => fn ($query) => $query->whereNull('grade')->whereNotIn('status', ['graded', 'Đã chấm']),
            ])
            ->whereHas('courseClass', function ($query) {
                $this->scopeTeacherClasses($query);
            })
            ->latest()
            ->paginate(10);

        return view('teacher.grades.index', compact('assignments'));
    }

    public function submissions(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        if ($assignment->isQuiz()) {
            $assignment->load(['questions', 'submissions.answers.question']);
            foreach ($assignment->submissions as $submission) {
                $this->syncQuizGrade($submission);
            }
        }

        $assignment->load(['courseClass', 'submissions.student'])
            ->loadCount('questions');

        $submissions = $assignment->submissions()->with('student')->latest()->paginate(15);

        return view('teacher.grades.submissions', compact('assignment', 'submissions'));
    }

    // Giáo viên phản hồi lại thắc mắc của học viên
    public function replyFeedback(Request $request, Feedback $feedback)
    {
        $request->validate([
            'teacher_reply' => 'required|string|max:1000',
        ], [
            'teacher_reply.required' => 'Vui lòng nhập nội dung phản hồi.',
        ]);

        // Kiểm tra quyền: giáo viên phải phụ trách lớp của bài nộp này
        $submission = $feedback->submission()->with('assignment.courseClass')->first();
        $this->authorizeAssignment($submission->assignment);

        $feedback->update([
            'teacher_reply'      => $request->teacher_reply,
            'teacher_replied_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đã gửi phản hồi tới học viên!');
    }

    public function edit(Submission $submission)
    {
        $submission->load([
            'student',
            'assignment.courseClass',
            'assignment.questions',
            'answers.question',
            'feedbacks.user',   // load thắc mắc của học viên
        ]);

        $this->authorizeAssignment($submission->assignment);

        if ($submission->assignment->isQuiz()) {
            $this->syncQuizGrade($submission);
            $submission->refresh()->load(['student', 'assignment.questions', 'answers.question']);
        }

        return view('teacher.grades.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        $submission->load(['assignment.questions', 'answers.question']);
        $this->authorizeAssignment($submission->assignment);

        if ($submission->assignment->isQuiz()) {
            $validated = $request->validate([
                'teacher_comment' => ['nullable', 'string'],
            ]);

            $grade = $this->calculateQuizGrade($submission);
            $submission->update([
                'grade' => $grade,
                'teacher_comment' => $validated['teacher_comment'] ?? null,
                'status' => 'graded',
            ]);
        } else {
            $validated = $request->validate([
                'grade' => ['required', 'numeric', 'min:0', 'max:10'],
                'teacher_comment' => ['nullable', 'string'],
            ]);

            $submission->update([
                'grade' => $validated['grade'],
                'teacher_comment' => $validated['teacher_comment'] ?? null,
                'status' => 'graded',
            ]);
        }

        return redirect()
            ->route('teacher.grades.submissions', $submission->assignment)
            ->with('success', 'Chấm bài thành công.');
    }

    private function syncQuizGrade(Submission $submission): void
    {
        if (! $submission->assignment->isQuiz()) {
            return;
        }

        $grade = $this->calculateQuizGrade($submission);

        if ((string) $submission->grade !== number_format($grade, 2, '.', '')) {
            $submission->forceFill(['grade' => $grade])->save();
        }
    }

    private function calculateQuizGrade(Submission $submission): float
    {
        $submission->loadMissing(['assignment.questions', 'answers.question']);
        $questions = $submission->assignment->questions;

        if ($questions->isEmpty()) {
            return 0.0;
        }

        $answers = $submission->answers->keyBy('question_id');
        $correct = $questions->filter(function ($question) use ($answers) {
            $answer = $answers->get($question->id);

            return $answer && mb_strtoupper((string) $answer->selected_option) === mb_strtoupper((string) $question->correct_option);
        })->count();

        return round(($correct / $questions->count()) * 10, 2);
    }

    private function authorizeAssignment(Assignment $assignment): void
    {
        if (Auth::user()->role === 'admin') {
            return;
        }

        abort_unless(
            $assignment->courseClass->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    private function scopeTeacherClasses($query): void
    {
        if (Auth::user()->role === 'admin') {
            return;
        }

        $query->whereHas('users', fn ($userQuery) => $userQuery->where('users.id', Auth::id()));
    }
}
