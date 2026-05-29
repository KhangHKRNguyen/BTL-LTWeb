<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    // Danh sách tài khoản
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo vai trò
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.accounts.index', compact('users'));
    }

    // Form tạo tài khoản
    public function create()
    {
        return view('admin.accounts.create');
    }

    // Lưu tài khoản mới
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role'  => ['required', Rule::in(['admin', 'teacher', 'student'])],
        ], [
            'name.required'  => 'Họ tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email'    => 'Email không hợp lệ.',
            'email.unique'   => 'Email này đã tồn tại trong hệ thống.',
            'role.required'  => 'Vui lòng chọn vai trò.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make('1'), // Mật khẩu mặc định là 1
            'role'     => $request->role,
            'status'   => 'active',
        ]);

        return redirect()->route('admin.accounts.index')
                         ->with('success', 'Tạo tài khoản thành công! Mật khẩu mặc định: 1');
    }

    // Form sửa tài khoản
    public function edit(User $user)
    {
        return view('admin.accounts.edit', compact('user'));
    }

    // Cập nhật tài khoản
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
        ], [
            'name.required' => 'Họ tên không được để trống.',
            'role.required' => 'Vui lòng chọn vai trò.',
        ]);

        $user->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.accounts.index')
                         ->with('success', 'Cập nhật tài khoản thành công!');
    }

    // Xóa mềm tài khoản
    public function destroy(User $user)
    {
        // Không cho xóa chính mình
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
        }

        $user->delete(); // SoftDelete

        return redirect()->route('admin.accounts.index')
                         ->with('success', 'Đã xóa tài khoản thành công!');
    }

    // Bật / Tắt trạng thái tài khoản
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Bạn không thể khóa tài khoản của chính mình!');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        $msg = $user->status === 'active' ? 'Đã mở khóa tài khoản!' : 'Đã khóa tài khoản!';

        return redirect()->route('admin.accounts.index')->with('success', $msg);
    }
}
