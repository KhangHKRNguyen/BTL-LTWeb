<x-app-layout>
    <div class="max-w-lg space-y-5">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.classes.index') }}" class="text-slate-400 hover:text-slate-600">← Quay lại</a>
            <h1 class="text-2xl font-bold text-slate-800">Tạo lớp học mới</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tên lớp học <span class="text-red-500">*</span></label>
                    <input type="text" name="class_name" value="{{ old('class_name') }}"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('class_name') border-red-400 @enderror"
                           placeholder="VD: Lập trình Web - Nhóm 01">
                    @error('class_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Ngày bắt đầu <span class="text-red-500">*</span></label>
                        <input type="date" name="start_time" value="{{ old('start_time') }}"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('start_time') border-red-400 @enderror">
                        @error('start_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Ngày kết thúc <span class="text-red-500">*</span></label>
                        <input type="date" name="end_time" value="{{ old('end_time') }}"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('end_time') border-red-400 @enderror">
                        @error('end_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phòng học</label>
                    <input type="text" name="room" value="{{ old('room') }}"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="VD: Phòng 402-A2">
                </div>

                <div class="flex space-x-3 pt-2">
                    <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                        Tạo lớp học
                    </button>
                    <a href="{{ route('admin.classes.index') }}" class="bg-slate-100 text-slate-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
