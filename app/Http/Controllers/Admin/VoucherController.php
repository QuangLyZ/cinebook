<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VoucherController extends Controller
{
    protected array $adminTabs = [
        'dashboard' => 'Dashboard',
        'management' => 'Quản lý',
        'posts' => 'Bài viết',
        'actions' => 'Action',
        'settings' => 'Cài đặt',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $editId = $request->integer('edit');

        $query = Voucher::query()->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('is_active', $status === 'active');
        }

        $vouchers = $query->paginate(8)->withQueryString();
        $editingVoucher = $editId ? Voucher::find($editId) : null;

        $stats = [
            'total' => Voucher::count(),
            'active' => Voucher::where('is_active', true)->count(),
            'expired' => Voucher::whereNotNull('expires_at')->where('expires_at', '<', now())->count(),
            'usage_cap' => Voucher::whereNotNull('usage_limit')->count(),
        ];

        return view('admin.home', [
            'activeTab' => 'actions',
            'pageTitle' => $this->adminTabs['actions'],
            'adminTabs' => $this->adminTabs,
            'vouchers' => $vouchers,
            'editingVoucher' => $editingVoucher,
            'voucherStats' => $stats,
            'voucherFilters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Voucher::create($this->validatedData($request));

        return redirect()
            ->route('admin.actions')
            ->with('success', 'Voucher đã được tạo thành công.');
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $voucher->update($this->validatedData($request, $voucher));

        return redirect()
            ->route('admin.actions')
            ->with('success', 'Voucher đã được cập nhật.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();

        return redirect()
            ->route('admin.actions')
            ->with('success', 'Voucher đã được xóa.');
    }

    protected function validatedData(Request $request, ?Voucher $voucher = null): array
    {
        $normalizedCode = strtoupper(trim((string) $request->input('code')));
        $request->merge(['code' => $normalizedCode]);

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers', 'code')->ignore($voucher?->id),
            ],
            'discount_type' => ['required', Rule::in(['value', 'rate'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_rate' => ['nullable', 'integer', 'between:1,100'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Mã voucher là bắt buộc.',
            'code.unique' => 'Mã voucher này đã tồn tại.',
            'discount_type.required' => 'Chọn loại giảm giá.',
            'discount_rate.between' => 'Phần trăm giảm phải từ 1 đến 100.',
            'expires_at.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        if ($data['discount_type'] === 'value') {
            $request->validate([
                'discount_value' => ['required', 'numeric', 'min:0'],
            ], [
                'discount_value.required' => 'Nhập số tiền giảm cho voucher.',
            ]);
            $data['discount_rate'] = null;
        } else {
            $request->validate([
                'discount_rate' => ['required', 'integer', 'between:1,100'],
            ], [
                'discount_rate.required' => 'Nhập phần trăm giảm cho voucher.',
            ]);
            $data['discount_value'] = null;
        }

        unset($data['discount_type']);

        $data['code'] = $normalizedCode;
        $data['is_active'] = $request->boolean('is_active');
        $data['used_count'] = $voucher?->used_count ?? 0;

        return $data;
    }
}
