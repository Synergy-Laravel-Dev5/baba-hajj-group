@extends('layout.master')
@section('title', 'Create Client Invoice')
@section('content')
@php
    $selectedClientId = $selectedClientId ?? null;
    $prefillBillTo = $prefillBillTo ?? '';
@endphp

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-18 fw-semibold m-0">Create Client Invoice</h4>
                    <p class="text-muted fs-12 mb-0">Generate official client invoice with itemized breakdown and payment details</p>
                </div>
                <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm">
                    &larr; Back to Invoices
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('invoice.store') }}" method="POST" id="invoiceForm">
                @csrf

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 fw-semibold text-primary">
                            <i class="mdi mdi-receipt me-1"></i> Invoice Details
                        </h5>
                        <span class="badge bg-soft-info text-info border border-info px-3 py-1 fs-12">
                            Official Invoice Template
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            <!-- Client Selection -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Client / Customer <span class="text-danger">*</span>
                                </label>
                                <select name="client_id" id="client_select" class="form-select @error('client_id') is-invalid @enderror" required>
                                    <option value="">-- Select Client --</option>
                                    @foreach($clients as $c)
                                        <option value="{{ $c->id }}" 
                                                data-name="{{ $c->name }}"
                                                data-company="{{ $c->company_name }}"
                                                data-phone="{{ $c->phone }}"
                                                data-email="{{ $c->email }}"
                                                data-passport="{{ $c->passport_number }}"
                                                data-cnic="{{ $c->cnic }}"
                                                {{ (old('client_id', $selectedClientId) == $c->id) ? 'selected' : '' }}>
                                            {{ $c->name }} {{ $c->phone ? '(' . $c->phone . ')' : '' }} {{ $c->company_name ? '— ' . $c->company_name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Invoice Number -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Invoice # <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="invoice_no" class="form-control fw-bold text-primary @error('invoice_no') is-invalid @enderror" 
                                       value="{{ old('invoice_no', $nextInvoiceNo) }}" required>
                                @error('invoice_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Invoice Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                       value="{{ old('invoice_date', $today) }}" required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Due Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                                       value="{{ old('due_date', $today) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Currency -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Currency <span class="text-danger">*</span></label>
                                <select name="currency" id="currency_select" class="form-select @error('currency') is-invalid @enderror">
                                    @foreach($currencies as $code => $label)
                                        <option value="{{ $code }}" {{ old('currency', 'PKR') == $code ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bill To Textarea -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    Bill To (Client / Passenger Details) <span class="text-danger">*</span>
                                </label>
                                <textarea name="bill_to" id="bill_to_input" class="form-control @error('bill_to') is-invalid @enderror" 
                                          rows="3" placeholder="Client details reflect automatically when selected above..." required>{{ old('bill_to', $prefillBillTo) }}</textarea>
                                @error('bill_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Invoice Line Items Table -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 fw-semibold text-primary">
                            <i class="mdi mdi-format-list-numbered me-1"></i> Item & Description
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_item_btn">
                            <i class="mdi mdi-plus me-1"></i> Add Another Row
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="items_table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Item & Description <span class="text-danger">*</span></th>
                                        <th style="width: 120px;" class="text-center">Qty</th>
                                        <th style="width: 180px;" class="text-end">Rate (<span class="currency-label">PKR</span>)</th>
                                        <th style="width: 180px;" class="text-end">Amount (<span class="currency-label">PKR</span>)</th>
                                        <th style="width: 50px;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="items_tbody">
                                    @php
                                        $oldItems = old('items', [
                                            [
                                                'item_name'   => '',
                                                'description' => '',
                                                'qty'         => 1.00,
                                                'rate'        => 0.00,
                                            ]
                                        ]);
                                    @endphp

                                    @foreach($oldItems as $idx => $item)
                                        <tr class="item-row">
                                            <td class="text-center fw-bold row-number align-top pt-3">{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="text" name="items[{{ $idx }}][item_name]" class="form-control form-control-sm mb-2 fw-semibold" 
                                                       placeholder="Service / Package Title" 
                                                       value="{{ $item['item_name'] ?? '' }}">
                                                <textarea name="items[{{ $idx }}][description]" class="form-control form-control-sm item-desc" 
                                                          rows="5" placeholder="Description / Inclusions / Details...">{{ $item['description'] ?? '' }}</textarea>
                                            </td>
                                            <td class="align-top">
                                                <input type="number" step="0.01" min="0.01" name="items[{{ $idx }}][qty]" class="form-control form-control-sm text-center item-qty" 
                                                       value="{{ $item['qty'] ?? 1.00 }}" required>
                                            </td>
                                            <td class="align-top">
                                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][rate]" class="form-control form-control-sm text-end item-rate" 
                                                       value="{{ $item['rate'] ?? 0.00 }}" required>
                                            </td>
                                            <td class="align-top text-end fw-bold pt-3 item-amount">
                                                0.00
                                            </td>
                                            <td class="text-center align-top pt-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" title="Remove Row">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totals Section -->
                    <div class="card-footer bg-white border-top p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Notes (optional)...">{{ old('notes') }}</textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Terms & Conditions</label>
                                    <textarea name="terms_conditions" class="form-control" rows="3" placeholder="Terms & Conditions (optional)...">{{ old('terms_conditions') }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Sub Total</span>
                                        <span class="fs-15 fw-bold" id="sub_total_display">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
                                        <span class="fw-semibold text-danger">
                                            <i class="mdi mdi-tag-outline me-1"></i> Discount (-)
                                        </span>
                                        <div style="width: 180px;">
                                            <input type="number" step="0.01" min="0" name="discount" id="discount_input" 
                                                   class="form-control form-control-sm text-end fw-semibold text-danger" 
                                                   placeholder="0.00"
                                                   value="{{ old('discount', 0.00) }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
                                        <span class="fs-16 fw-bold">Total (<span class="currency-label">PKR</span>)</span>
                                        <span class="fs-18 fw-bold text-primary" id="total_display">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
                                        <span class="fw-semibold text-danger">Payment Made (-)</span>
                                        <div style="width: 180px;">
                                            <input type="number" step="0.01" min="0" name="payment_made" id="payment_made_input" 
                                                   class="form-control form-control-sm text-end fw-semibold text-danger" 
                                                   value="{{ old('payment_made', 0.00) }}">
                                        </div>
                                    </div>
                                    <div class="mb-2 pt-2 border-top">
                                        <label class="form-label fw-semibold fs-12 mb-1 d-flex justify-content-between align-items-center">
                                            <span><i class="mdi mdi-bank me-1 text-primary"></i> Bank Account</span>
                                            <span class="badge bg-success-subtle text-success" style="font-size:10px;">Active Only</span>
                                        </label>
                                        <select name="bank_id" class="form-select form-select-sm @error('bank_id') is-invalid @enderror">
                                            <option value="">-- No Bank Selected / Cash Payment --</option>
                                            @foreach($banks as $b)
                                                <option value="{{ $b->id }}" {{ old('bank_id') == $b->id ? 'selected' : '' }}>
                                                    {{ $b->name }}{{ $b->account_name ? ' — ' . $b->account_name : '' }}{{ $b->account_number ? ' (' . $b->account_number . ')' : '' }}{{ $b->branch ? ' [' . $b->branch . ']' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-1" style="font-size:11px;">Selected bank payment instructions will appear on the invoice and PDF for the client.</small>
                                        @error('bank_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="fs-16 fw-bold text-dark">Balance Due (<span class="currency-label">PKR</span>)</span>
                                        <span class="fs-20 fw-bold text-danger" id="balance_due_display">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('invoice.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 fs-14 fw-semibold">
                        <i class="mdi mdi-check-circle me-1"></i> Save & Generate Invoice
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
(function () {
    function initInvoiceForm() {
        const tbody = document.getElementById('items_tbody');
        const addBtn = document.getElementById('add_item_btn');
        const currencySelect = document.getElementById('currency_select');
        const discountInput = document.getElementById('discount_input');
        const paymentMadeInput = document.getElementById('payment_made_input');
        const clientSelect = document.getElementById('client_select');
        const billToInput = document.getElementById('bill_to_input');

        if (!tbody || !addBtn) {
            return;
        }

        // Auto-update Bill To when client is selected
        if (clientSelect) {
            clientSelect.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.value) {
                    let lines = [];
                    const name = opt.getAttribute('data-name');
                    const comp = opt.getAttribute('data-company');
                    const phone = opt.getAttribute('data-phone');
                    const email = opt.getAttribute('data-email');
                    const passport = opt.getAttribute('data-passport');
                    const cnic = opt.getAttribute('data-cnic');

                    if (name) lines.push(name.toUpperCase());
                    if (comp) lines.push(comp);
                    if (phone) lines.push('Contact: ' + phone);
                    if (email) lines.push('Email: ' + email);
                    if (passport) lines.push('Passport: ' + passport);
                    if (cnic) lines.push('CNIC: ' + cnic);

                    if (billToInput) {
                        billToInput.value = lines.join('\n');
                    }
                }
            });
        }

        // Update currency labels
        function updateCurrencyLabels() {
            const cur = currencySelect ? currencySelect.value : 'PKR';
            document.querySelectorAll('.currency-label').forEach(el => el.textContent = cur);
        }
        if (currencySelect) {
            currencySelect.addEventListener('change', updateCurrencyLabels);
            updateCurrencyLabels();
        }

        // Calculate row amounts and overall totals
        function recalculate() {
            let subtotal = 0;
            const rows = tbody.querySelectorAll('.item-row');

            rows.forEach((row, idx) => {
                const numCell = row.querySelector('.row-number');
                if (numCell) numCell.textContent = idx + 1;

                const qtyInput = row.querySelector('.item-qty');
                const rateInput = row.querySelector('.item-rate');
                const amountCell = row.querySelector('.item-amount');

                const qty = parseFloat(qtyInput ? qtyInput.value : 1) || 0;
                const rate = parseFloat(rateInput ? rateInput.value : 0) || 0;
                const amount = qty * rate;

                if (amountCell) {
                    amountCell.textContent = amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                subtotal += amount;
            });

            const discount = parseFloat(discountInput ? discountInput.value : 0) || 0;
            const paymentMade = parseFloat(paymentMadeInput ? paymentMadeInput.value : 0) || 0;
            const total = Math.max(0, subtotal - discount);
            const balanceDue = Math.max(0, total - paymentMade);

            const subTotalEl = document.getElementById('sub_total_display');
            if (subTotalEl) subTotalEl.textContent = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const totalEl = document.getElementById('total_display');
            if (totalEl) totalEl.textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const balanceDueEl = document.getElementById('balance_due_display');
            if (balanceDueEl) balanceDueEl.textContent = balanceDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Attach listeners on table inputs
        tbody.addEventListener('input', function (e) {
            if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-rate')) {
                recalculate();
            }
        });

        if (discountInput) {
            discountInput.addEventListener('input', recalculate);
        }

        if (paymentMadeInput) {
            paymentMadeInput.addEventListener('input', recalculate);
        }

        // Remove row
        tbody.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-item-btn');
            if (removeBtn) {
                e.preventDefault();
                const rows = tbody.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    removeBtn.closest('.item-row').remove();
                    recalculate();
                } else {
                    alert('At least one item row is required.');
                }
            }
        });

        // Add item row
        let rowCounter = tbody.querySelectorAll('.item-row').length + Date.now();
        addBtn.addEventListener('click', function (e) {
            e.preventDefault();
            rowCounter++;
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <td class="text-center fw-bold row-number align-top pt-3"></td>
                <td>
                    <input type="text" name="items[${rowCounter}][item_name]" class="form-control form-control-sm mb-2 fw-semibold" placeholder="Service / Package Title">
                    <textarea name="items[${rowCounter}][description]" class="form-control form-control-sm item-desc" rows="5" placeholder="Description / Inclusions / Details..."></textarea>
                </td>
                <td class="align-top">
                    <input type="number" step="0.01" min="0.01" name="items[${rowCounter}][qty]" class="form-control form-control-sm text-center item-qty" value="1.00" required>
                </td>
                <td class="align-top">
                    <input type="number" step="0.01" min="0" name="items[${rowCounter}][rate]" class="form-control form-control-sm text-end item-rate" value="0.00" required>
                </td>
                <td class="align-top text-end fw-bold pt-3 item-amount">0.00</td>
                <td class="text-center align-top pt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" title="Remove Row">
                        <i class="mdi mdi-close"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            recalculate();
        });

        // Initial calculation
        recalculate();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInvoiceForm);
    } else {
        initInvoiceForm();
    }
})();
</script>

@endsection
