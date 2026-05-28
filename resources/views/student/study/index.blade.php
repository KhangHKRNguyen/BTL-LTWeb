<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6"> 
            
            <div class="w-full md:w-64 flex-shrink-0">
                <x-student-sidebar /> 
            </div>

            <div class="flex-1 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="container mt-2">
                    
                    <div class="pb-4 mb-6 border-b border-gray-100">
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="me-2">📚</span> Tài liệu học tập
                        </h1>
                    </div>

                    <div class="space-y-6">
                        @forelse($classes as $class)
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                
                                <div class="px-5 py-4 bg-slate-800 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                                    <div>
                                        <h5 class="font-bold text-base tracking-tight text-white">{{ $class->class_name }}</h5>
                                        <p class="text-xs text-slate-400 mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <span>🚪 Phòng: <span class="text-cyan-400 font-medium">{{ $class->room ?? 'N/A' }}</span></span>
                                            <span class="text-slate-500">|</span>
                                            <span>📅 Thời gian: {{ $class->start_time?->format('d/m/Y') ?? 'N/A' }} - {{ $class->end_time?->format('d/m/Y') ?? 'N/A' }}</span>
                                        </p>
                                    </div>
                                    <span class="px-2.5 py-0.5 bg-slate-700 text-slate-300 text-[11px] font-semibold rounded-md border border-slate-600 uppercase">
                                        Lớp học phần
                                    </span>
                                </div>
                                
                                <div class="p-5">
                                    @if($class->materials->count() > 0)
                                        <div class="overflow-x-auto rounded-lg border border-gray-100 shadow-sm">
                                            <table class="w-full text-left border-collapse text-sm text-gray-600">
                                                <thead class="bg-slate-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase">
                                                    <tr>
                                                        <th class="px-4 py-3 font-bold">Tên tài liệu / Bài giảng</th>
                                                        <th class="px-4 py-3 text-end font-bold">Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 bg-white">
                                                    @foreach($class->materials as $material)
                                                        <tr class="hover:bg-slate-50/80 transition duration-150">
                                                            <td class="px-4 py-3.5 font-medium text-gray-800 flex items-center gap-2.5">
                                                                <span class="text-base">picture_as_pdf</span>
                                                                <span class="truncate max-w-md md:max-w-xl" title="{{ $material->title }}">
                                                                    {{ $material->title }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3.5 text-end whitespace-nowrap">
                                                                <a href="{{ route('student.study.download', $material->id) }}" class="inline-block">
                                                                    <x-primary-button type="button" class="py-1.5 px-3 text-[11px] bg-blue-600 hover:bg-blue-700 active:bg-blue-800" title="Tải xuống tài liệu">
                                                                        ⬇️ Tải xuống
                                                                    </x-primary-button>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="py-6 text-center bg-slate-50 rounded-lg border border-dashed border-gray-200">
                                            <p class="text-sm text-gray-400 flex items-center justify-center gap-1.5">
                                                📥 Hiện lớp học này chưa được giảng viên tải lên tài liệu nào.
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-xl bg-slate-50">
                                <div class="text-4xl mb-2">🏫</div>
                                <h3 class="text-sm font-semibold text-gray-700">Chưa tham gia lớp học nào</h3>
                                <p class="text-xs text-gray-400 mt-1">Dữ liệu thời khóa biểu và tài liệu môn học của bạn sẽ hiển thị tự động khi có lớp học phần chính thức.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div> </div> 
    </div>
</x-app-layout>