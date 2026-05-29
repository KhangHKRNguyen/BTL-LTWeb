<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LMS Dashboard') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col hidden md:flex">
            <div class="p-5 text-xl font-bold text-white border-b border-slate-800 tracking-wide">
                LMS SYSTEM
            </div>
            <nav class="flex-1 p-4 space-y-1">

                @if(auth()->user()->role === 'student')
                    <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Học viên</div>
                    
                    <a href="{{ route('student.study.index') }}" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('student.study.*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Lớp học và Tài liệu
                    </a>

                    <a href="{{ route('student.assignments.index') }}" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('student.assignments.*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Bài tập cần làm
                    </a>

                    <a href="{{ route('student.results.index') }}" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('student.results.*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Kết quả và Phản hồi
                    </a>

                    <a href="{{ route('student.leave_requests.index') }}" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('student.leave_requests.*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Xin nghỉ học
                    </a>
                @endif

                @if(auth()->user()->role === 'teacher')
                    <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Giáo viên</div>
                    
                    <a href="/teacher/attendance" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('teacher/attendance*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Điểm danh
                    </a>
                    
                    <a href="/teacher/materials" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('teacher/materials*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Quản lý tài liệu
                    </a>
                    
                    <a href="/teacher/assignments" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('teacher/assignments*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Giao bài tập
                    </a>
                    
                    <a href="/teacher/grades" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('teacher/grades*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Chấm bài
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Quản trị</div>
                    
                    <a href="/admin/accounts" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/accounts*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Quản lý tài khoản
                    </a>
                    
                    <a href="/admin/classes" 
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/classes*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    Quản lý lớp học
                    </a>
                @endif
            </nav>
            <div class="p-4 border-t border-slate-800 text-sm">
                Vai trò: <span class="text-blue-400 capitalize font-medium">{{ auth()->user()->role }}</span>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto">
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 z-10">
                <div class="text-lg font-semibold text-slate-700">Xin chào, {{ auth()->user()->name }}!</div>
                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 transition">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-6 flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-toast fixed bottom-5 right-5 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg flex items-center space-x-2 z-50">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-toast fixed bottom-5 right-5 bg-rose-600 text-white px-5 py-3 rounded-xl shadow-lg flex items-center space-x-2 z-50">
            <span>{{ session('error') }}</span>
        </div>
    @endif
</body>
</html>