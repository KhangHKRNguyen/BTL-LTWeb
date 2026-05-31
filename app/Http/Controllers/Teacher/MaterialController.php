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
    // Danh sách tài liệu theo lớp
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $classes = $teacher->classes;

        $selectedClass = null;
        $materials     = collect();

        if ($request->filled('class_id')) {
            $selectedClass = CourseClass::where('id', $request->class_id)
                ->whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
                ->firstOrFail();

            $materials = $selectedClass->materials()->latest()->get();
        }

        return view('teacher.materials.index', compact('classes', 'selectedClass', 'materials'));
    }

    // Upload tài liệu mới
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:course_classes,id',
            'title'    => 'required|string|max:255',
            'file'     => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:20480',
        ], [
            'file.mimes' => 'Chỉ chấp nhận file PDF, Word, PowerPoint hoặc Excel.',
            'file.max'   => 'File không được vượt quá 20MB.',
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

        return redirect()->route('teacher.materials.index', ['class_id' => $class->id])
            ->with('success', 'Tài liệu đã được tải lên thành công!');
    }

    // Download tài liệu qua PHP (không cần symlink)
    public function download(Material $material)
    {
        $teacher = Auth::user();

        // Chỉ giáo viên trong lớp mới được download
        CourseClass::whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
            ->findOrFail($material->course_class_id);

        if (!Storage::disk('public')->exists($material->file_path)) {
            return back()->with('error', 'File không tồn tại trên máy chủ.');
        }

        $fullPath  = Storage::disk('public')->path($material->file_path);
        $ext       = pathinfo($material->file_path, PATHINFO_EXTENSION);
        $fileName  = $material->title . '.' . $ext;

        return response()->download($fullPath, $fileName);
    }

    // Xóa tài liệu
    public function destroy(Material $material)
    {
        $teacher = Auth::user();

        $class = CourseClass::whereHas('users', fn($q) => $q->where('user_id', $teacher->id))
            ->findOrFail($material->course_class_id);

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return redirect()->route('teacher.materials.index', ['class_id' => $class->id])
            ->with('success', 'Tài liệu đã được xóa!');
    }
}
