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
                            <span class="me-2">📝</span> Danh sách bài tập
                        </h1>
                    </div>

                    @if(session('success'))
                        <x-auth-session-status :status="session('success')" class="mb-4 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg shadow-sm font-medium" />
                    @endif

                    <div class="space-y-4">
                        @forelse($assignments as $assignment)
                            @php
                                $userSubmission = $assignment->submissions->first();
                                $status = $userSubmission?->status ?? 'not-started';
                                $dueTime = \Carbon\Carbon::parse($assignment->due_time);
                                $isOverdue = now() > $dueTime;
                                
                                // Thiết lập màu sắc đường viền bên trái dựa trên trạng thái quá hạn
                                $borderColor = ($isOverdue && $status !== 'submitted') ? 'border-l-red-500' : 'border-l-blue-500';
                            @endphp

                            <div class="p-5 bg-white border border-gray-200 hover:border-blue-200 rounded-xl shadow-sm transition-all duration-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 !{{ $borderColor }}">
                                
                                <div class="space-y-1.5 flex-1">
                                    <h5 class="text-base font-bold text-gray-800">{{ $assignment->title }}</h5>
                                    
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><span class="font-medium text-gray-400">🏫 Lớp:</span> <span class="text-gray-800 font-medium">{{ $assignment->courseClass->class_name }}</span></p>
                                        
                                        <p class="flex items-center gap-2">
                                            <span class="font-medium text-gray-400">🏷️ Loại:</span> 
                                            <span class="px-2 py-0.5 bg-cyan-50 text-cyan-700 text-xs font-semibold rounded border border-cyan-100">
                                                {{ $assignment->type }}
                                            </span>
                                        </p>
                                        
                                        <p class="flex items-center gap-1.5">
                                            <span class="font-medium text-gray-400">⏳ Hạn nộp:</span> 
                                            <span class="{{ ($isOverdue && $status !== 'submitted') ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                                {{ $dueTime->format('d/m/Y H:i') }}
                                            </span>
                                            @if($isOverdue && $status !== 'submitted')
                                                <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-bold rounded border border-red-100 uppercase">
                                                    Quá hạn
                                                </span>
                                            @endif
                                        </p>
                                    </div>

                                    @if($assignment->content)
                                        <div class="text-xs text-gray-400 pt-2 border-t border-gray-100 mt-2">
                                            <span class="font-medium text-gray-500">📖 Mô tả:</span> {{ Str::limit($assignment->content, 100) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex flex-col items-start md:items-end gap-2 w-full md:w-auto">
                                    @if(!$userSubmission)
                                        @if(!$isOverdue)
                                            <a href="{{ route('student.assignments.show', $assignment->id) }}" class="w-full md:w-auto">
                                                <x-primary-button type="button" class="w-full justify-center py-2 px-4 text-xs">
                                                    Làm bài
                                                </x-primary-button>
                                            </a>
                                        @else
                                            <button class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-400 uppercase tracking-widest shadow-sm cursor-not-allowed" disabled>
                                                Quá hạn
                                            </button>
                                        @endif
                                    @else
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-md border border-emerald-200">
                                                ✓ Đã nộp
                                            </span>
                                            @if($userSubmission->grade !== null)
                                                <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-md border border-indigo-200">
                                                    Điểm: {{ $userSubmission->grade }}/10
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <a href="{{ route('student.assignments.show', $assignment->id) }}" class="w-full md:w-auto">
                                            <x-secondary-button type="button" class="w-full justify-center py-1.5 text-[11px]">
                                                Xem chi tiết
                                            </x-secondary-button>
                                        </a>
                                    @endif
                                </div>

                            </div>
                        @empty
                            <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-xl bg-slate-50">
                                <div class="text-4xl mb-2">🎉</div>
                                <h3 class="text-sm font-semibold text-gray-700">Tuyệt vời! Không có bài tập</h3>
                                <p class="text-xs text-gray-400 mt-1">Hiện tại bạn đã hoàn thành hoặc không có bài tập nào được giao từ giáo viên.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div> </div> 
    </div>
</x-app-layout>