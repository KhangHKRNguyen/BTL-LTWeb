<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Assignment;
use App\Models\CourseClass;
use App\Services\ExcelImportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    public function index()
    {
        $classes = $this->teacherClasses()
            ->withCount(['students', 'assignments'])
            ->with(['assignments' => fn ($query) => $query->latest()->limit(5)])
            ->orderBy('class_name')
            ->get();

        return view('teacher.assignments.index', compact('classes'));
    }

    public function create(CourseClass $courseClass)
    {
        $this->authorizeClass($courseClass);

        $courseClass->loadCount('students');

        return view('teacher.assignments.create', compact('courseClass'));
    }

    public function store(Request $request, ExcelImportService $excelImportService)
    {
        $rules = [
            'course_class_id' => ['required', 'exists:course_classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['quiz', 'essay'])],
            'open_time' => ['nullable', 'date', 'before:due_time'],
            'due_time' => ['required', 'date', 'after:now'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'import_file' => ['nullable', 'file', 'max:5120'],
        ];

        if ($request->input('type') === 'quiz' && ! $request->hasFile('import_file')) {
            $rules = array_merge($rules, [
                'questions' => ['required', 'array', 'min:1'],
                'questions.*.question_text' => ['required', 'string'],
                'questions.*.option_a' => ['required', 'string'],
                'questions.*.option_b' => ['required', 'string'],
                'questions.*.option_c' => ['required', 'string'],
                'questions.*.option_d' => ['required', 'string'],
                'questions.*.correct_option' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            ]);
        }

        if ($request->input('type') === 'essay') {
            $rules['content'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);
        $courseClass = CourseClass::findOrFail($validated['course_class_id']);
        $this->authorizeClass($courseClass);

        $questions = [];
        if ($validated['type'] === 'quiz') {
            $questions = $request->hasFile('import_file')
                ? $excelImportService->importQuestions($request->file('import_file'))
                : array_values($validated['questions']);
        }

        $assignment = DB::transaction(function () use ($request, $validated, $courseClass, $questions) {
            $filePath = $request->file('attachment')?->store('assignments', 'public');

            $assignment = Assignment::create([
                'title' => $validated['title'],
                'content' => $validated['content'] ?? null,
                'type' => $validated['type'],
                'open_time' => $validated['open_time'] ?? now(),
                'due_time' => $validated['due_time'],
                'file_path' => $filePath,
                'course_class_id' => $courseClass->id,
            ]);

            foreach ($questions as $question) {
                $assignment->questions()->create([
                    'question_text' => $question['question_text'],
                    'option_a' => $question['option_a'],
                    'option_b' => $question['option_b'],
                    'option_c' => $question['option_c'],
                    'option_d' => $question['option_d'],
                    'correct_option' => mb_strtoupper($question['correct_option']),
                    'type' => 'single_choice',
                ]);
            }

            return $assignment;
        });

        return redirect()
            ->route('teacher.assignments.show', $assignment)
            ->with('success', 'Giao bài tập thành công.');
    }

    public function show(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->load(['courseClass', 'questions'])
            ->loadCount('submissions');

        return view('teacher.assignments.show', compact('assignment'));
    }

    public function template(ExcelImportService $excelImportService)
    {
        return response($excelImportService->sampleCsvContent(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_import_cau_hoi.csv"',
        ]);
    }

    private function teacherClasses()
    {
        return CourseClass::query()
            ->when(Auth::user()->role !== 'admin', function ($query) {
                $query->whereHas('users', fn ($userQuery) => $userQuery->where('users.id', Auth::id()));
            });
    }

    private function authorizeAssignment(Assignment $assignment): void
    {
        $this->authorizeClass($assignment->courseClass);
    }

    private function authorizeClass(CourseClass $courseClass): void
    {
        if (Auth::user()->role === 'admin') {
            return;
        }

        abort_unless(
            $courseClass->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }
}
