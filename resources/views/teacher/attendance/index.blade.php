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

        @if(!$selectedClass)
            {{-- Empty state --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 py-20 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-slate-600 font-semibold">Chọn lớp và ngày để bắt đầu điểm danh</p>
                <p class="text-slate-400 text-sm mt-1 max-w-xs">Danh sách học viên và trạng thái điểm danh sẽ hiện ra sau khi bạn chọn lớp học</p>
            </div>
        @else
            {{-- Thống kê nhanh --}}
            @php
                $totalCount   = $students->count();
                $presentCount = 0;
                $absentCount  = 0;
                $lateCount    = 0;
                foreach ($students as $_s) {
                    $_hasLeave = !is_null($leaveRequests->get($_s->id));
                    $_default  = $_hasLeave ? 'Vắng' : 'Có mặt';
                    $_status   = $existingAttendances->get($_s->id, $_default);
                    if ($_status === 'Có mặt') $presentCount++;
                    elseif ($_status === 'Vắng') $absentCount++;
                    elseif ($_status === 'Muộn') $lateCount++;
                }
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-slate-700">{{ $totalCount }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Tổng học viên</p>
                </div>
                <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $presentCount }}</p>
                    <p class="text-xs text-emerald-400 mt-0.5">Có mặt</p>
                </div>
                <div class="bg-white rounded-xl border border-rose-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-rose-600">{{ $absentCount }}</p>
                    <p class="text-xs text-rose-400 mt-0.5">Vắng</p>
                </div>
                <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $lateCount }}</p>
                    <p class="text-xs text-amber-400 mt-0.5">Muộn</p>
                </div>
            </div>

            {{-- Bảng điểm danh --}}
            @php $hasLeave = $leaveRequests->isNotEmpty(); @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <h2 class="text-base font-semibold text-slate-700">
                        Lớp: <span class="text-blue-600">{{ $selectedClass->class_name }}</span>
                        &mdash; Ngày: <span class="text-blue-600">{{ \Carbon\Carbon::parse($attendanceDate)->format('d/m/Y') }}</span>
                    </h2>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($hasLeave)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                                ⚠ Có {{ $leaveRequests->count() }} đơn báo nghỉ hôm nay
                            </span>
                        @endif
                        <button type="button" id="btnSelectAll"
                            class="px-3 py-1.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                            ✓ Tất cả có mặt
                        </button>
                    </div>
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
                                            $leaveReason     = $leaveRequests->get($student->id);
                                            $hasStudentLeave = !is_null($leaveReason);
                                            $defaultStatus   = $hasStudentLeave ? 'Vắng' : 'Có mặt';
                                            $current         = $existingAttendances->get($student->id, $defaultStatus);
                                            $statusColors    = [
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

    <script>
        document.getElementById('btnSelectAll')?.addEventListener('click', function () {
            document.querySelectorAll('input[type="radio"][value="Có mặt"]').forEach(r => r.checked = true);
        });
    </script>
</x-app-layout>
