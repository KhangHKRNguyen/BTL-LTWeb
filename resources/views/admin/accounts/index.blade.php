<x-app-layout>
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">Quản lý tài khoản</h1>
            <a href="{{ route('admin.accounts.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition text-sm">
                + Tạo tài khoản
            </a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Bộ lọc --}}
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tìm theo tên hoặc email..."
                   class="border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <select name="role" class="border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Tất cả vai trò --</option>
                <option value="admin"   @selected(request('role') === 'admin')>Admin</option>
                <option value="teacher" @selected(request('role') === 'teacher')>Giáo viên</option>
                <option value="student" @selected(request('role') === 'student')>Học viên</option>
            </select>
            <button type="submit" class="bg-slate-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-slate-800 transition">Lọc</button>
            <a href="{{ route('admin.accounts.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-300 transition">Xóa lọc</a>
        </form>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">#</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Họ tên</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Email</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Vai trò</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Trạng thái</th>
                        <th class="text-right px-5 py-3 text-slate-600 font-semibold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-slate-500">{{ $user->id }}</td>
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @php
                                $roleColor = match($user->role) {
                                    'admin'   => 'bg-purple-100 text-purple-700',
                                    'teacher' => 'bg-blue-100 text-blue-700',
                                    'student' => 'bg-emerald-100 text-emerald-700',
                                    default   => 'bg-slate-100 text-slate-700',
                                };
                                $roleLabel = match($user->role) {
                                    'admin'   => 'Admin',
                                    'teacher' => 'Giáo viên',
                                    'student' => 'Học viên',
                                    default   => $user->role,
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColor }}">{{ $roleLabel }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if($user->status === 'active')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Hoạt động</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Đã khóa</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end space-x-2">
                                {{-- Sửa: Không cho sửa admin khác --}}
                                @if($user->id === auth()->id() || $user->role !== 'admin')
                                <a href="{{ route('admin.accounts.edit', $user) }}"
                                   class="text-blue-600 hover:underline text-xs font-medium">Sửa</a>
                                @endif

                                {{-- Khóa/Mở: Không áp dụng với chính mình và admin khác --}}
                                @if($user->id !== auth()->id() && $user->role !== 'admin')
                                <form method="POST" action="{{ route('admin.accounts.toggle-status', $user) }}"
                                      onsubmit="return confirm('{{ $user->status === 'active' ? 'Khóa tài khoản này?' : 'Mở khóa tài khoản này?' }}')">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="{{ $user->status === 'active' ? 'text-amber-600 hover:underline' : 'text-green-600 hover:underline' }} text-xs font-medium">
                                        {{ $user->status === 'active' ? 'Khóa' : 'Mở khóa' }}
                                    </button>
                                </form>

                                {{-- Xóa: Không áp dụng với chính mình và admin khác --}}
                                <form method="POST" action="{{ route('admin.accounts.destroy', $user) }}"
                                      onsubmit="return confirm('Xóa tài khoản {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs font-medium">Xóa</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">Không tìm thấy tài khoản nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $users->links() }}</div>
    </div>
</x-app-layout>
