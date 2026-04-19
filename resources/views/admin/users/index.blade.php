@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.management') }}" class="text-[rgb(255,255,255)] transition hover:text-gray-300">
                <i class="fa-solid fa-chevron-left text-2xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-white">Danh sách người dùng</h2>
                <p class="mt-1 text-sm text-gray-400">Quản lý và phân tích hành vi của người dùng hệ thống.</p>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Khách hàng</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Liên hệ</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Phân quyền</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Sở thích Thể loại</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Khung giờ hay xem</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Tổng chi tiêu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($users as $user)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-purple-500/20 text-lg font-bold text-purple-400">
                                        {{ strtoupper(substr($user->name ?? $user->username ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white">{{ $user->name ?? 'Người dùng Thường' }}</div>
                                        <div class="mt-1 text-xs text-gray-500">{{ '@' . $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-gray-300">{{ $user->email ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="admin_role" onchange="this.form.submit()" class="rounded-lg bg-gray-800 text-xs font-semibold {{ $user->admin_role ? 'text-purple-400' : 'text-gray-300' }} border border-gray-700 hover:bg-gray-700 focus:ring-1 focus:ring-purple-500 outline-none p-2 cursor-pointer transition-colors shadow-sm">
                                        <option value="1" {{ $user->admin_role ? 'selected' : '' }}>Quản trị viên</option>
                                        <option value="0" {{ !$user->admin_role ? 'selected' : '' }}>Khách hàng</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->favorite_genre !== 'Chưa có dữ liệu')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-500 border border-amber-500/20">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        {{ $user->favorite_genre }}
                                    </span>
                                @else
                                    <span class="text-gray-600 text-xs italic">{{ $user->favorite_genre }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->favorite_time !== 'Chưa có dữ liệu')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-500/10 px-3 py-1 text-xs font-semibold text-sky-400 border border-sky-500/20">
                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                        {{ $user->favorite_time }}
                                    </span>
                                @else
                                    <span class="text-gray-600 text-xs italic">{{ $user->favorite_time }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-400">
                                    {{ number_format($user->total_spent, 0, ',', '.') }} ₫
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-users text-4xl mb-3 text-gray-700"></i>
                                    <p>Chưa có dữ liệu người dùng.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($users->hasPages())
            <div class="border-t border-gray-800 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
