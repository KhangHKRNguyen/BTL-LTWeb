<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div>
            <a href="{{ route('student.results.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                &larr; Quay lại danh sách điểm
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Thông tin bài làm</h2>
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">{{ $submission->assignment->title }}</h1>
                        <p class="text-sm text-slate-500 mt-1">{{ $submission->assignment->description }}</p>
                    </div>

                    <div class="border-t border-slate-100 pt-4 text-center">
                        <span class="text-xs font-semibold text-slate-400 uppercase block mb-1">Điểm số đạt được</span>
                        <div class="text-4xl font-black text-blue-600">{{ $submission->grade }} <span class="text-lg text-slate-400 font-normal">/ 10</span></div>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <span class="text-xs font-semibold text-slate-400 uppercase block mb-1">Lời phê của Giáo viên</span>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 italic">
                            "{{ $submission->teacher_comment ?? 'Không có lời phê cụ thể.' }}"
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col h-[500px]">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-xl flex justify-between items-center">
                        <h3 class="font-bold text-slate-700 flex items-center space-x-2">
                            <span>Khung trao đổi thắc mắc điểm số</span>
                        </h3>
                        <span class="text-xs text-slate-400">Học viên và Giáo viên</span>
                    </div>

                    <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
                        @forelse($submission->feedbacks as $feedback)
                            <div class="flex flex-col {{ $feedback->user_id == auth()->id() ? 'items-end' : 'items-start' }}">
                                <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm shadow-sm
                                    {{ $feedback->user_id == auth()->id() 
                                        ? 'bg-blue-600 text-white rounded-tr-none' 
                                        : 'bg-white text-slate-800 border border-slate-200 rounded-tl-none' }}">
                                    <p class="font-semibold text-xs mb-1 opacity-75">
                                        {{ $feedback->user->name }} ({{ $feedback->user->role == 'student' ? 'Học viên' : 'Giáo viên' }})
                                    </p>
                                    <p class="break-words">{{ $feedback->feedback_content }}</p>
                                    
                                    @if($feedback->old_score !== null && $feedback->new_score !== null)
                                        <div class="mt-2 text-xs p-1.5 rounded bg-amber-500/20 text-amber-900 border border-amber-500/30">
                                            Hệ thống: Cập nhật điểm ({{ $feedback->old_grade }} &rarr; {{ $feedback->new_grade }})
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 px-1">
                                    {{ $feedback->created_at->format('H:i d/m/Y') }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center text-slate-400 text-sm py-12">
                                Chưa có ý kiến phản hồi nào. Nếu bạn thắc mắc về điểm số hoặc lời phê, hãy gửi tin nhắn ở form bên dưới.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-4 border-t border-slate-200 bg-white rounded-b-xl">
                        <form action="{{ route('student.feedback.store') }}" method="POST" class="flex space-x-2">
                            @csrf
                            <input type="hidden" name="submission_id" value="{{ $submission->id }}">
                            <input type="text" name="feedback_content" required placeholder="Gõ thắc mắc của bạn về bài chấm này..." 
                                   class="flex-1 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-5 py-2.5 rounded-xl transition shadow-sm">
                                Gửi đi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>