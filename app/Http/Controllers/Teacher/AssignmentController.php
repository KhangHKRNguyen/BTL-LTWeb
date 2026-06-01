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

    public function parseImport(Request $request, ExcelImportService $excelImportService)
    {
        try {
            $request->validate([
                'import_file' => ['required', 'file', 'max:5120'],
            ]);
            
            $questions = $excelImportService->importQuestions($request->file('import_file'));
            
            return response()->json([
                'success' => true,
                'questions' => $questions,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first('import_file'),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý file hoặc cấu trúc Excel/CSV không đúng định dạng mẫu.',
            ], 500);
        }
    }

    public function export(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        $questions = $assignment->questions()->get();
        
        $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel compatibility
        $csvContent .= "question_text,option_a,option_b,option_c,option_d,correct_option\n";
        
        foreach ($questions as $question) {
            $text = str_replace('"', '""', $question->question_text);
            $a = str_replace('"', '""', $question->option_a);
            $b = str_replace('"', '""', $question->option_b);
            $c = str_replace('"', '""', $question->option_c);
            $d = str_replace('"', '""', $question->option_d);
            $correct = $question->correct_option;
            
            $csvContent .= "\"{$text}\",\"{$a}\",\"{$b}\",\"{$c}\",\"{$d}\",\"{$correct}\"\n";
        }
        
        $filename = "danh_sach_cau_hoi_" . \Illuminate\Support\Str::slug($assignment->title) . ".csv";
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function downloadAttachment(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        
        if (!$assignment->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($assignment->file_path)) {
            return redirect()->back()->with('error', 'Không tìm thấy file đề bài đính kèm.');
        }
        
        $pathInfo = pathinfo($assignment->file_path);
        $extension = $pathInfo['extension'] ?? 'bin';
        
        $filename = "de_bai_" . \Illuminate\Support\Str::slug($assignment->title) . "." . $extension;
        
        return \Illuminate\Support\Facades\Storage::disk('public')->download($assignment->file_path, $filename);
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
