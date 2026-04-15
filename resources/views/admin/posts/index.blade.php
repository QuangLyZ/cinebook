@extends('layouts.admin')

@section('title', 'Quản lý Bài Viết')
@section('page-title', 'Bài Viết')

@section('content')
<div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.management') }}" class="text-white transition hover:text-gray-300">
            <i class="fa-solid fa-chevron-left text-2xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-white">Danh sách Bài Viết</h2>
            <p class="mt-1 text-sm text-gray-400">Xem và quản lý các bài viết đã được đăng lên hệ thống.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed whitespace-nowrap text-left text-sm">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400">
                    <tr>
                        <th class="w-[40%] px-6 py-4 text-center">Bài viết</th>
                        <th class="w-[25%] px-6 py-4 text-center">Tác giả</th>
                        <th class="w-[20%] px-6 py-4 text-center">Trạng thái</th>
                        <th class="w-[15%] px-6 py-4 text-center">Ngày đăng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($posts as $post)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="max-w-xs px-6 py-4">
                                <div class="truncate font-bold text-white">{{ $post->title }}</div>
                                <div class="mt-1 line-clamp-1 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($post->context ?? '', 80) }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-semibold text-gray-200">{{ $post->author_name }}</span>
                                    <span class="mt-0.5 text-xs text-gray-500">{{ $post->author_email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'published' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'draft' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'archived' => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                                    ];
                                    $statusLabels = [
                                        'published' => 'Đã đăng',
                                        'draft' => 'Bản nháp',
                                        'archived' => 'Lưu trữ',
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
                            <td class="px-6 py-4 text-center text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-newspaper mb-3 text-4xl text-gray-700"></i>
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

    <form action="{{ route('admin.posts.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <h3 class="my-7 block text-center text-2xl font-bold text-gray-300">TẠO BÀI VIẾT MỚI</h3>
        </div>

        <div class="mx-auto max-w-4xl space-y-6 rounded-2xl border border-gray-800 bg-gray-900/60 p-6 shadow-xl backdrop-blur-xl">
            <div>
                <strong class="mb-4 block text-center text-gray-400">TIÊU ĐỀ</strong>
                <input
                    type="text"
                    name="title"
                    placeholder="Nhập tiêu đề..."
                    class="w-full rounded-xl border border-gray-700 bg-gray-950 p-3 text-white outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500"
                >
            </div>

            <div>
                <strong class="mb-4 block text-center text-gray-400">KEYWORDS</strong>
                <input
                    type="text"
                    name="keywords"
                    placeholder="vd: phim hay, cinema..."
                    class="w-full rounded-xl border border-gray-700 bg-gray-950 p-3 text-white outline-none transition focus:border-pink-500 focus:ring-1 focus:ring-pink-500"
                >
            </div>

            <div>
                <strong class="mb-4 block text-center text-gray-400">THỜI GIAN ĐĂNG</strong>
                <input
                    type="text"
                    id="publish_at"
                    name="publish_at"
                    placeholder="Chọn ngày giờ đăng..."
                    value="{{ old('publish_at') }}"
                    class="mt-1 w-full rounded-xl border border-gray-700 bg-gray-950 p-3 text-white outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500"
                >
            </div>

            <div>
                <strong class="mb-4 block text-center text-gray-400">NỘI DUNG BÀI VIẾT</strong>
                <textarea id="editor" class="ckeditor" name="content"></textarea>
                <style>
                    .ck-editor__editable { min-height: 200px; background: #111827 !important; color: #ffffff !important; }
                </style>
            </div>

            <div class="flex items-center justify-between text-center">
                <button class="rounded-xl bg-gradient-to-r from-pink-500 to-red-500 px-6 py-3 font-semibold text-white shadow-lg transition hover:opacity-90">
                    Đăng bài
                </button>

                @if(session('success'))
                    <p class="text-green-400">
                        {{ session('success') }}
                    </p>
                @endif
            </div>
        </div>
    </form>

    <div class="space-y-4">
        @foreach($posts ?? [] as $post)
            <div class="rounded bg-gray-800 p-4">
                <h3 class="text-xl text-white">{{ $post->title }}</h3>
                <p class="text-sm text-gray-400">{{ $post->keywords }}</p>
                <p class="text-xs text-gray-500">
                    {{ $post->publish_at ?? 'Đăng ngay' }}
                </p>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
let editorInstance = null;

document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#editor');

    if (!el || editorInstance) {
        return;
    }

    ClassicEditor
        .create(el, {
            ckfinder: {
                uploadUrl: "{{ route('upload.image') }}?_token={{ csrf_token() }}"
            }
        })
        .then(editor => {
            editorInstance = editor;
            console.log('CKEditor READY');
        })
        .catch(error => {
            console.error(error);
        });
});
</script>
@endsection
