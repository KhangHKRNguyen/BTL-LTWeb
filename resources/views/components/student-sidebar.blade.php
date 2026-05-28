<div class="w-full md:w-64 bg-white text-gray-700 flex flex-col justify-between rounded-xl shadow-sm border border-gray-200 min-h-auto md:min-h-[calc(100vh-140px)] transition-all duration-300">
    <div class="p-2 md:p-3">
        <div class="hidden md:block p-4 text-center border-b border-gray-100 bg-slate-50/70 rounded-t-lg mb-3">
            <h5 class="text-gray-800 font-bold text-xs tracking-wider uppercase">Chức năng Học viên</h5>
        </div>

        <nav class="flex flex-row md:flex-col gap-1.5 overflow-x-auto md:overflow-x-visible pb-2 md:pb-0 scrollbar-none">
              <a href="{{ route('student.assignments.index') }}" 
               class="flex items-center justify-center md:justify-start flex-shrink-0 px-4 py-2.5 md:py-3 text-xs font-semibold rounded-lg transition-all duration-200 whitespace-nowrap w-auto md:w-full
               {{ Request::is('student/assignments*') 
                    ? 'bg-indigo-50 text-indigo-700 border-b-2 md:border-b-0 md:border-l-4 border-indigo-500 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-b-2 border-transparent md:border-b-0' }}">
                <span class="me-1.5 text-sm md:text-base">📝</span> 
                <span>Bài tập</span>
            </a>
            <a href="{{ route('student.study.index') }}" 
               class="flex items-center justify-center md:justify-start flex-shrink-0 px-4 py-2.5 md:py-3 text-xs font-semibold rounded-lg transition-all duration-200 whitespace-nowrap w-auto md:w-full
               {{ Request::is('student/study*') 
                    ? 'bg-indigo-50 text-indigo-700 border-b-2 md:border-b-0 md:border-l-4 border-indigo-500 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-b-2 border-transparent md:border-b-0' }}">
                <span class="me-1.5 text-sm md:text-base">📁</span> 
                <span>Tài liệu lớp học</span>
            </a>

          

            <a href="{{ route('student.leave_requests.index') }}" 
               class="flex items-center justify-center md:justify-start flex-shrink-0 px-4 py-2.5 md:py-3 text-xs font-semibold rounded-lg transition-all duration-200 whitespace-nowrap w-auto md:w-full
               {{ Request::is('student/leave_requests*') 
                    ? 'bg-indigo-50 text-indigo-700 border-b-2 md:border-b-0 md:border-l-4 border-indigo-500 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-b-2 border-transparent md:border-b-0' }}">
                <span class="me-1.5 text-sm md:text-base">📊</span> 
                <span>Báo nghỉ</span>
            </a>
            
        </nav>
    </div>
</div>