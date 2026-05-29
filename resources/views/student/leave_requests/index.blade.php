<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6"> 
            
            <div class="w-full md:w-64 flex-shrink-0">
                <x-student-sidebar /> 
            </div>

            <div class="flex-1 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="container mt-2">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="me-2">📂</span> Danh sách đơn xin nghỉ
                        </h1>
                        <a href="{{ route('student.leave_requests.create') }}">
                            <x-primary-button type="button">
                                ➕ Xin nghỉ mới
                            </x-primary-button>
                        </a>
                    </div>

                    @if(session('success'))
                        <x-auth-session-status :status="session('success')" class="mb-4 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg shadow-sm font-medium" />
                    @endif

                    <div class="space-y-4">
                        @forelse($leaveRequests as $request)
                            <div class="p-5 bg-white border border-gray-200 hover:border-blue-300 rounded-xl shadow-sm transition-all duration-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 !border-l-blue-500">
                                
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-md border border-blue-100">
                                            {{ $request->courseClass->class_name }}
                                        </span>
                                        @if($request->courseClass->room)
                                            <span class="text-xs text-gray-400">🚪 Phòng {{ $request->courseClass->room }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium text-gray-500">📅 Ngày xin nghỉ:</span> 
                                        <span class="font-semibold text-gray-900">{{ $request->request_date->format('d/m/Y') }}</span>
                                    </p>
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium text-gray-500">📝 Lý do:</span> 
                                        <span class="italic text-gray-800">"{{ $request->reason }}"</span>
                                    </p>
                                    <p class="text-xs text-gray-400 pt-1 flex items-center gap-1">
                                        🕒 Gửi lúc: {{ $request->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <div class="flex-shrink-0 self-end md:self-center">
                                    <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200 shadow-sm">
                                        ✓ Đã gửi
                                    </span>
                                </div>

                            </div>
                        @empty
                            <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-xl bg-slate-50">
                                <div class="text-4xl mb-2">📥</div>
                                <h3 class="text-sm font-semibold text-gray-700">Chưa có đơn xin nghỉ nào</h3>
                                <p class="text-xs text-gray-400 mt-1 mb-4">Mọi đơn đăng ký xin phép nghỉ học của bạn sẽ hiển thị tập trung tại đây.</p>
                                <a href="{{ route('student.leave_requests.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 underline">
                                    Tạo đơn xin nghỉ đầu tiên ngay
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($leaveRequests->hasPages())
                        <nav class="mt-6 border-t border-gray-100 pt-4">
                            {{ $leaveRequests->links() }}
                        </nav>
                    @endif

                </div>
            </div> </div> 
    </div>
</x-app-layout>