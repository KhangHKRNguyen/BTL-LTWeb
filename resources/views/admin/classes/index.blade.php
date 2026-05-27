<x-app-layout>
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">Quản lý lớp học</h1>
            <a href="{{ route('admin.classes.create') }}"
               class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-emerald-700 transition text-sm">
                + Tạo lớp học
            </a>
        </div>

        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tìm theo tên lớp..."
                   class="border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 w-64">
            <button type="submit" class="bg-slate-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-slate-800 transition">Lọc</button>
            <a href="{{ route('admin.classes.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-300 transition">Xóa lọc</a>
        </form>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">#</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Tên lớp</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Phòng</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Thời gian</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">HV / BT</th>
                        <th class="text-right px-5 py-3 text-slate-600 font-semibold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($classes as $class)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-slate-500">{{ $class->id }}</td>
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $class->class_name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $class->room ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600 text-xs">
                            {{ $class->start_time?->format('d/m/Y') }} — {{ $class->end_time?->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            <span class="text-emerald-600 font-medium">{{ $class->students_count }}</span> HV /
                            <span class="text-blue-600 font-medium">{{ $class->assignments_count }}</span> BT
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.classes.members', $class) }}"
                                   class="text-emerald-600 hover:underline text-xs font-medium">Thành viên</a>
                                <a href="{{ route('admin.classes.edit', $class) }}"
                                   class="text-blue-600 hover:underline text-xs font-medium">Sửa</a>
                                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                      onsubmit="return confirm('Xóa lớp {{ $class->class_name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs font-medium">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">Không có lớp học nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $classes->links() }}</div>
    </div>
</x-app-layout>
