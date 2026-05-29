<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Tiêu đề --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Điểm danh lớp học</h1>
            <p class="text-sm text-slate-500 mt-1">Chọn lớp và ngày để thực hiện điểm danh học viên.</p>
        </div>

        {{-- Form chọn lớp & ngày --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form method="GET" action="{{ route('teacher.attendance.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Lớp học</label>
                    <select name="class_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Chọn lớp --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ngày điểm danh</label>
                    <input type="date" name="attendance_date"
                        value="{{ $attendanceDate }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Tải danh sách
                </button>
            </form>
        </div>

        {{-- Bảng điểm danh --}}
        @if($selectedClass)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-4">
                    Lớp: <span class="text-blue-600">{{ $selectedClass->class_name }}</span>
                    &mdash; Ngày: <span class="text-blue-600">{{ \Carbon\Carbon::parse($attendanceDate)->format('d/m/Y') }}</span>
                </h2>

                @if($students->isEmpty())
                    <p class="text-slate-500 text-sm">Lớp này chưa có học viên nào.</p>
                @else
                    <form method="POST" action="{{ route('teacher.attendance.store') }}">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                        <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 text-left">
                                        <th class="px-4 py-3 font-semibold rounded-tl-lg">#</th>
                                        <th class="px-4 py-3 font-semibold">Họ tên học viên</th>
                                        <th class="px-4 py-3 font-semibold">Email</th>
                                        <th class="px-4 py-3 font-semibold text-center rounded-tr-lg">Tình trạng</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($students as $i => $student)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                                            <td class="px-4 py-3 font-medium text-slate-800">{{ $student->name }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $student->email }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex justify-center gap-4">
                                                    @foreach(['Có mặt', 'Vắng', 'Muộn'] as $statusOption)
                                                        @php
                                                            $current = $existingAttendances->get($student->id, 'Có mặt');
                                                            $colors = [
                                                                'Có mặt' => 'peer-checked:bg-emerald-500 peer-checked:border-emerald-500',
                                                                'Vắng'   => 'peer-checked:bg-rose-500 peer-checked:border-rose-500',
                                                                'Muộn'   => 'peer-checked:bg-amber-500 peer-checked:border-amber-500',
                                                            ];
                                                        @endphp
                                                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                            <input type="radio"
                                                                name="attendance[{{ $student->id }}]"
                                                                value="{{ $statusOption }}"
                                                                class="peer hidden"
                                                                {{ $current === $statusOption ? 'checked' : '' }}>
                                                            <span class="w-4 h-4 rounded-full border-2 border-slate-300 {{ $colors[$statusOption] }} transition flex items-center justify-center">
                                                            </span>
                                                            <span class="text-xs text-slate-600">{{ $statusOption }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                                Lưu điểm danh
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
