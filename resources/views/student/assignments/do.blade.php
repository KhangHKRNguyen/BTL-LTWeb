<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $assignment->title }}</h1>
                </div>

                <div class="p-4 bg-blue-50/60 border border-blue-100 rounded-xl text-sm text-blue-800 space-y-1 shadow-sm">
                    <p class="font-bold text-blue-900 mb-1 flex items-center">💡 Thông tin bài tập:</p>
                    <p><span class="font-medium text-blue-600">Loại hình:</span> <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-bold rounded">{{ $assignment->type }}</span></p>
                    <p><span class="font-medium text-blue-600">Lớp quản lý:</span> <span class="font-semibold text-gray-800">{{ $assignment->courseClass->class_name }}</span></p>
                    <p>
                        <span class="font-medium text-blue-600">Hạn cuối nộp:</span> 
                        <span class="font-semibold {{ now() > $assignment->due_time ? 'text-red-600' : 'text-gray-800' }}">{{ $assignment->due_time->format('d/m/Y H:i') }}</span>
                        @if(now() > $assignment->due_time)
                            <span class="ms-1 px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded uppercase">Quá hạn</span>
                        @endif
                    </p>
                </div>

                @if($assignment->content)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-gray-100 font-semibold text-sm text-gray-700">
                            📖 Nội dung đề bài
                        </div>
                        <div class="p-5 text-sm text-gray-600 leading-relaxed">
                            {!! nl2br(e($assignment->content)) !!}
                        </div>
                    </div>
                @endif

                <form action="{{ route('student.assignments.store', $assignment->id) }}" method="POST" enctype="multipart/form-data" id="assignmentForm" class="space-y-6">
                    @csrf

                    @if($assignment->type === 'Trắc nghiệm')
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-5 py-3.5 bg-indigo-600 text-white font-semibold text-sm flex justify-between items-center">
                                <span>🎯 Câu hỏi trắc nghiệm</span>
                                <span class="px-2 py-0.5 bg-indigo-700 text-xs rounded-full font-medium">{{ $assignment->questions->count() }} câu hỏi</span>
                            </div>
                            <div class="p-5 space-y-6 divide-y divide-gray-100">
                                @foreach($assignment->questions as $question)
                                    @php
                                        $userAnswer = $submission?->studentAnswers->where('question_id', $question->id)->first();
                                    @endphp

                                    <div class="pt-6 first:pt-0">
                                        <h6 class="font-bold text-gray-800 mb-4 text-sm leading-snug">
                                            Câu {{ $loop->iteration }}: {{ $question->question_text }}
                                        </h6>

                                        <div class="ms-2 space-y-2.5 max-w-2xl">
                                            @foreach(['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d] as $key => $value)
                                                <div class="flex items-start p-3 rounded-lg border border-gray-100 hover:bg-slate-50 transition duration-150">
                                                    <input class="mt-0.5 text-indigo-600 focus:ring-indigo-500 border-gray-300" type="radio" 
                                                           name="answers[{{ $loop->parent->index }}][selected_option]" 
                                                           value="{{ $key }}" id="q{{ $question->id }}_{{ strtolower($key) }}"
                                                           @checked($userAnswer?->selected_option === $key)
                                                           @disabled($submission)>
                                                    <label class="ms-3 text-sm text-gray-700 font-medium cursor-pointer w-full" for="q{{ $question->id }}_{{ strtolower($key) }}">
                                                        <span class="font-bold text-gray-400 me-1">{{ $key }}.</span> {{ $value }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <input type="hidden" name="answers[{{ $loop->index }}][question_id]" value="{{ $question->id }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    @else
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-5 py-3.5 bg-blue-600 text-white font-semibold text-sm">
                                📝 Khu vực làm bài tự luận
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <x-input-label for="submission_content" value="Nhập nội dung bài làm văn bản (nếu có)" />
                                    <textarea id="submission_content" name="submission_content" rows="6" 
                                              class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" 
                                              placeholder="Gõ trực tiếp câu trả lời của bạn tại đây..."
                                              @disabled($submission)>{{ $submission?->submission_content }}</textarea>
                                    <x-input-error :messages="$errors->get('submission_content')" class="mt-1" />
                                </div>

                                <div class="pt-2">
                                    <x-input-label for="file" value="Tải lên tệp đính kèm bài làm (Tối đa 10MB)" />
                                    <input type="file" id="file" name="file"
                                           class="block w-full mt-1 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-gray-300 rounded-md shadow-sm p-1 bg-white"
                                           accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png"
                                           @disabled($submission)>
                                    <span class="text-[11px] text-gray-400 block mt-1">Định dạng hỗ trợ: PDF, DOC, DOCX, TXT, JPG, PNG</span>
                                    <x-input-error :messages="$errors->get('file')" class="mt-1" />

                                    @if($submission?->file_path)
                                        <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                                            <span class="text-gray-600 font-medium">📁 Bài làm đính kèm đã lưu:</span>
                                            <a href="{{ asset($submission->file_path) }}" target="_blank">
                                                <x-secondary-button type="button" class="py-1 px-2.5 text-[10px]">Xem file</x-secondary-button>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($submission && $submission->grade !== null)
                        <div class="bg-white border border-emerald-200 rounded-xl shadow-sm overflow-hidden border-l-4 !border-l-emerald-500">
                            <div class="px-5 py-3 bg-emerald-50 text-emerald-800 font-bold text-sm">
                                🎉 Kết quả chấm điểm từ Giáo viên
                            </div>
                            <div class="p-5 space-y-2 text-sm">
                                <p class="text-gray-700">
                                    <span class="font-medium text-gray-500">Điểm số đạt được:</span> 
                                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded border border-indigo-100 text-base ml-1">{{ $submission->grade }}/10</span>
                                </p>
                                @if($submission->teacher_comment)
                                    <p class="text-gray-700 pt-2 border-t border-gray-100 mt-2">
                                        <span class="font-medium text-gray-500">Lời phê nhận xét:</span><br>
                                        <span class="italic text-gray-800 block mt-1 bg-slate-50 p-3 rounded-lg border border-slate-100">"{{ $submission->teacher_comment }}"</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <x-input-error :messages="$errors->all()" class="mb-2" />

                    <div class="flex flex-col sm:flex-row justify-between gap-3 items-center pt-4 border-t border-gray-100">
                        @if($submission)
                            <a href="{{ route('student.assignments.index') }}" class="w-full sm:w-auto">
                                <x-secondary-button type="button" class="w-full justify-center">← Quay lại danh sách</x-secondary-button>
                            </a>
                        @else
                            <x-secondary-button type="button" x-data x-on:click="$dispatch('open-modal', 'confirm-back-assignment')" class="w-full sm:w-auto justify-center">
                                ← Quay lại danh sách
                            </x-secondary-button>
                        @endif
                        
                        @if($submission)
                            <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-100 border border-transparent rounded-md font-semibold text-xs text-emerald-600 uppercase tracking-widest shadow-sm cursor-not-allowed opacity-70" disabled>
                                ✓ Bài làm đã được ghi nhận
                            </button>
                        @elseif(now() <= $assignment->due_time)
                            <x-primary-button type="submit" id="submitBtn">✓ Nộp bài làm</x-primary-button>
                        @else
                            <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-100 border border-transparent rounded-md font-semibold text-xs text-red-500 uppercase tracking-widest shadow-sm cursor-not-allowed" disabled>❌ Quá hạn nộp bài</button>
                        @endif
                    </div>
                </form>

                <x-modal name="confirm-back-assignment" maxWidth="md">
                    <div class="p-6">
                        <div class="flex items-center gap-3 text-red-500 mb-3">
                            <span class="text-2xl">🚨</span>
                            <h3 class="text-lg font-bold text-gray-900">Cảnh báo: Chưa nộp bài!</h3>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                            Tiến trình làm bài tập của bạn **chưa được lưu lại**. Nếu rời khỏi trang lúc này, các đáp án bạn đã chọn hoặc câu trả lời đã gõ sẽ bị xóa sạch. Bạn vẫn muốn thoát chứ?
                        </p>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                            <x-secondary-button type="button" x-data x-on:click="$dispatch('close-modal', 'confirm-back-assignment')">
                                Tiếp tục làm bài
                            </x-secondary-button>

                            <a href="{{ route('student.assignments.index') }}">
                                <x-danger-button type="button">
                                    Xác nhận rời đi
                                </x-danger-button>
                            </a>
                        </div>
                    </div>
                </x-modal>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden sticky top-6">
                    <div class="px-4 py-3 bg-slate-800 text-slate-100 font-semibold text-sm flex items-center gap-1.5">
                        <span>📖 Hướng dẫn làm bài</span>
                    </div>
                    <div class="p-5 text-xs text-gray-600 space-y-4">
                        @if($assignment->type === 'Trắc nghiệm')
                            <ul class="list-disc ps-4 space-y-1.5">
                                <li>Hãy click lựa chọn duy nhất một đáp án đúng cho mỗi câu.</li>
                                <li>Hệ thống cho phép bạn thay đổi đáp án linh hoạt lúc làm bài.</li>
                                <li>Nhớ bấm nút <strong class="text-gray-800">"Nộp bài làm"</strong> ở cuối trang khi hoàn tất.</li>
                            </ul>
                        @else
                            <ul class="list-disc ps-4 space-y-1.5">
                                <li>Bạn có thể soạn thảo trực tiếp văn bản hoặc tải lên file Word/PDF đính kèm.</li>
                                <li>Dung lượng file đính kèm không được vượt quá mốc 10MB.</li>
                                <li>Hệ thống cho phép nộp cập nhật ghi đè bài cũ nếu còn trong thời hạn.</li>
                            </ul>
                        @endif

                        <hr class="border-gray-100">
                        
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-center">
                            <p class="font-medium text-gray-500 mb-1">⏰ Thời gian còn lại:</p>
                            <p id="countdown" class="text-sm font-bold text-red-600 tracking-wide">
                                Đang tính toán...
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function updateCountdown() {
            const dueDate = new Date('{{ $assignment->due_time }}');
            const now = new Date();
            const diff = dueDate - now;

            if (diff <= 0) {
                document.getElementById('countdown').textContent = 'Đã hết hạn nộp bài!';
                document.getElementById('submitBtn')?.setAttribute('disabled', 'disabled');
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('countdown').textContent = 
                `${days} ngày ${hours} giờ ${minutes} phút ${seconds} giây`;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
</x-app-layout>