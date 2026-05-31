<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyController extends Controller
{
    /**
     * Danh sách tài liệu học tập
     */
    public function index()
    {
        // 1. Lấy danh sách lớp mà học viên đang tham gia kèm materials của lớp đó
        $classes = auth()->user()->courseClasses()
            ->with('materials')
            ->get();

        // 2. ĐÃ SỬA: Nếu không tìm thấy qua quan hệ, fetch đúng lớp từ table class_user 
        // Chứ không lấy bừa CourseClass::all() nữa để tránh lộ tài liệu lớp khác
        if ($classes->isEmpty()) {
            $classIds = \DB::table('class_user')
                ->where('user_id', auth()->id())
                ->pluck('course_class_id');

            $classes = CourseClass::whereIn('id', $classIds)->with('materials')->get();
        }

        return view('student.study.index', compact('classes'));
    }

    /**
     * Download file tài liệu
     */
    public function downloadMaterial($materialId)
    {
        // 1. Tìm tài liệu, nếu không thấy trả về 404
        $material = Material::findOrFail($materialId);

        // 2. ĐÃ SỬA: Sửa lỗi nhập nhằng cột id bằng cách thêm tên bảng 'course_classes.id'
        $userClassIds = auth()->user()->courseClasses()->pluck('course_classes.id');
        
        if ($userClassIds->isEmpty()) {
            $userClassIds = \DB::table('class_user')
                ->where('user_id', auth()->id())
                ->pluck('course_class_id');
        }

        // 3. ĐÃ BỔ SUNG: Kiểm tra học viên có thuộc lớp sở hữu tài liệu này không
        if (!$userClassIds->contains($material->course_class_id)) {
            abort(403, 'Bạn không có quyền tải tài liệu của lớp học này!');
        }
        
      // 4. Kiểm tra file tồn tại vật lý trên ổ đĩa
if (!Storage::disk('public')->exists($material->file_path)) {  // ✅
    abort(404, 'File không tìm thấy trên hệ thống vật lý!');
}

// 5. Tiến hành cho tải file
return Storage::disk('public')
    ->download(
        $material->file_path,
        basename($material->file_path)
    ); // ✅
}}