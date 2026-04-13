@extends('layouts.admin')

@section('title', 'Quản lý Bài Viết')
@section('page-title', 'Bài Viết')

@section('content')
<div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.management') }}" class="text-[rgb(255,255,255)] transition hover:text-gray-300">
            <i class="fa-solid fa-chevron-left text-2xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-white">Danh sách Bài Viết</h2>
            <p class="mt-1 text-sm text-gray-400">Xem và quản lý các bài viết đã được đăng lên hệ thống.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Bài viết</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Tác giả</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Ngày đăng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($posts as $post)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="px-6 py-4 max-w-xs">
                                <div class="font-bold text-white truncate">{{ $post->title }}</div>
                                <div class="mt-1 text-xs text-gray-500 line-clamp-1">{{ Str::limit($post->context ?? '', 80) }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-semibold text-gray-200">{{ $post->author_name }}</span>
                                    <span class="text-xs text-gray-500 mt-0.5">{{ $post->author_email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'published' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'draft'     => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'archived'  => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                                    ];
                                    $statusLabels = [
                                        'published' => 'Đã đăng',
                                        'draft'     => 'Bản nháp',
                                        'archived'  => 'Lưu trữ',
                                    ];
                                    $statusKey = $post->status ?? 'published';
                                    $colorClass = $statusColors[$statusKey] ?? $statusColors['published'];
                                    $statusLabel = $statusLabels[$statusKey] ?? 'Đã đăng';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $colorClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ str_contains($colorClass, 'emerald') ? 'bg-emerald-400' : (str_contains($colorClass, 'amber') ? 'bg-amber-400' : 'bg-gray-400') }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-400 text-xs">
                                {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-newspaper text-4xl mb-3 text-gray-700"></i>
                                    <p>Chưa có bài viết nào được đăng.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($posts, 'hasPages') && $posts->hasPages())
            <div class="border-t border-gray-800 px-6 py-4">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
