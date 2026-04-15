@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="max-w-4xl animate-[fadeIn_0.5s_ease-in-out]">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.movies.index') }}" class="text-[rgb(255,255,255)] transition hover:text-gray-300">
            <i class="fa-solid fa-chevron-left text-2xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-white">{{ $pageTitle }}</h2>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-500/10 p-5">
            <div class="flex items-start gap-3 text-red-400">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <div>
                    <h3 class="font-bold">Đã có lỗi xảy ra!</h3>
                    <ul class="mt-2 list-inside list-disc text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-[2rem] border border-gray-800 bg-gray-900/60 p-8 shadow-xl">
        @csrf
        
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Tên Phim -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-300">Tên Phim <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Avengers: Hồi Kết" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner">
            </div>

            <!-- Thể loại -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Thể loại</label>
                <input type="text" name="genre" value="{{ old('genre') }}" placeholder="Ví dụ: Hành động, Viễn tưởng" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner">
            </div>

            <!-- Thời lượng -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Thời lượng (Phút)</label>
                <div class="relative">
                    <input type="number" name="duration" value="{{ old('duration') }}" placeholder="Ví dụ: 120" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Phút</span>
                </div>
            </div>

            <!-- Đạo diễn -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Đạo diễn</label>
                <input type="text" name="director" value="{{ old('director') }}" placeholder="Tên Đạo diễn" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner">
            </div>

            <!-- Ngày chiếu -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Ngày Khởi Chiếu</label>
                <input type="date" name="release_date" value="{{ old('release_date') }}" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner [color-scheme:dark]">
            </div>

            <!-- Diễn viên -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-300">Diễn viên</label>
                <input type="text" name="actors" value="{{ old('actors') }}" placeholder="Phân tách bằng dấu phẩy" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner">
            </div>

            <!-- Giới hạn độ tuổi -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Giới hạn Độ tuổi</label>
                <select name="age_limit" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner appearance-none">
                    <option value="" class="bg-gray-900">Không giới hạn</option>
                    <option value="13" class="bg-gray-900" {{ old('age_limit') == 13 ? 'selected' : '' }}>C13 (13+)</option>
                    <option value="16" class="bg-gray-900" {{ old('age_limit') == 16 ? 'selected' : '' }}>C16 (16+)</option>
                    <option value="18" class="bg-gray-900" {{ old('age_limit') == 18 ? 'selected' : '' }}>C18 (18+)</option>
                </select>
            </div>

            <!-- Trailer Link -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Trailer Link (Youtube)</label>
                <input type="url" name="trailer_link" value="{{ old('trailer_link') }}" placeholder="https://youtube.com/..." class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner">
            </div>

            <!-- Mô tả -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-300">Mô tả phim</label>
                <textarea name="description" rows="4" placeholder="Nội dung tóm tắt..." class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner">{{ old('description') }}</textarea>
            </div>

            <!-- Poster -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-300">Ảnh Poster</label>
                <div class="rounded-2xl border-2 border-dashed border-gray-700 bg-black/30 p-8 text-center transition hover:border-red-500/50 hover:bg-black/50">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-800 text-gray-400 mb-4">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                    </div>
                    <p class="text-sm text-gray-400 mb-2">Kéo thả ảnh hoặc click để chọn file</p>
                    <input type="file" name="poster" accept="image/*" class="w-full max-w-xs text-sm text-gray-400 file:mr-4 file:rounded-full file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700 transition">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 border-t border-gray-800/60 pt-6 mt-8">
            <a href="{{ route('admin.movies.index') }}" class="rounded-xl px-6 py-3 text-sm font-bold text-gray-300 transition hover:text-white">
                Hủy
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                <i class="fa-solid fa-floppy-disk"></i>
                Lưu Phim
            </button>
        </div>
    </form>
</div>
@endsection
