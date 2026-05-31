<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Tiêu đề --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản lý tài liệu học tập</h1>
            <p class="text-sm text-slate-500 mt-1">Upload tài liệu PDF, Word cho từng lớp học.</p>
        </div>

        {{-- Form chọn lớp --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form method="GET" action="{{ route('teacher.materials.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
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
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Xem tài liệu
                </button>
            </form>
        </div>

        @if($selectedClass)
            {{-- Form upload tài liệu --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-4">
                    Upload tài liệu cho lớp: <span class="text-blue-600">{{ $selectedClass->class_name }}</span>
                </h2>

                <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tiêu đề tài liệu</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Ví dụ: Slide Chương 1"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-rose-400 @enderror">
                        @error('title')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Chọn file <span class="text-slate-400 font-normal">(PDF, Word, PowerPoint, Excel - tối đa 20MB)</span>
                        </label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                            class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('file') border border-rose-400 rounded-lg @enderror">
                        @error('file')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                            Tải lên tài liệu
                        </button>
                    </div>
                </form>
            </div>

            {{-- Danh sách tài liệu --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-4">
                    Danh sách tài liệu ({{ $materials->count() }} tài liệu)
                </h2>

                @if($materials->isEmpty())
                    <p class="text-slate-500 text-sm">Chưa có tài liệu nào cho lớp này.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-slate-600 text-left">
                                    <th class="px-4 py-3 font-semibold rounded-tl-lg">#</th>
                                    <th class="px-4 py-3 font-semibold">Tiêu đề</th>
                                    <th class="px-4 py-3 font-semibold">Loại file</th>
                                    <th class="px-4 py-3 font-semibold">Ngày tải lên</th>
                                    <th class="px-4 py-3 font-semibold text-center rounded-tr-lg">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($materials as $i => $material)
                                    @php
                                        $ext = strtolower(pathinfo($material->file_path, PATHINFO_EXTENSION));
                                        $extColors = [
                                            'pdf'  => 'bg-rose-100 text-rose-700',
                                            'doc'  => 'bg-blue-100 text-blue-700',
                                            'docx' => 'bg-blue-100 text-blue-700',
                                            'ppt'  => 'bg-orange-100 text-orange-700',
                                            'pptx' => 'bg-orange-100 text-orange-700',
                                            'xls'  => 'bg-emerald-100 text-emerald-700',
                                            'xlsx' => 'bg-emerald-100 text-emerald-700',
                                        ];
                                        $badgeClass = $extColors[$ext] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-800">{{ $material->title }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase {{ $badgeClass }}">
                                                {{ $ext }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-500">
                                            {{ $material->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('teacher.materials.download', $material->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">
                                                    Tải xuống
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('teacher.materials.destroy', $material->id) }}"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-rose-500 hover:text-rose-700 text-xs font-medium transition">
                                                        Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
