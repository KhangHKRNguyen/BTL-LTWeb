<x-app-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Bảng Điểm và Kết Quả Học Tập</h1>
                <p class="text-sm text-slate-500">Xem lại điểm số, lời phê và gửi thắc mắc phản hồi nếu có sai sót.</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-6 py-4">Tên bài tập</th>
                        <th class="px-6 py-4">Lớp học</th>
                        <th class="px-6 py-4">Loại bài</th>
                        <th class="px-6 py-4 text-center">Điểm số</th>
                        <th class="px-6 py-4">Ngày chấm</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                    @forelse($results as $result)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $result->assignment->title }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $result->assignment->courseClass->class_name ?? 'N/A' }}</td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $result->assignment->type === 'Trắc nghiệm' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $result->assignment->type ?? 'Chưa rõ' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-base font-bold {{ $result->grade >= 5 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $result->grade }} / 10
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $result->updated_at->format('H:i d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('student.results.show', $result->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                                    Xem chi tiết và Phản hồi &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                Bạn chưa có bài tập nào được chấm điểm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>