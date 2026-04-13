@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="max-w-3xl space-y-6 animate-[fadeIn_0.5s_ease-in-out]">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.cinemas.index') }}"
           class="text-[rgb(255,255,255)] transition hover:text-gray-300">
            <i class="fa-solid fa-chevron-left text-2xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-white">Chỉnh Sửa Rạp Chiếu</h2>
            <p class="mt-1 text-sm text-gray-400">Cập nhật thông tin rạp <span class="text-sky-400 font-semibold">{{ $cinema->name }}</span>.</p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-red-400">
            <ul class="space-y-1 text-sm">
                @foreach($errors->all() as $e)
                    <li><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.cinemas.update', $cinema) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Thông tin rạp --}}
        <div class="rounded-3xl border border-gray-800 bg-gray-900/80 p-6 space-y-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/10 text-sky-400">
                    <i class="fa-solid fa-building"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Thông tin Rạp</h3>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Tên Rạp <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $cinema->name) }}"
                       class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Địa chỉ</label>
                <textarea name="address" rows="3"
                          class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20">{{ old('address', $cinema->address) }}</textarea>
            </div>
        </div>

        {{-- Phòng chiếu --}}
        <div class="rounded-3xl border border-gray-800 bg-gray-900/80 p-6 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/10 text-violet-400">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Phòng Chiếu</h3>
                </div>
                <button type="button" id="add-room-btn"
                        class="inline-flex items-center gap-2 rounded-xl border border-sky-500/40 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-400 transition hover:bg-sky-500/20">
                    <i class="fa-solid fa-plus"></i> Thêm phòng
                </button>
            </div>

            <div id="rooms-container" class="space-y-3">
                @foreach($cinema->rooms as $i => $room)
                    <div class="flex items-center gap-3 rounded-2xl border border-gray-800 bg-gray-950/50 p-4 room-row">
                        <input type="hidden" name="rooms[{{ $i }}][id]" value="{{ $room->id }}">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-400 text-xs font-bold shrink-0">
                            {{ $i + 1 }}
                        </div>
                        <input type="text" name="rooms[{{ $i }}][name]" value="{{ $room->name }}"
                               placeholder="Tên phòng"
                               class="flex-1 rounded-xl border border-gray-700 bg-black/40 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20">
                        <input type="number" name="rooms[{{ $i }}][seat_count]" value="{{ $room->seat_count }}"
                               placeholder="Số ghế" min="0"
                               class="w-28 rounded-xl border border-gray-700 bg-black/40 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20">
                        <button type="button" onclick="this.closest('.room-row').remove()"
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/10 text-red-400 transition hover:bg-red-500/20">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-500">Xóa phòng khỏi danh sách sẽ xóa vĩnh viễn phòng đó khỏi hệ thống.</p>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.cinemas.index') }}"
               class="rounded-xl border border-gray-700 bg-transparent px-5 py-2.5 text-sm font-semibold text-gray-300 transition hover:bg-gray-800">
                Hủy
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-sky-900/30 transition hover:bg-sky-700">
                <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<script>
    let roomCount = {{ $cinema->rooms->count() }};
    const container = document.getElementById('rooms-container');
    const addBtn    = document.getElementById('add-room-btn');

    addBtn.addEventListener('click', function () {
        const idx = roomCount++;
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 rounded-2xl border border-gray-800 bg-gray-950/50 p-4 room-row';
        div.innerHTML = `
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-400 text-xs font-bold shrink-0">
                ${idx + 1}
            </div>
            <input type="text" name="rooms[${idx}][name]" placeholder="Tên phòng mới"
                   class="flex-1 rounded-xl border border-gray-700 bg-black/40 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20">
            <input type="number" name="rooms[${idx}][seat_count]" placeholder="Số ghế" min="0"
                   class="w-28 rounded-xl border border-gray-700 bg-black/40 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20">
            <button type="button" onclick="this.closest('.room-row').remove()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/10 text-red-400 transition hover:bg-red-500/20">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        `;
        container.appendChild(div);
    });
</script>
@endsection
