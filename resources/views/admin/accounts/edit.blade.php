<x-app-layout>
    <div class="max-w-lg space-y-5">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.accounts.index') }}" class="text-slate-400 hover:text-slate-600">← Quay lại</a>
            <h1 class="text-2xl font-bold text-slate-800">Sửa tài khoản</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <form method="POST" action="{{ route('admin.accounts.update', $user) }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full border border-slate-200 bg-slate-50 rounded-lg px-4 py-2 text-sm text-slate-400 cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">Email không thể thay đổi.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
                    <select name="role" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-400 @enderror">
                        <option value="admin"   @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="teacher" @selected(old('role', $user->role) === 'teacher')>Giáo viên</option>
                        <option value="student" @selected(old('role', $user->role) === 'student')>Học viên</option>
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex space-x-3 pt-2">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.accounts.index') }}" class="bg-slate-100 text-slate-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
