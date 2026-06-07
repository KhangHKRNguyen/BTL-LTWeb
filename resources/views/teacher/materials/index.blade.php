<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Tiêu đề --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản lý tài liệu học tập</h1>
            <p class="text-sm text-slate-500 mt-1">Upload và quản lý tài liệu PDF, Word, PowerPoint, Excel cho từng lớp học.</p>
        </div>

        {{-- Form bộ lọc --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form method="GET" action="{{ route('teacher.materials.index') }}" class="flex flex-wrap gap-4 items-end">
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

                @if($selectedClass)
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tìm theo tên</label>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Nhập tên tài liệu..."
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Loại file</label>
                    <select name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Tất cả --</option>
                        <option value="pdf"   {{ $filterType === 'pdf'   ? 'selected' : '' }}>PDF</option>
                        <option value="word"  {{ $filterType === 'word'  ? 'selected' : '' }}>Word (doc/docx)</option>
                        <option value="ppt"   {{ $filterType === 'ppt'   ? 'selected' : '' }}>PowerPoint</option>
                        <option value="excel" {{ $filterType === 'excel' ? 'selected' : '' }}>Excel</option>
                    </select>
                </div>
                @endif

                <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    {{ $selectedClass ? 'Lọc' : 'Xem tài liệu' }}
                </button>

                @if($selectedClass && ($search || $filterType))
                <a href="{{ route('teacher.materials.index', ['class_id' => request('class_id')]) }}"
                    class="px-4 py-2 text-sm text-slate-500 border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                    Xoá bộ lọc
                </a>
                @endif
            </form>
        </div>

        @if(!$selectedClass)
            {{-- Empty state --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 py-20 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-slate-600 font-semibold">Chọn lớp để xem và quản lý tài liệu</p>
                <p class="text-slate-400 text-sm mt-1 max-w-xs">Danh sách tài liệu và form upload sẽ hiện ra sau khi bạn chọn lớp học</p>
            </div>
        @else
            {{-- Thống kê theo loại file --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-slate-700">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Tổng</p>
                </div>
                <a href="{{ route('teacher.materials.index', ['class_id' => request('class_id'), 'type' => 'pdf']) }}"
                    class="bg-white rounded-xl border {{ $filterType === 'pdf' ? 'border-rose-400 ring-1 ring-rose-300' : 'border-rose-200' }} shadow-sm p-4 text-center hover:border-rose-400 transition cursor-pointer">
                    <p class="text-2xl font-bold text-rose-600">{{ $stats['pdf'] }}</p>
                    <p class="text-xs text-rose-400 mt-0.5">PDF</p>
                </a>
                <a href="{{ route('teacher.materials.index', ['class_id' => request('class_id'), 'type' => 'word']) }}"
                    class="bg-white rounded-xl border {{ $filterType === 'word' ? 'border-blue-400 ring-1 ring-blue-300' : 'border-blue-200' }} shadow-sm p-4 text-center hover:border-blue-400 transition cursor-pointer">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['word'] }}</p>
                    <p class="text-xs text-blue-400 mt-0.5">Word</p>
                </a>
                <a href="{{ route('teacher.materials.index', ['class_id' => request('class_id'), 'type' => 'ppt']) }}"
                    class="bg-white rounded-xl border {{ $filterType === 'ppt' ? 'border-orange-400 ring-1 ring-orange-300' : 'border-orange-200' }} shadow-sm p-4 text-center hover:border-orange-400 transition cursor-pointer">
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['ppt'] }}</p>
                    <p class="text-xs text-orange-400 mt-0.5">PowerPoint</p>
                </a>
                <a href="{{ route('teacher.materials.index', ['class_id' => request('class_id'), 'type' => 'excel']) }}"
                    class="bg-white rounded-xl border {{ $filterType === 'excel' ? 'border-emerald-400 ring-1 ring-emerald-300' : 'border-emerald-200' }} shadow-sm p-4 text-center hover:border-emerald-400 transition cursor-pointer">
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['excel'] }}</p>
                    <p class="text-xs text-emerald-400 mt-0.5">Excel</p>
                </a>
            </div>

            {{-- Form upload --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-4">
                    Upload tài liệu cho lớp: <span class="text-blue-600">{{ $selectedClass->class_name }}</span>
                </h2>
                <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                    <div class="grid sm:grid-cols-2 gap-4">
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
                                Chọn file <span class="text-slate-400 font-normal">(PDF, Word, PPT, Excel — tối đa 20MB)</span>
                            </label>
                            <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                                class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('file') border border-rose-400 rounded-lg @enderror">
                            @error('file')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
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
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-base font-semibold text-slate-700">
                        Danh sách tài liệu
                        <span class="text-slate-400 font-normal text-sm">({{ $materials->total() }} kết quả)</span>
                    </h2>
                    @if($search || $filterType)
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            @if($search)
                                <span class="px-2 py-1 bg-slate-100 rounded-full">Tên: "{{ $search }}"</span>
                            @endif
                            @if($filterType)
                                <span class="px-2 py-1 bg-slate-100 rounded-full">Loại: {{ strtoupper($filterType) }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                @if($materials->isEmpty())
                    <div class="py-12 flex flex-col items-center text-center">
                        <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-slate-500 text-sm font-medium">
                            {{ ($search || $filterType) ? 'Không tìm thấy tài liệu phù hợp' : 'Chưa có tài liệu nào cho lớp này' }}
                        </p>
                        @if($search || $filterType)
                            <a href="{{ route('teacher.materials.index', ['class_id' => request('class_id')]) }}"
                                class="text-blue-600 text-xs mt-1 hover:underline">Xem tất cả tài liệu</a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-slate-600 text-left">
                                    <th class="px-4 py-3 font-semibold rounded-tl-lg">#</th>
                                    <th class="px-4 py-3 font-semibold">Tiêu đề</th>
                                    <th class="px-4 py-3 font-semibold">Loại</th>
                                    <th class="px-4 py-3 font-semibold">Kích thước</th>
                                    <th class="px-4 py-3 font-semibold">Ngày tải lên</th>
                                    <th class="px-4 py-3 font-semibold text-center rounded-tr-lg">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($materials as $material)
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

                                        $fileSize = '—';
                                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($material->file_path)) {
                                            $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($material->file_path);
                                            $fileSize = $bytes < 1048576
                                                ? round($bytes / 1024, 1) . ' KB'
                                                : round($bytes / 1048576, 1) . ' MB';
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-slate-400">{{ $materials->firstItem() + $loop->index }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-800">{{ $material->title }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase {{ $badgeClass }}">
                                                {{ $ext }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $fileSize }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $material->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('teacher.materials.download', $material->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">
                                                    Tải xuống
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('teacher.materials.destroy', $material->id) }}"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu «{{ addslashes($material->title) }}»?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-medium transition">
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

                    @if($materials->hasPages())
                        <div class="mt-5 border-t border-slate-100 pt-4">
                            {{ $materials->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
