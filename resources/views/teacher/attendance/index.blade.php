<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Tiêu đề --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Điểm danh lớp học</h1>
            <p class="text-sm text-slate-500 mt-1">Chọn lớp và ngày để thực hiện điểm danh. Chỉ được điểm danh ngày hôm nay hoặc quá khứ.</p>
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
                        max="{{ $today }}"
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
            @php $hasLeave = $leaveRequests->isNotEmpty(); @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-slate-700">
                        Lớp: <span class="text-blue-600">{{ $selectedClass->class_name }}</span>
                        &mdash; Ngày: <span class="text-blue-600">{{ \Carbon\Carbon::parse($attendanceDate)->format('d/m/Y') }}</span>
                    </h2>
                    @if($hasLeave)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                            ⚠ Có {{ $leaveRequests->count() }} đơn báo nghỉ hôm nay
                        </span>
                    @endif
                </div>

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
                                        <th class="px-4 py-3 font-semibold">Đơn báo nghỉ</th>
                                        <th class="px-4 py-3 font-semibold text-center rounded-tr-lg">Tình trạng điểm danh</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($students as $i => $student)
                                        @php
                                            $leaveReason = $leaveRequests->get($student->id);
                                            $hasStudentLeave = !is_null($leaveReason);

                                            // Nếu có đơn báo nghỉ và chưa điểm danh → mặc định Vắng
                                            $defaultStatus = $hasStudentLeave ? 'Vắng' : 'Có mặt';
                                            $current = $existingAttendances->get($student->id, $defaultStatus);

                                            $statusColors = [
                                                'Có mặt' => 'peer-checked:bg-emerald-500 peer-checked:border-emerald-500',
                                                'Vắng'   => 'peer-checked:bg-rose-500 peer-checked:border-rose-500',
                                                'Muộn'   => 'peer-checked:bg-amber-500 peer-checked:border-amber-500',
                                            ];
                                        @endphp
                                        <tr class="{{ $hasStudentLeave ? 'bg-amber-50' : 'hover:bg-slate-50' }} transition">
                                            <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-slate-800">{{ $student->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $student->email }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($hasStudentLeave)
                                                    <div class="flex items-start gap-1.5">
                                                        <span class="mt-0.5 text-amber-500">⚠</span>
                                                        <div>
                                                            <span class="inline-block px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold mb-1">
                                                                Đã gửi đơn báo nghỉ
                                                            </span>
                                                            <p class="text-xs text-slate-500 max-w-xs">{{ $leaveReason }}</p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">Không có</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex justify-center gap-5">
                                                    @foreach(['Có mặt', 'Vắng', 'Muộn'] as $statusOption)
                                                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                            <input type="radio"
                                                                name="attendance[{{ $student->id }}]"
                                                                value="{{ $statusOption }}"
                                                                class="peer hidden"
                                                                {{ $current === $statusOption ? 'checked' : '' }}>
                                                            <span class="w-4 h-4 rounded-full border-2 border-slate-300 {{ $statusColors[$statusOption] }} transition"></span>
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
