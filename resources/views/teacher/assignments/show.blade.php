<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('teacher.assignments.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <svg class="-ml-1 mr-1.5 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Quay lại
                </a>
                <h2 class="text-xl font-semibold text-gray-800">Chi tiết bài tập</h2>
            </div>
            <a href="{{ route('teacher.grades.submissions', $assignment) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 shadow-sm transition">
                Xem bài nộp
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ $assignment->courseClass->class_name }}</p>
                        <h3 class="mt-1 text-xl font-semibold text-gray-900">{{ $assignment->title }}</h3>
                    </div>
                    <span class="inline-flex w-fit rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">{{ $assignment->typeLabel() }}</span>
                </div>

                <dl class="mt-6 grid gap-4 text-sm md:grid-cols-3">
                    <div>
                        <dt class="text-gray-500">Thời gian mở</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ optional($assignment->open_time)->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Hạn nộp</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ optional($assignment->due_time)->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Bài đã nộp</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $assignment->submissions_count }}</dd>
                    </div>
                </dl>

                @if ($assignment->content)
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900">Yêu cầu</h4>
                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $assignment->content }}</p>
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap gap-4 border-t border-slate-100 pt-6">
                    @if ($assignment->file_path)
                        <a href="{{ route('teacher.assignments.download-attachment', $assignment) }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-50 border border-indigo-200 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 hover:text-indigo-800 transition shadow-sm">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Tải file đề bài đính kèm
                        </a>
                    @endif

                    @if ($assignment->isQuiz())
                        <a href="{{ route('teacher.assignments.export', $assignment) }}" download class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 transition shadow-sm">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            Xuất danh sách câu hỏi (Excel/CSV)
                        </a>
                    @endif
                </div>
            </div>

            @if ($assignment->isQuiz())
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-gray-900">Danh sách câu hỏi</h3>
                    <div class="mt-4 space-y-4">
                        @foreach ($assignment->questions as $question)
                            <div class="rounded-md border border-gray-200 p-4">
                                <p class="font-medium text-gray-900">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                                <div class="mt-3 grid gap-2 text-sm text-gray-700 md:grid-cols-2">
                                    <p>A. {{ $question->option_a }}</p>
                                    <p>B. {{ $question->option_b }}</p>
                                    <p>C. {{ $question->option_c }}</p>
                                    <p>D. {{ $question->option_d }}</p>
                                </div>
                                <p class="mt-3 text-sm font-medium text-green-700">Đáp án đúng: {{ $question->correct_option }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
