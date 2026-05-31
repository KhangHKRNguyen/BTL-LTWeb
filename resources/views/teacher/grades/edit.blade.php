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
                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="mt-4 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-800">Xem/tải file bài làm</a>
                    @endif
                </div>
            @endif

            {{-- Thắc mắc của học viên & Phản hồi giáo viên --}}
            @if($submission->feedbacks->isNotEmpty())
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-4">💬 Thắc mắc của học viên ({{ $submission->feedbacks->count() }})</h3>
                <div class="space-y-4">
                    @foreach($submission->feedbacks as $feedback)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $feedback->teacher_reply ? 'bg-gray-50' : 'bg-amber-50 border-amber-200' }}">
                        {{-- Học viên hỏi --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-sm shrink-0">
                                {{ mb_substr($feedback->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-800">{{ $feedback->user->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $feedback->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $feedback->feedback_content }}</p>
                            </div>
                        </div>

                        {{-- Giáo viên phản hồi --}}
                        @if($feedback->teacher_reply)
                            <div class="ml-11 pl-4 border-l-2 border-indigo-300">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-indigo-700">Giáo viên phản hồi</span>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($feedback->teacher_replied_at)->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $feedback->teacher_reply }}</p>
                                {{-- Cho phép sửa phản hồi --}}
                                <form method="POST" action="{{ route('teacher.grades.feedback.reply', $feedback) }}" class="mt-2 flex gap-2">
                                    @csrf
                                    <input type="text" name="teacher_reply" value="{{ $feedback->teacher_reply }}"
                                        class="flex-1 text-sm rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                                    <button type="submit" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                                        Cập nhật
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="ml-11">
                                <form method="POST" action="{{ route('teacher.grades.feedback.reply', $feedback) }}" class="flex gap-2 items-end">
                                    @csrf
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-500 mb-1">Nhập phản hồi của bạn:</label>
                                        <textarea name="teacher_reply" rows="2"
                                            class="w-full text-sm rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-400"
                                            placeholder="Nhập phản hồi cho học viên..."></textarea>
                                        @error('teacher_reply')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition mb-0.5">
                                        Gửi
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('teacher.grades.update', $submission) }}" class="bg-white p-6 shadow-sm sm:rounded-lg">
                @csrf
                @method('PATCH')

                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label for="grade" class="block text-sm font-medium text-gray-700">Điểm số</label>
                        <input id="grade" type="number" name="grade" min="0" max="10" step="0.01" value="{{ old('grade', $submission->grade) }}" @if ($submission->assignment->isQuiz()) readonly @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if ($submission->assignment->isQuiz()) bg-gray-100 @endif">
                    </div>
                    <div class="md:col-span-3">
                        <label for="teacher_comment" class="block text-sm font-medium text-gray-700">Nhận xét</label>
                        <textarea id="teacher_comment" name="teacher_comment" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('teacher_comment', $submission->teacher_comment) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Hoàn tất chấm bài</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
