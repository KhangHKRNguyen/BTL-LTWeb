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
                <h2 class="text-xl font-semibold text-gray-800">Tạo và giao bài tập</h2>
            </div>
        </div>
    </x-slot>

    @php
        $oldQuestions = old('questions');
        if (!$oldQuestions) {
            $oldQuestions = [['question_text' => '', 'option_a' => '', 'option_b' => '', 'option_c' => '', 'option_d' => '', 'correct_option' => 'A']];
        } else {
            $oldQuestions = array_map(function($q) {
                return [
                    'question_text' => $q['question_text'] ?? '',
                    'option_a' => $q['option_a'] ?? '',
                    'option_b' => $q['option_b'] ?? '',
                    'option_c' => $q['option_c'] ?? '',
                    'option_d' => $q['option_d'] ?? '',
                    'correct_option' => $q['correct_option'] ?? 'A',
                ];
            }, array_values($oldQuestions));
        }
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('teacher.assignments.store') }}" enctype="multipart/form-data" x-data="{
                type: '{{ old('type', 'quiz') }}',
                questions: {{ json_encode($oldQuestions) }},
                attachmentName: '',
                toast: { show: false, message: '', type: 'success' },
                showToast(message, type = 'success') {
                    this.toast.show = true;
                    this.toast.message = message;
                    this.toast.type = type;
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },
                async importExcel(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const formData = new FormData();
                    formData.append('import_file', file);
                    
                    try {
                        const response = await fetch('{{ route('teacher.assignments.parse-import') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        
                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.questions = data.questions;
                            this.showToast('Import câu hỏi thành công! Đã nạp ' + data.questions.length + ' câu hỏi vào giao diện.', 'success');
                        } else {
                            this.showToast(data.message || 'Sai định dạng file Excel/CSV. Vui lòng kiểm tra lại cấu trúc file mẫu.', 'error');
                        }
                    } catch (error) {
                        console.error(error);
                        this.showToast('Có lỗi xảy ra khi kết nối máy chủ.', 'error');
                    }
                    
                    event.target.value = '';
                }
            }" class="space-y-6">
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
                            <div class="mt-1.5 flex flex-wrap items-center gap-3">
                                <label class="inline-flex cursor-pointer items-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Chọn tệp đính kèm
                                    <input id="attachment" type="file" name="attachment" @change="attachmentName = $event.target.files[0]?.name || ''" class="sr-only">
                                </label>
                                <span class="text-sm font-medium text-gray-500 bg-gray-50 border border-gray-200 px-3 py-2 rounded-xl" x-text="attachmentName || 'Chưa chọn file đề bài'"></span>
                                <button type="button" x-show="attachmentName" @click="document.getElementById('attachment').value = ''; attachmentName = ''" class="rounded-xl bg-rose-50 border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 shadow-sm hover:bg-rose-100 hover:text-rose-700 transition" style="display: none;">
                                    Xóa file
                                </button>
                            </div>
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
                            <a href="{{ route('teacher.assignments.template') }}" download class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Tải file mẫu</a>
                            <label class="inline-flex cursor-pointer items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Import Excel/CSV
                                <input type="file" name="import_file" accept=".xlsx,.csv,.txt" @change="importExcel($event)" class="sr-only">
                            </label>
                        </div>
                    </div>

                    <template x-for="(question, idx) in questions" :key="idx">
                        <div class="mt-6 rounded-md border border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gray-900">Câu hỏi <span x-text="idx + 1"></span></h4>
                                <button type="button" x-show="questions.length > 1" @click="questions.splice(idx, 1)" class="text-sm text-red-600 hover:text-red-800">Xóa</button>
                            </div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Nội dung câu hỏi</label>
                                    <textarea :name="`questions[${idx}][question_text]`" x-model="question.question_text" rows="2" :required="type === 'quiz'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án A</label>
                                    <input :name="`questions[${idx}][option_a]`" x-model="question.option_a" :required="type === 'quiz'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án B</label>
                                    <input :name="`questions[${idx}][option_b]`" x-model="question.option_b" :required="type === 'quiz'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án C</label>
                                    <input :name="`questions[${idx}][option_c]`" x-model="question.option_c" :required="type === 'quiz'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án D</label>
                                    <input :name="`questions[${idx}][option_d]`" x-model="question.option_d" :required="type === 'quiz'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Đáp án đúng</label>
                                    <select :name="`questions[${idx}][correct_option]`" x-model="question.correct_option" :required="type === 'quiz'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="questions.push({ question_text: '', option_a: '', option_b: '', option_c: '', option_d: '', correct_option: 'A' })" class="mt-5 rounded-md border border-indigo-300 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50">
                        Thêm câu hỏi
                    </button>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('teacher.assignments.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</a>
                    <button class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Giao bài tập</button>
                </div>

                <!-- Beautiful Toast Notification -->
                <div x-show="toast.show" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-2 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-y-2 translate-x-2"
                     class="fixed bottom-5 right-5 z-50 flex items-center space-x-3 rounded-xl p-4 shadow-xl border border-slate-200 transition-all duration-300 bg-white"
                     :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'"
                     style="display: none;">
                    <!-- Success Icon -->
                    <template x-if="toast.type === 'success'">
                        <svg class="h-6 w-6 text-emerald-600 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <!-- Error Icon -->
                    <template x-if="toast.type === 'error'">
                        <svg class="h-6 w-6 text-rose-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    <span class="text-sm font-semibold" x-text="toast.message"></span>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
