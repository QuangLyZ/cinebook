@extends('layouts.admin')

@section('title', 'Quản lý Bài Viết')
@section('page-title', 'Bài Viết')

@section('content')
<div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
    <div>
<h2 class="text-2xl font-extrabold text-white ">
    Danh sách Bài Viết
</h2>

<p class="mt-1 text-sm text-gray-400 ">
    Xem và quản lý các bài viết đã được đăng lên hệ thống.
</p>
      
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-left text-sm whitespace-nowrap">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400 ">
                    <tr>
                        <th class="px-6 py-4 w-1/3 text-center">Bài viết</th>
                        <th class="px-6 py-4 w-1/3 text-center">Trạng thái</th>
                        <th class="px-6 py-4 w-1/3 text-center">Ngày đăng</th>
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
<form action="{{ route('admin.posts.store') }}" method="POST">
        @csrf
    <div>
        <h1 class=" text-center justify-between block text-gray-300 my-7 font-bold text-2xl">TẠO BÀI VIẾT MỚI</h1>
    </div>
    <div class="max-w-4xl mx-auto bg-gray-900/60 backdrop-blur-xl 
                border border-gray-800 rounded-2xl p-6 space-y-6 shadow-xl">

        <!-- Title -->
        <div>
            <strong class="text-center block text-gray-400 mb-4">TIÊU ĐỀ</strong>
            <input type="text" name="title" placeholder="Nhập tiêu đề..."
                class="w-full p-3 rounded-xl bg-gray-950 text-white 
                       border border-gray-700 focus:border-red-500 
                       focus:ring-1 focus:ring-red-500 outline-none transition">
        </div>

        <!-- Keywords -->
        <div>
            <strong class=" text-center block text-gray-400 mb-4">KEYWORDS</strong>
            <input type="text" name="keywords" placeholder="vd: phim hay, cinema..."
                class="w-full p-3 rounded-xl bg-gray-950 text-white 
                       border border-gray-700 focus:border-pink-500 
                       focus:ring-1 focus:ring-pink-500 outline-none transition">
        </div>

        <!-- Publish time -->
        <div>
            <strong class=" text-center block text-gray-400 mb-4">THỜI GIAN ĐĂNG</strong>

    <input type="text" id="publish_at" name="publish_at"
        placeholder="Chọn ngày giờ đăng..."
        value="{{ old('publish_at') }}"
        style="
            width:100%;
            padding:12px;
            border-radius:12px;
            border:1px solid #374151;
            background:#020617;
            color:white;
            margin-top:5px;
        ">
        </div>

        <!-- Content -->
        <div>
            <strong class=" text-center block text-gray-400 mb-4">NỘI DUNG BÀI VIẾT</strong>
           <textarea id="editor" class="ckeditor" name="content"></textarea>
            <style> .ck-editor__editable { min-height: 200px; background: #111827 !important; color: #ffffff !important; } </style>
        </div>

        <!-- Button -->
        <div class="text-center flex items-center justify-between">
            <button 
                class="bg-red-600 px-6 py-3 rounded-xl text-white font-semibold
                       bg-gradient-to-r from-pink-500 to-red-500 
                       hover:opacity-90 transition shadow-lg">
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

    <!-- LIST -->
    <div class="space-y-4">
        @foreach($posts ?? [] as $post)
            <div class="p-4 bg-gray-800 rounded">
                <h3 class="text-xl text-white">{{ $post->title }}</h3>
                <p class="text-gray-400 text-sm">{{ $post->keywords }}</p>
                <p class="text-gray-500 text-xs">
                    {{ $post->publish_at ?? 'Đăng ngay' }}
                </p>
            </div>

        @endforeach
    </div>

</div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
let editorInstance = null;

document.addEventListener("DOMContentLoaded", function () {

    const el = document.querySelector('#editor');

    if (!el) return;

    // ❗ nếu đã có rồi thì không tạo lại
    if (editorInstance) return;

    ClassicEditor
        .create(el, {
            ckfinder: {
                uploadUrl: "{{ route('upload.image') }}?_token={{ csrf_token() }}"
            }
        })
        .then(editor => {
            editorInstance = editor;
            console.log("✅ CKEditor READY");
        })
        .catch(error => {
            console.error(error);
        });
});
</script>
@endsection
