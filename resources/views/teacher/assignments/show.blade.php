<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Chi tiết bài tập</h2>
            <a href="{{ route('teacher.grades.submissions', $assignment) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
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

                @if ($assignment->file_path)
                    <div class="mt-6">
                        <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Xem file đề bài</a>
                    </div>
                @endif
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
