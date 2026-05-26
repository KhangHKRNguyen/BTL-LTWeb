<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tạo và giao bài tập</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('teacher.assignments.store') }}" enctype="multipart/form-data" x-data="{ type: '{{ old('type', 'quiz') }}', questions: {{ max(count(old('questions', [['question_text' => '']])), 1) }} }" class="space-y-6">
                @csrf
                <input type="hidden" name="course_class_id" value="{{ $courseClass->id }}">

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
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">Lớp học</p>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $courseClass->class_name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $courseClass->students_count }} học viên</p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề bài tập</label>
                            <input id="title" name="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Loại bài tập</label>
                            <select id="type" name="type" x-model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="quiz">Trắc nghiệm</option>
                                <option value="essay">Tự luận</option>
                            </select>
                        </div>

                        <div>
                            <label for="open_time" class="block text-sm font-medium text-gray-700">Thời gian mở bài</label>
                            <input id="open_time" type="datetime-local" name="open_time" value="{{ old('open_time', now()->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="due_time" class="block text-sm font-medium text-gray-700">Hạn nộp</label>
                            <input id="due_time" type="datetime-local" name="due_time" value="{{ old('due_time') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="attachment" class="block text-sm font-medium text-gray-700">File đề bài đính kèm</label>
                            <input id="attachment" type="file" name="attachment" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200">
                        </div>

                        <div class="md:col-span-2">
                            <label for="content" class="block text-sm font-medium text-gray-700">Mô tả / Yêu cầu đề bài</label>
                            <textarea id="content" name="content" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content') }}</textarea>
                        </div>
                    </div>
                </div>

                <div x-show="type === 'quiz'" class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="font-semibold text-gray-900">Câu hỏi trắc nghiệm</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('teacher.assignments.template') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Tải file mẫu</a>
                            <label class="inline-flex cursor-pointer items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Import Excel/CSV
                                <input type="file" name="import_file" accept=".xlsx,.csv,.txt" class="sr-only">
                            </label>
                        </div>
                    </div>

                    <template x-for="index in questions" :key="index">
                        <div class="mt-6 rounded-md border border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gray-900">Câu hỏi <span x-text="index"></span></h4>
                                <button type="button" x-show="questions > 1" @click="questions--" class="text-sm text-red-600 hover:text-red-800">Xóa</button>
                            </div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Nội dung câu hỏi</label>
                                    <textarea :name="`questions[${index - 1}][question_text]`" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án A</label>
                                    <input :name="`questions[${index - 1}][option_a]`" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án B</label>
                                    <input :name="`questions[${index - 1}][option_b]`" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án C</label>
                                    <input :name="`questions[${index - 1}][option_c]`" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án D</label>
                                    <input :name="`questions[${index - 1}][option_d]`" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án đúng</label>
                                    <select :name="`questions[${index - 1}][correct_option]`" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="questions++" class="mt-5 rounded-md border border-indigo-300 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50">
                        Thêm câu hỏi
                    </button>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('teacher.assignments.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</a>
                    <button class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Giao bài tập</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
