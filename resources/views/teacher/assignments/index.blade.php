<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Giao bài tập</h2>
            <a href="{{ route('teacher.grades.index') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Quản lý chấm bài
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="font-semibold text-gray-900">Danh sách lớp học</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($classes as $class)
                        <div class="p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900">{{ $class->class_name }}</h4>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $class->students_count }} học viên · {{ $class->assignments_count }} bài tập
                                        @if ($class->room)
                                            · {{ $class->room }}
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('teacher.assignments.create', $class) }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                    Giao bài tập
                                </a>
                            </div>

                            <div class="mt-5 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        <tr>
                                            <th class="px-4 py-3">Tiêu đề</th>
                                            <th class="px-4 py-3">Loại</th>
                                            <th class="px-4 py-3">Hạn nộp</th>
                                            <th class="px-4 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @forelse ($class->assignments as $assignment)
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-gray-900">{{ $assignment->title }}</td>
                                                <td class="px-4 py-3 text-gray-600">{{ $assignment->typeLabel() }}</td>
                                                <td class="px-4 py-3 text-gray-600">{{ optional($assignment->due_time)->format('d/m/Y H:i') }}</td>
                                                <td class="px-4 py-3 text-right">
                                                    <a href="{{ route('teacher.assignments.show', $assignment) }}" class="text-indigo-600 hover:text-indigo-800">Chi tiết</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">Chưa có bài tập.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">Bạn chưa được phân công lớp học nào.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
