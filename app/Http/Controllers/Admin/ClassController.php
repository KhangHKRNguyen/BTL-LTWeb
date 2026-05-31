<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    // Danh sách lớp học
    public function index(Request $request)
    {
        $query = CourseClass::withCount(['students', 'assignments']);

        if ($request->filled('search')) {
            $query->where('class_name', 'like', '%' . $request->search . '%');
        }

        $classes = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.classes.index', compact('classes'));
    }

    // Form tạo lớp học
    public function create()
    {
        return view('admin.classes.create');
    }

    // Lưu lớp học mới
    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'room'       => 'nullable|string|max:100',
        ], [
            'class_name.required' => 'Tên lớp không được để trống.',
            'start_time.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_time.required'   => 'Vui lòng chọn ngày kết thúc.',
            'end_time.after'      => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        CourseClass::create($request->only('class_name', 'start_time', 'end_time', 'room'));

        return redirect()->route('admin.classes.index')
                         ->with('success', 'Tạo lớp học thành công!');
    }

    // Form sửa lớp học
    public function edit(CourseClass $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    // Cập nhật lớp học
    public function update(Request $request, CourseClass $class)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'room'       => 'nullable|string|max:100',
        ], [
            'class_name.required' => 'Tên lớp không được để trống.',
            'start_time.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_time.required'   => 'Vui lòng chọn ngày kết thúc.',
            'end_time.after'      => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        $class->update($request->only('class_name', 'start_time', 'end_time', 'room'));

        return redirect()->route('admin.classes.index')
                         ->with('success', 'Cập nhật lớp học thành công!');
    }

    // Xóa lớp học (chỉ khi chưa có học viên và bài tập)
    public function destroy(CourseClass $class)
    {
        if (!$class->isDeletable()) {
            return redirect()->route('admin.classes.index')
                             ->with('error', 'Không thể xóa lớp đã có học viên hoặc bài tập!');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
                         ->with('success', 'Đã xóa lớp học thành công!');
    }

    // Trang quản lý thành viên lớp
    public function members(CourseClass $class)
    {
        $teacher  = $class->users()->where('users.role', 'teacher')->first();
        $students = $class->students()->orderBy('name')->get();

        // Danh sách giáo viên để chọn
        $allTeachers = User::where('role', 'teacher')->where('status', 'active')->orderBy('name')->get();

        // Học viên chưa có trong lớp (cho modal thêm)
        $availableStudents = User::where('role', 'student')
                                 ->where('status', 'active')
                                 ->whereNotIn('id', $students->pluck('id'))
                                 ->orderBy('name')
                                 ->get();

        return view('admin.classes.members', compact('class', 'teacher', 'students', 'allTeachers', 'availableStudents'));
    }

    // Gán giáo viên vào lớp
    public function assignTeacher(Request $request, CourseClass $class)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->teacher_id);

        if ($teacher->role !== 'teacher') {
            return back()->with('error', 'Người dùng này không phải giáo viên!');
        }

        // Xóa giáo viên cũ khỏi lớp
        $class->users()->wherePivot('user_id', function ($q) {
            $q->select('id')->from('users')->where('role', 'teacher');
        })->detach();

        // Xóa tất cả giáo viên hiện tại trong lớp rồi gán mới
        $currentTeacherIds = $class->users()->where('users.role', 'teacher')->pluck('users.id');
        $class->users()->detach($currentTeacherIds);

        // Gán giáo viên mới
        $class->users()->attach($teacher->id);

        return back()->with('success', 'Đã gán giáo viên thành công!');
    }

    // Thêm nhiều học viên vào lớp
    public function addStudents(Request $request, CourseClass $class)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ], [
            'student_ids.required' => 'Vui lòng chọn ít nhất 1 học viên.',
        ]);

        $studentIds = collect($request->student_ids)->filter(function ($id) {
            return User::where('id', $id)->where('role', 'student')->exists();
        });

        // Chỉ thêm những học viên chưa có trong lớp
        $class->users()->syncWithoutDetaching($studentIds->toArray());

        return back()->with('success', 'Đã thêm ' . $studentIds->count() . ' học viên vào lớp!');
    }

    // Xóa học viên khỏi lớp
    public function removeStudent(CourseClass $class, User $user)
    {
        $class->users()->detach($user->id);

        return back()->with('success', 'Đã xóa học viên khỏi lớp!');
    }
}
