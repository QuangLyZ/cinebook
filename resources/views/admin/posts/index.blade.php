@extends('layouts.admin')

@section('title', 'Quản lý Bài Viết')
@section('page-title', 'Bài Viết')

@section('content')
<<<<<<< HEAD
@php
    $statusColors = [
        'published' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
        'scheduled' => 'border-amber-500/30 bg-amber-500/10 text-amber-300',
        'draft' => 'border-gray-500/30 bg-gray-500/10 text-gray-300',
    ];
    $statusLabels = [
        'published' => 'Đã đăng',
        'scheduled' => 'Lên lịch',
        'draft' => 'Nháp',
    ];
@endphp

<div class="space-y-8 animate-[fadeIn_0.5s_ease-in-out]">
    @if ($errors->any())
        <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-300">
            <div class="font-bold text-white">Không thể lưu bài viết</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-300">
            {{ session('success') }}
=======
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
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
        </div>
    @endif

<<<<<<< HEAD
    <section class="grid gap-8 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
        <form id="postCreateForm" action="{{ route('admin.posts.store') }}" method="POST" class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-xl shadow-black/10">
            @csrf

            <div class="flex items-center justify-between gap-4 border-b border-gray-800 pb-5">
                <div>
                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Tạo bài viết mới</h3>
                </div>
                <button id="submitPostButton" type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <i class="fa-solid fa-paper-plane"></i>
                    Lưu bài viết
                </button>
            </div>

            <div class="mt-6 grid gap-6">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="mb-2 block text-sm font-semibold text-gray-300">Tiêu đề</label>
                            <input id="title" type="text" name="title" value="{{ old('title') }}" placeholder="Ví dụ: Ưu đãi đặt vé cuối tuần cho sinh viên" class="w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white placeholder:text-gray-500 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        </div>

                        <div>
                            <label for="keywords" class="mb-2 block text-sm font-semibold text-gray-300">Keywords</label>
                            <input id="keywords" type="text" name="keywords" value="{{ old('keywords') }}" placeholder="khuyến mãi, phim mới, combo..." class="w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white placeholder:text-gray-500 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        </div>

                        <div>
                            <label for="publish_at" class="mb-2 block text-sm font-semibold text-gray-300">Thời gian đăng</label>
                            <input id="publish_at" type="datetime-local" name="publish_at" value="{{ old('publish_at') }}" class="w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white [color-scheme:dark] focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            <p class="mt-2 text-xs text-gray-500">Để trống nếu muốn đăng ngay.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="mb-2 block text-sm font-semibold text-gray-300">Thumbnail</div>
                            <div id="thumbnailDropzone" class="group relative flex min-h-[280px] cursor-pointer flex-col items-center justify-center rounded-[1.75rem] border border-dashed border-gray-700 bg-[linear-gradient(180deg,rgba(17,24,39,0.95),rgba(3,7,18,0.95))] p-5 text-center transition hover:border-red-500/60 hover:bg-gray-950">
                                <input id="thumbnailFile" type="file" accept="image/*" class="hidden">
                                <input id="thumbnail" type="hidden" name="thumbnail" value="{{ old('thumbnail') }}">

                                <div id="thumbnailEmptyState" class="{{ old('thumbnail') ? 'hidden' : '' }}">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-red-500/10 text-red-300">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                    </div>
                                    <div class="mt-4 text-lg font-bold text-white">Kéo thả ảnh thumbnail</div>
                                    <div class="mt-2 text-sm text-gray-400">PNG, JPG, WEBP hoặc GIF. Tối đa 5MB.</div>
                                    <button type="button" id="thumbnailPickerButton" class="mt-5 inline-flex items-center gap-2 rounded-2xl border border-gray-700 bg-gray-900 px-4 py-2 text-sm font-semibold text-gray-200 transition hover:border-red-500 hover:text-white">
                                        <i class="fa-regular fa-image"></i>
                                        Chọn ảnh
                                    </button>
                                </div>

                                <div id="thumbnailPreviewState" class="w-full {{ old('thumbnail') ? '' : 'hidden' }}">
                                    <img id="thumbnailPreview" src="{{ old('thumbnail') }}" alt="Thumbnail preview" class="h-56 w-full rounded-[1.25rem] object-cover">
                                    <div class="mt-4 rounded-2xl border border-gray-800 bg-black/40 p-3 text-left">
                                        <div class="text-xs uppercase tracking-[0.22em] text-gray-500">Cloudinary URL</div>
                                        <div id="thumbnailUrlLabel" class="mt-2 break-all text-sm text-gray-300">{{ old('thumbnail') }}</div>
                                    </div>
                                    <button type="button" id="removeThumbnailButton" class="mt-4 inline-flex items-center gap-2 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 transition hover:bg-red-500/20">
                                        <i class="fa-solid fa-trash-can"></i>
                                        Gỡ thumbnail
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="thumbnailUrlField" class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">URL sẽ lưu vào DB</label>
                                <input id="thumbnailUrlField" type="text" value="{{ old('thumbnail') }}" readonly class="w-full rounded-2xl border border-gray-800 bg-gray-950 px-4 py-3 text-sm text-gray-300 placeholder:text-gray-600 focus:outline-none">
                            </div>
                            <div id="thumbnailUploadStatus" class="mt-3 text-sm text-gray-400"></div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="editor" class="mb-2 block text-sm font-semibold text-gray-300">Nội dung bài viết</label>
                    <textarea id="editor" class="ckeditor" name="content">{{ old('content') }}</textarea>
                </div>
            </div>
        </form>

        <div class="space-y-6">
            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-xl shadow-black/10">
                <div class="flex items-center justify-between gap-4 border-b border-gray-800 pb-5">
                    <div>
                        <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Bài viết đã tạo</h3>
                    </div>
                    <div class="text-sm text-gray-500">{{ $posts->total() }} bài viết</div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800 text-sm">
                            <thead class="bg-gray-950/80 text-left text-xs uppercase tracking-[0.22em] text-gray-500">
                                <tr>
                                    <th class="px-4 py-4">Tiêu đề</th>
                                    <th class="px-4 py-4">Thumbnail</th>
                                    <th class="px-4 py-4">Trạng thái</th>
                                    <th class="px-4 py-4">Ngày đăng</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 bg-gray-950/40">
                                @forelse ($posts as $post)
                                    @php
                                        $statusKey = $post->status ?? 'draft';
                                        $statusClass = $statusColors[$statusKey] ?? $statusColors['draft'];
                                        $statusLabel = $statusLabels[$statusKey] ?? 'Nháp';
                                    @endphp
                                    <tr class="align-top text-gray-300">
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-white">{{ $post->title }}</div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 90) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($post->thumbnail)
                                                <a href="{{ $post->thumbnail }}" target="_blank" rel="noopener noreferrer" class="text-red-400 hover:text-red-300">
                                                    Xem ảnh
                                                </a>
                                            @else
                                                <span class="text-gray-500">Không có</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="rounded-full border px-3 py-1 text-[11px] font-semibold {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-xs text-gray-400">
                                            {{ $post->publish_at?->format('d/m/Y H:i') ?: 'Đăng ngay' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                                            Chưa có bài viết nào trong hệ thống.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($posts->hasPages())
                    <div class="mt-6 border-t border-gray-800 pt-5">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
=======
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
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
let editorInstance = null;

document.addEventListener('DOMContentLoaded', function () {
<<<<<<< HEAD
    const editorElement = document.querySelector('#editor');
    const dropzone = document.getElementById('thumbnailDropzone');
    const pickerButton = document.getElementById('thumbnailPickerButton');
    const fileInput = document.getElementById('thumbnailFile');
    const hiddenInput = document.getElementById('thumbnail');
    const emptyState = document.getElementById('thumbnailEmptyState');
    const previewState = document.getElementById('thumbnailPreviewState');
    const previewImage = document.getElementById('thumbnailPreview');
    const urlLabel = document.getElementById('thumbnailUrlLabel');
    const statusLabel = document.getElementById('thumbnailUploadStatus');
    const thumbnailUrlField = document.getElementById('thumbnailUrlField');
    const removeButton = document.getElementById('removeThumbnailButton');
    const form = document.getElementById('postCreateForm');
    const submitButton = document.getElementById('submitPostButton');
    const uploadUrl = @json(route('admin.posts.upload-thumbnail'));
    let isUploadingThumbnail = false;

    if (editorElement && !editorInstance) {
        ClassicEditor
            .create(editorElement, {
                ckfinder: {
                    uploadUrl: "{{ route('upload.image') }}?_token={{ csrf_token() }}"
                }
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });
    }

    const setThumbnail = (url) => {
        hiddenInput.value = url || '';
        thumbnailUrlField.value = url || '';

        if (url) {
            previewImage.src = url;
            urlLabel.textContent = url;
            emptyState.classList.add('hidden');
            previewState.classList.remove('hidden');
        } else {
            previewImage.src = '';
            urlLabel.textContent = '';
            previewState.classList.add('hidden');
            emptyState.classList.remove('hidden');
        }
    };
=======
    const el = document.querySelector('#editor');

    if (!el || editorInstance) {
        return;
    }
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783

    const setStatus = (message, tone = 'default') => {
        const toneClass = {
            default: 'text-gray-400',
            success: 'text-emerald-400',
            error: 'text-red-400',
            loading: 'text-yellow-300',
        };

        statusLabel.className = `mt-3 text-sm ${toneClass[tone] || toneClass.default}`;
        statusLabel.textContent = message || '';
    };

    const syncSubmitState = () => {
        submitButton.disabled = isUploadingThumbnail;

        if (isUploadingThumbnail) {
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải thumbnail...';
        } else {
            submitButton.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Lưu bài viết';
        }
    };

    const uploadThumbnail = async (file) => {
        if (!file) {
            return;
        }

        const formData = new FormData();
        formData.append('thumbnail', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        isUploadingThumbnail = true;
        syncSubmitState();
        setStatus('Đang tải ảnh lên Cloudinary...', 'loading');

        try {
            const response = await fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Upload thất bại.');
            }
<<<<<<< HEAD

            setThumbnail(payload.url);
            setStatus(payload.message || 'Tải thumbnail thành công.', 'success');
        } catch (error) {
            setStatus(error.message || 'Không thể tải ảnh.', 'error');
        } finally {
            isUploadingThumbnail = false;
            syncSubmitState();
            fileInput.value = '';
        }
    };

    pickerButton?.addEventListener('click', () => fileInput.click());

    dropzone?.addEventListener('click', (event) => {
        if (event.target.closest('#removeThumbnailButton')) {
            return;
        }

        if (!hiddenInput.value) {
            fileInput.click();
        }
    });

    fileInput?.addEventListener('change', (event) => {
        uploadThumbnail(event.target.files[0]);
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone?.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.add('border-red-500');
=======
        })
        .then(editor => {
            editorInstance = editor;
            console.log('CKEditor READY');
        })
        .catch(error => {
            console.error(error);
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropzone?.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.remove('border-red-500');
        });
    });

    dropzone?.addEventListener('drop', (event) => {
        const [file] = event.dataTransfer.files || [];
        uploadThumbnail(file);
    });

    removeButton?.addEventListener('click', () => {
        setThumbnail('');
        setStatus('Đã gỡ thumbnail khỏi bài viết.', 'default');
    });

    form?.addEventListener('submit', (event) => {
        if (isUploadingThumbnail) {
            event.preventDefault();
            setStatus('Chờ upload thumbnail hoàn tất rồi hãy lưu bài viết.', 'loading');
        }
    });

    setThumbnail(hiddenInput.value);
    syncSubmitState();
});
</script>

<style>
.ck.ck-editor {
    border-radius: 1.5rem;
    overflow: hidden;
}

.ck.ck-toolbar {
    border: 1px solid rgb(55 65 81) !important;
    border-bottom: 0 !important;
    background: #111827 !important;
}

.ck.ck-content {
    border: 1px solid rgb(55 65 81) !important;
    border-top: 0 !important;
    border-bottom-left-radius: 1.5rem !important;
    border-bottom-right-radius: 1.5rem !important;
}

.ck.ck-button,
.ck.ck-button .ck-icon {
    color: #d1d5db !important;
}

.ck.ck-button:hover,
.ck.ck-button.ck-on {
    background: rgba(239, 68, 68, 0.14) !important;
    color: #ffffff !important;
}

.ck.ck-dropdown__panel,
.ck.ck-list {
    background: #111827 !important;
    border-color: rgb(55 65 81) !important;
}

.ck-editor__editable {
    min-height: 320px;
    background: #0b1120 !important;
    color: #ffffff !important;
    border-radius: 0 0 1.5rem 1.5rem !important;
}

.ck-editor__editable:focus {
    box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.45) !important;
}
</style>
@endsection
