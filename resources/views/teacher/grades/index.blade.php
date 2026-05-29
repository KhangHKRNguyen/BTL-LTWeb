<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Quản lý và chấm bài</h2>
            <a href="{{ route('teacher.assignments.index') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Giao bài tập
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Bài tập</th>
                            <th class="px-6 py-3">Lớp</th>
                            <th class="px-6 py-3">Loại</th>
                            <th class="px-6 py-3">Bài nộp</th>
                            <th class="px-6 py-3">Chờ chấm</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $assignment->title }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $assignment->courseClass->class_name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $assignment->typeLabel() }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $assignment->submissions_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">{{ $assignment->pending_submissions_count }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('teacher.grades.submissions', $assignment) }}" class="text-indigo-600 hover:text-indigo-800">Xem bài nộp</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Chưa có bài tập để chấm.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
