<x-app-layout>
    <div class="max-w-lg space-y-5">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.accounts.index') }}" class="text-slate-400 hover:text-slate-600">← Quay lại</a>
            <h1 class="text-2xl font-bold text-slate-800">Tạo tài khoản mới</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <form method="POST" action="{{ route('admin.accounts.store') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                               placeholder="Nguyễn Văn A">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror"
                               placeholder="example@gmail.com">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
                        <select name="role" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-400 @enderror">
                            <option value="">-- Chọn vai trò --</option>
                            <option value="admin"   @selected(old('role') === 'admin')>Admin</option>
                            <option value="teacher" @selected(old('role') === 'teacher')>Giáo viên</option>
                            <option value="student" @selected(old('role') === 'student')>Học viên</option>
                        </select>
                        @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <p class="text-xs text-slate-400">* Mật khẩu mặc định sau khi tạo là: <strong>1</strong></p>
                </div>

                <div class="flex space-x-3 pt-2">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Tạo tài khoản
                    </button>
                    <a href="{{ route('admin.accounts.index') }}" class="bg-slate-100 text-slate-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
