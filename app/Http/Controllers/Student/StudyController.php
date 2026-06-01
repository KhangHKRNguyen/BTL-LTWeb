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
    public function index(Request $request)
    {
        // 1. Lấy danh sách lớp mà học viên đang tham gia kèm materials của lớp đó
        $classIds = auth()->user()->courseClasses()
            ->pluck('course_classes.id');

        // 2. ĐÃ SỬA: Nếu không tìm thấy qua quan hệ, fetch đúng lớp từ table class_user 
        // Chứ không lấy bừa CourseClass::all() nữa để tránh lộ tài liệu lớp khác
        if ($classIds->isEmpty()) {
            $classIds = \DB::table('class_user')
                ->where('user_id', auth()->id())
                ->pluck('course_class_id');

          //  $classes = CourseClass::whereIn('id', $classIds)->with('materials')->get();
        }
if ($classIds->isEmpty()) {
        $classIds = collect([]);
    }
       // 2. Lấy danh sách tất cả các lớp của học viên (Dùng làm dữ liệu đổ vào ô Select bộ lọc)
    $filterClasses = CourseClass::whereIn('id', $classIds)->get();

    // 3. Xử lý lấy dữ liệu hiển thị kèm bộ lọc
    $query = CourseClass::whereIn('id', $classIds);

    // Nếu học viên chọn một lớp cụ thể, ta lọc chỉ lấy đúng lớp đó
    if ($request->has('class_id') && $request->class_id != '') {
        $query->where('id', $request->class_id);
    }

    // Eager load tài liệu (materials) của các lớp thỏa mãn điều kiện
    $classes = $query->with('materials')->get();

    // 4. Truyền thêm biến $filterClasses sang View để làm bộ lọc
    return view('student.study.index', compact('classes', 'filterClasses'));
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