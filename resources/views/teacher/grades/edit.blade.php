<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Chấm bài</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $submission->student->name }} · {{ $submission->assignment->title }}</p>
            </div>
            <a href="{{ route('teacher.grades.submissions', $submission->assignment) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Quay lại</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <div class="font-medium">Dữ liệu chưa hợp lệ.</div>
                    <ul class="mt-2 list-disc space-y-1 ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="grid gap-4 text-sm md:grid-cols-4">
                    <div>
                        <p class="text-gray-500">Học viên</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $submission->student->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Thời gian nộp</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $submission->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Trạng thái</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $submission->isGraded() ? 'Đã chấm' : 'Đã nộp' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Điểm hiện tại</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $submission->grade !== null ? number_format((float) $submission->grade, 2) : '-' }}</p>
                    </div>
                </div>
            </div>

            @if ($submission->assignment->isQuiz())
                @php
                    $answers = $submission->answers->keyBy('question_id');
                @endphp
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Chi tiết trắc nghiệm</h3>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">
                            Điểm tự chấm: {{ number_format((float) $submission->grade, 2) }}
                        </span>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Câu hỏi</th>
                                    <th class="px-4 py-3">Học viên chọn</th>
                                    <th class="px-4 py-3">Đáp án đúng</th>
                                    <th class="px-4 py-3">Kết quả</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($submission->assignment->questions as $question)
                                    @php
                                        $answer = $answers->get($question->id);
                                        $isCorrect = $answer && mb_strtoupper($answer->selected_option) === mb_strtoupper($question->correct_option);
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-gray-900">{{ $loop->iteration }}. {{ $question->question_text }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $answer?->selected_option ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $question->correct_option }}</td>
                                        <td class="px-4 py-3">
                                            <span class="rounded-full {{ $isCorrect ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} px-3 py-1 text-xs font-medium">
                                                {{ $isCorrect ? 'Đúng' : 'Sai' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-gray-900">Bài làm tự luận</h3>
                    @if ($submission->submission_content)
                        <p class="mt-3 whitespace-pre-line text-sm text-gray-700">{{ $submission->submission_content }}</p>
                    @endif

                    @if ($submission->file_path)
                        <a href="{{ Storage::url($submission->file_path) }}" download class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-50 border border-indigo-200 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 hover:text-indigo-800 transition-all duration-200 shadow-sm">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Tải về file bài làm (PDF)
                        </a>
                    @endif
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Cột trái: Form chấm điểm (Chiếm 2/3 màn hình) -->
                <div class="lg:col-span-2 space-y-6">
                    <form method="POST" action="{{ route('teacher.grades.update', $submission) }}" class="bg-white p-6 shadow-sm sm:rounded-lg border border-slate-200">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-5 md:grid-cols-3">
                            <div>
                                <label for="grade" class="block text-sm font-semibold text-gray-700">Điểm số</label>
                                <input id="grade" type="number" name="grade" min="0" max="10" step="0.01" value="{{ old('grade', $submission->grade) }}" @if ($submission->assignment->isQuiz()) readonly @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if ($submission->assignment->isQuiz()) bg-gray-100 @endif">
                            </div>
                            <div class="md:col-span-3">
                                <label for="teacher_comment" class="block text-sm font-semibold text-gray-700">Nhận xét</label>
                                <textarea id="teacher_comment" name="teacher_comment" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nhập nhận xét chi tiết của giáo viên tại đây...">{{ old('teacher_comment', $submission->teacher_comment) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-sm">Hoàn tất chấm bài</button>
                        </div>
                    </form>
                </div>

                <!-- Cột phải: Khung trao đổi thắc mắc điểm số (Chiếm 1/3 màn hình) -->
                <div class="lg:col-span-1">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col h-[500px]">
                        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 rounded-t-xl flex justify-between items-center">
                            <h3 class="font-bold text-slate-700 text-sm">Trao đổi thắc mắc điểm số</h3>
                            <span class="text-[10px] text-slate-400">Học viên & Giáo viên</span>
                        </div>

                        <!-- Danh sách tin nhắn thắc mắc -->
                        <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/50">
                            @forelse($submission->feedbacks as $feedback)
                                <div class="flex flex-col {{ $feedback->user_id == auth()->id() ? 'items-end' : 'items-start' }}">
                                    <div class="max-w-[85%] rounded-2xl px-3 py-2 text-xs shadow-sm
                                        {{ $feedback->user_id == auth()->id() 
                                            ? 'bg-indigo-600 text-white rounded-tr-none' 
                                            : 'bg-white text-slate-800 border border-slate-200 rounded-tl-none' }}">
                                        <p class="font-bold text-[10px] mb-0.5 opacity-75">
                                            {{ $feedback->user->name }} ({{ $feedback->user->role == 'student' ? 'Học viên' : 'Giáo viên' }})
                                        </p>
                                        <p class="break-words font-medium">{{ $feedback->feedback_content }}</p>
                                        
                                        @if($feedback->old_grade !== null && $feedback->new_grade !== null)
                                            <div class="mt-1.5 text-[10px] p-1 rounded bg-green-500/80 text-white border border-green-400 font-semibold text-center">
                                                Hệ thống: Cập nhật điểm ({{ $feedback->old_grade }} &rarr; {{ $feedback->new_grade }})
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-[9px] text-slate-400 mt-0.5 px-1">
                                        {{ $feedback->created_at->format('H:i d/m/Y') }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-slate-400 text-xs py-16">
                                    Chưa có tin nhắn trao đổi nào cho bài chấm này.
                                </div>
                            @endforelse
                        </div>

                        <!-- Form gửi phản hồi từ giáo viên -->
                        <div class="p-3 border-t border-slate-200 bg-white rounded-b-xl">
                            <form action="{{ route('teacher.grades.feedback', $submission) }}" method="POST" class="flex space-x-2">
                                @csrf
                                <input type="text" name="feedback_content" required placeholder="Gõ phản hồi tới học viên..." 
                                       class="flex-1 border border-slate-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500 transition">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                    Gửi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
