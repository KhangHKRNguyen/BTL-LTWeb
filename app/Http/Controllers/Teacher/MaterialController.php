<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $classes = $teacher->classes;

        $selectedClass = null;
        $materials     = collect();
        $search        = $request->get('search', '');
        $filterType    = $request->get('type', '');
        $stats         = [];

        if ($request->filled('class_id')) {
            $selectedClass = CourseClass::where('id', $request->class_id)
                ->whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
                ->firstOrFail();

            // Thống kê số lượng theo định dạng file
            $allMaterials = $selectedClass->materials()->get();
            $stats = [
                'total' => $allMaterials->count(),
                'pdf'   => $allMaterials->filter(fn($m) => strtolower(pathinfo($m->file_path, PATHINFO_EXTENSION)) === 'pdf')->count(),
                'word'  => $allMaterials->filter(fn($m) => in_array(strtolower(pathinfo($m->file_path, PATHINFO_EXTENSION)), ['doc', 'docx']))->count(),
                'ppt'   => $allMaterials->filter(fn($m) => in_array(strtolower(pathinfo($m->file_path, PATHINFO_EXTENSION)), ['ppt', 'pptx']))->count(),
                'excel' => $allMaterials->filter(fn($m) => in_array(strtolower(pathinfo($m->file_path, PATHINFO_EXTENSION)), ['xls', 'xlsx']))->count(),
            ];

            $query = $selectedClass->materials()->latest();

            // Tìm kiếm theo tiêu đề
            if ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            }

            // Lọc theo định dạng file
            if ($filterType) {
                $typeMap = [
                    'pdf'   => ['pdf'],
                    'word'  => ['doc', 'docx'],
                    'ppt'   => ['ppt', 'pptx'],
                    'excel' => ['xls', 'xlsx'],
                ];
                if (isset($typeMap[$filterType])) {
                    $exts = $typeMap[$filterType];
                    $query->where(function ($q) use ($exts) {
                        foreach ($exts as $ext) {
                            $q->orWhere('file_path', 'like', '%.' . $ext);
                        }
                    });
                }
            }

            $materials = $query->paginate(10)->withQueryString();
        }

        return view('teacher.materials.index', compact(
            'classes', 'selectedClass', 'materials',
            'search', 'filterType', 'stats'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:course_classes,id',
            'title'    => 'required|string|max:255',
            'file'     => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:20480',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề tài liệu.',
            'title.max'      => 'Tiêu đề không được vượt quá 255 ký tự.',
            'file.required'  => 'Vui lòng chọn file tài liệu.',
            'file.mimes'     => 'Chỉ chấp nhận file PDF, Word, PowerPoint hoặc Excel.',
            'file.max'       => 'File không được vượt quá 20MB.',
        ]);

        $teacher = Auth::user();

        $class = CourseClass::whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
            ->findOrFail($request->class_id);

        $path = $request->file('file')->store('uploads/materials', 'public');

        Material::create([
            'title'           => $request->title,
            'file_path'       => $path,
            'course_class_id' => $class->id,
        ]);

        return redirect()->route('teacher.materials.index', [
            'class_id' => $class->id,
            'search'   => $request->get('search', ''),
            'type'     => $request->get('type', ''),
        ])->with('success', 'Tài liệu đã được tải lên thành công!');
    }

    public function download(Material $material)
    {
        $teacher = Auth::user();

        CourseClass::whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
            ->findOrFail($material->course_class_id);

        if (!Storage::disk('public')->exists($material->file_path)) {
            return back()->with('error', 'File không tồn tại trên máy chủ.');
        }

        $fullPath = Storage::disk('public')->path($material->file_path);
        $ext      = pathinfo($material->file_path, PATHINFO_EXTENSION);
        $fileName = $material->title . '.' . $ext;

        return response()->download($fullPath, $fileName);
    }

    public function destroy(Material $material)
    {
        $teacher = Auth::user();

        $class = CourseClass::whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
            ->findOrFail($material->course_class_id);

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return redirect()->route('teacher.materials.index', [
            'class_id' => $class->id,
            'search'   => request('search', ''),
            'type'     => request('type', ''),
        ])->with('success', 'Tài liệu đã được xóa!');
    }
}
