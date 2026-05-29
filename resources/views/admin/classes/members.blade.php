<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.classes.index') }}" class="text-slate-400 hover:text-slate-600">← Quay lại</a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $class->class_name }}</h1>
                <p class="text-sm text-slate-500">{{ $class->room }} | {{ $class->start_time?->format('d/m/Y') }} — {{ $class->end_time?->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- GÁN GIÁO VIÊN --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-4">
                <h2 class="font-semibold text-slate-700 text-base">Giáo viên phụ trách</h2>

                @if($teacher)
                    <div class="flex items-center justify-between bg-blue-50 rounded-lg px-4 py-3">
                        <div>
                            <p class="font-medium text-slate-800">{{ $teacher->name }}</p>
                            <p class="text-xs text-slate-500">{{ $teacher->email }}</p>
                        </div>
                        <span class="text-xs text-blue-600 font-medium">Đang phụ trách</span>
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Chưa có giáo viên phụ trách.</p>
                @endif

                <form method="POST" action="{{ route('admin.classes.assign-teacher', $class) }}" class="flex gap-2">
                    @csrf
                    <select name="teacher_id" class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Chọn giáo viên --</option>
                        @foreach($allTeachers as $t)
                            <option value="{{ $t->id }}" @selected($teacher?->id === $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Gán
                    </button>
                </form>
            </div>

            {{-- THÊM HỌC VIÊN --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-slate-700 text-base">Học viên ({{ $students->count() }})</h2>
                    <button onclick="document.getElementById('modal-add-students').classList.remove('hidden')"
                            class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-emerald-700 transition">
                        + Thêm học viên
                    </button>
                </div>

                @if($students->isEmpty())
                    <p class="text-sm text-slate-400 italic">Chưa có học viên nào trong lớp.</p>
                @else
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($students as $student)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $student->name }}</p>
                                <p class="text-xs text-slate-500">{{ $student->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.classes.remove-student', [$class, $student]) }}"
                                  onsubmit="return confirm('Xóa {{ $student->name }} khỏi lớp?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Xóa</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL THÊM HỌC VIÊN --}}
    <div id="modal-add-students" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="font-semibold text-slate-800">Thêm học viên vào lớp</h3>
                <button onclick="document.getElementById('modal-add-students').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 text-xl font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.classes.add-students', $class) }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-4 border-b border-slate-100">
                    <input type="text" id="search-student" placeholder="Tìm theo tên hoặc email..."
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           oninput="filterStudents(this.value)">
                </div>

                <div id="student-list" class="overflow-y-auto flex-1 p-4 space-y-2">
                    @forelse($availableStudents as $s)
                    <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer student-item"
                           data-name="{{ strtolower($s->name) }}" data-email="{{ strtolower($s->email) }}">
                        <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="rounded text-emerald-600">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $s->name }}</p>
                            <p class="text-xs text-slate-500">{{ $s->email }}</p>
                        </div>
                    </label>
                    @empty
                    <p class="text-sm text-slate-400 italic text-center py-4">Không còn học viên nào để thêm.</p>
                    @endforelse
                </div>

                <div class="p-4 border-t border-slate-200 flex space-x-3">
                    <button type="submit" class="flex-1 bg-emerald-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                        Thêm vào lớp
                    </button>
                    <button type="button" onclick="document.getElementById('modal-add-students').classList.add('hidden')"
                            class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function filterStudents(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('.student-item').forEach(item => {
            const match = item.dataset.name.includes(q) || item.dataset.email.includes(q);
            item.style.display = match ? '' : 'none';
        });
    }
    </script>
</x-app-layout>
