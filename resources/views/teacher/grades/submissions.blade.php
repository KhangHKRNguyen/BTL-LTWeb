<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Danh sách bài nộp</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $assignment->title }}</p>
            </div>
            <a href="{{ route('teacher.grades.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Quay lại</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="mb-6 grid gap-4 md:grid-cols-4">
                <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Lớp</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $assignment->courseClass->class_name }}</p>
                </div>
                <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Loại</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $assignment->typeLabel() }}</p>
                </div>
                <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Số câu hỏi</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $assignment->questions_count }}</p>
                </div>
                <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Hạn nộp</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ optional($assignment->due_time)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Học viên</th>
                            <th class="px-6 py-3">Thời gian nộp</th>
                            <th class="px-6 py-3">Trạng thái</th>
                            <th class="px-6 py-3">Điểm số</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($submissions as $submission)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $submission->student->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full {{ $submission->isGraded() ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }} px-3 py-1 text-xs font-medium">
                                        {{ $submission->isGraded() ? 'Đã chấm' : 'Đã nộp' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $submission->grade !== null ? number_format((float) $submission->grade, 2) : '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('teacher.grades.edit', $submission) }}" class="text-indigo-600 hover:text-indigo-800">Chấm bài</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Chưa có học viên nộp bài.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $submissions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
