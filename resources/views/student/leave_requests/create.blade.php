<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">
                📝 Xin phép nghỉ học
            </h2>

            <x-input-error :messages="$errors->all()" class="mb-4" />

            <form action="{{ route('student.leave_requests.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <x-input-label for="course_class_id" value="Chọn lớp học" required />
                    <select class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" 
                            id="course_class_id" name="course_class_id" required>
                        <option value="">-- Chọn lớp --</option>
                        @forelse($classes as $class)
                            <option value="{{ $class->id }}" @selected(old('course_class_id') == $class->id)>
                                {{ $class->class_name }} @if($class->room) (Phòng {{ $class->room }}) @endif
                            </option>
                        @empty
                            <option value="" disabled>Không có lớp học nào</option>
                        @endforelse
                    </select>
                    <x-input-error :messages="$errors->get('course_class_id')" class="mt-1" />
                </div>

                <div class="mb-4">
                    <x-input-label for="request_date" value="Ngày xin nghỉ" required />
                    <x-text-input 
                        id="request_date" 
                        name="request_date" 
                        type="date" 
                        class="block w-full mt-1 text-sm" 
                        :value="old('request_date')" 
                        min="{{ now()->format('Y-m-d') }}" 
                        required 
                    />
                    <x-input-error :messages="$errors->get('request_date')" class="mt-1" />
                </div>

                <div class="mb-6">
                    <x-input-label for="reason" value="Lý do xin nghỉ" required />
                    <textarea 
                        id="reason" 
                        name="reason" 
                        rows="5" 
                        class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" 
                        placeholder="Nhập lý do xin nghỉ học chi tiết..." 
                        required
                    >{{ old('reason') }}</textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-1" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <x-secondary-button type="button" x-data x-on:click="$dispatch('open-modal', 'confirm-back')">
                        ← Quay lại
                    </x-secondary-button>
                    
                    <x-primary-button type="submit">
                        Gửi đơn xin nghỉ →
                    </x-primary-button>
                </div>

            </form> <x-modal name="confirm-back" maxWidth="md">
                <div class="p-6">
                    <div class="flex items-center gap-3 text-amber-600 mb-3">
                        <span class="text-2xl">⚠️</span>
                        <h3 class="text-lg font-bold text-gray-900">Xác nhận rời khỏi trang</h3>
                    </div>
                    
                    <p class="text-sm text-gray-600 my-4 leading-relaxed">
                        Nội dung đơn xin nghỉ học của bạn **chưa được lưu**. Bạn có chắc chắn muốn rời đi và chấp nhận mất các thông tin đã nhập không?
                    </p>
                    
                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                        <x-secondary-button type="button" x-data x-on:click="$dispatch('close-modal', 'confirm-back')">
                            Ở lại gõ tiếp
                        </x-secondary-button>

                        <a href="{{ route('student.leave_requests.index') }}">
                            <x-danger-button type="button">
                                Rời đi và hủy đơn
                            </x-danger-button>
                        </a>
                    </div>
                </div>
            </x-modal>

        </div>
    </div>
</x-app-layout>