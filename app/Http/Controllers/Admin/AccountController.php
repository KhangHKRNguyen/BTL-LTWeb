<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.accounts.index', compact('users'));
    }

    public function create()
    {
        return view('admin.accounts.create');
    }

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
            'password' => Hash::make('1'),
            'role'     => $request->role,
            'status'   => 'active',
        ]);

        return redirect()->route('admin.accounts.index')
                         ->with('success', 'Tạo tài khoản thành công! Mật khẩu mặc định: 1');
    }

    public function edit(User $user)
    {
        return view('admin.accounts.edit', compact('user'));
    }

    // FIX 2: Chặn đổi role của chính mình và admin khác
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
        ], [
            'name.required' => 'Họ tên không được để trống.',
            'role.required' => 'Vui lòng chọn vai trò.',
        ]);

        // Không cho đổi role của chính mình
        if ($user->id === auth()->id() && $request->role !== $user->role) {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Bạn không thể thay đổi vai trò của chính mình!');
        }

        // Không cho đổi role của admin khác
        if ($user->role === 'admin' && $user->id !== auth()->id() && $request->role !== 'admin') {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Không thể thay đổi vai trò của tài khoản Admin khác!');
        }

        $user->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.accounts.index')
                         ->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
        }

        // Không cho xóa admin khác
        if ($user->role === 'admin') {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Không thể xóa tài khoản Admin khác!');
        }

        $user->delete();

        return redirect()->route('admin.accounts.index')
                         ->with('success', 'Đã xóa tài khoản thành công!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Bạn không thể khóa tài khoản của chính mình!');
        }

        // Không cho khóa admin khác
        if ($user->role === 'admin') {
            return redirect()->route('admin.accounts.index')
                             ->with('error', 'Không thể khóa tài khoản Admin khác!');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        $msg = $user->status === 'active' ? 'Đã mở khóa tài khoản!' : 'Đã khóa tài khoản!';

        return redirect()->route('admin.accounts.index')->with('success', $msg);
    }
}
