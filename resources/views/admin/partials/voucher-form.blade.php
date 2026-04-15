{{--
    Partial: admin.partials.voucher-form
    Biến truyền vào: $v (App\Models\Voucher|null)
--}}

{{-- Code --}}
<div>
    <label class="mb-1.5 block text-sm font-semibold text-gray-300">Mã Voucher <span class="text-red-400">*</span></label>
    <input type="text" name="code" value="{{ old('code', $v?->code) }}"
           placeholder="VD: CINEMA2024"
           class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 font-mono text-sm uppercase text-white placeholder-gray-600 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
</div>

{{-- Mô tả --}}
<div>
    <label class="mb-1.5 block text-sm font-semibold text-gray-300">Mô tả</label>
    <input type="text" name="description" value="{{ old('description', $v?->description) }}"
           placeholder="VD: Giảm 20% dịp lễ 30/4"
           class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
</div>

{{-- Loại giảm + Giá trị --}}
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-semibold text-gray-300">Loại giảm <span class="text-red-400">*</span></label>
        <select name="discount_type" id="discount_type_{{ $v?->id ?? 'new' }}"
                onchange="toggleDiscountFields(this)"
                class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none appearance-none">
            <option value="value" {{ old('discount_type', $v?->discount_value !== null ? 'value' : 'rate') === 'value' ? 'selected' : '' }}>
                Số tiền (VND)
            </option>
            <option value="rate" {{ old('discount_type', $v?->discount_rate !== null ? 'rate' : 'value') === 'rate' ? 'selected' : '' }}>
                Phần trăm (%)
            </option>
        </select>
    </div>

    <div id="field_value_{{ $v?->id ?? 'new' }}"
         class="{{ old('discount_type', $v?->discount_rate !== null ? 'rate' : 'value') === 'rate' ? 'hidden' : '' }}">
        <label class="mb-1.5 block text-sm font-semibold text-gray-300">Số tiền giảm (VND)</label>
        <input type="number" name="discount_value" value="{{ old('discount_value', $v?->discount_value) }}"
               min="0" placeholder="VD: 50000"
               class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
    </div>

    <div id="field_rate_{{ $v?->id ?? 'new' }}"
         class="{{ old('discount_type', $v?->discount_rate !== null ? 'rate' : 'value') === 'value' ? 'hidden' : '' }}">
        <label class="mb-1.5 block text-sm font-semibold text-gray-300">Phần trăm giảm (%)</label>
        <input type="number" name="discount_rate" value="{{ old('discount_rate', $v?->discount_rate) }}"
               min="1" max="100" placeholder="VD: 20"
               class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
    </div>
</div>

{{-- Thời gian --}}
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-semibold text-gray-300">Ngày bắt đầu</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', $v?->starts_at?->format('Y-m-d')) }}"
               class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 [color-scheme:dark]">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-semibold text-gray-300">Ngày hết hạn</label>
        <input type="date" name="expires_at" value="{{ old('expires_at', $v?->expires_at?->format('Y-m-d')) }}"
               class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 [color-scheme:dark]">
    </div>
</div>

{{-- Giới hạn dùng + Trạng thái --}}
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-semibold text-gray-300">Giới hạn lượt dùng</label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit', $v?->usage_limit) }}"
               min="0" placeholder="Để trống = không giới hạn"
               class="w-full rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
    </div>
    <div class="flex items-end pb-1">
        <label class="flex cursor-pointer items-center gap-3">
            <div class="relative">
                <input type="checkbox" name="is_active" value="1" class="peer sr-only"
                       {{ old('is_active', $v?->is_active ?? true) ? 'checked' : '' }}>
                <div class="h-7 w-12 rounded-full bg-gray-700 transition peer-checked:bg-red-600"></div>
                <div class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></div>
            </div>
            <span class="text-sm font-semibold text-gray-300">Kích hoạt Voucher</span>
        </label>
    </div>
</div>

<script>
function toggleDiscountFields(sel) {
    const suffix = sel.id.replace('discount_type_', '');
    const valField  = document.getElementById('field_value_' + suffix);
    const rateField = document.getElementById('field_rate_'  + suffix);
    if (!valField || !rateField) return;
    if (sel.value === 'rate') {
        valField.classList.add('hidden');
        rateField.classList.remove('hidden');
    } else {
        rateField.classList.add('hidden');
        valField.classList.remove('hidden');
    }
}
</script>
