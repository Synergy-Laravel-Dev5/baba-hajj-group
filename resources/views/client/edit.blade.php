@extends('layout.master')
@section('title', 'Edit Client')
@section('header-title', 'Edit Client')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container">
                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">Edit Client</h4>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="mdi mdi-account-edit me-2 text-primary"></i>
                                    Update Client Information
                                </h5>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $e)
                                                <li>{{ $e }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form action="{{ route('client.update', $client->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name"
                                                    value="{{ old('name', $client->name) }}" class="form-control"
                                                    placeholder="Enter client name" required>
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">CNIC</label>
                                                <input type="text" name="cnic" id="client_cnic"
                                                    value="{{ old('cnic', $client->cnic) }}" class="form-control"
                                                    placeholder="42101-2285325-7" maxlength="15">
                                                @error('cnic')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact No</label>
                                                <input type="text" name="phone" id="client_phone"
                                                    value="{{ old('phone', $client->phone) }}" class="form-control"
                                                    placeholder="+92 334 5409691" maxlength="16">
                                                @error('phone')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Referral</label>
                                                <select name="company_id" class="form-select select2" data-placeholder="-- Select Referral --">
                                                    <option value="">-- Select Referral --</option>
                                                    @foreach ($companies as $comp)
                                                        <option value="{{ $comp->id }}"
                                                            {{ old('company_id', $client->company_id) == $comp->id ? 'selected' : '' }}>
                                                            {{ $comp->company_name ?? $comp->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('company_id')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 text-end">
                                            <a href="{{ route('client.index') }}" class="btn btn-secondary">
                                                Cancel
                                            </a>
                                            <button type="submit" class="btn btn-success px-4">
                                                <i class="mdi mdi-content-save me-1"></i> Update Client
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function() {
    function initClientFormatting() {
        // --- CNIC Auto Formatting (XXXXX-XXXXXXX-X) ---
        const cnicInput = document.getElementById('client_cnic');
        function formatCNIC(val) {
            if (!val) return '';
            let digits = val.replace(/\D/g, '').substring(0, 13);
            let res = '';
            if (digits.length > 0) {
                res = digits.substring(0, 5);
                if (digits.length > 5) {
                    res += '-' + digits.substring(5, 12);
                    if (digits.length > 12) {
                        res += '-' + digits.substring(12, 13);
                    }
                }
            }
            return res;
        }

        if (cnicInput) {
            if (cnicInput.value) {
                cnicInput.value = formatCNIC(cnicInput.value);
            }

            cnicInput.addEventListener('input', function() {
                this.value = formatCNIC(this.value);
            });

            cnicInput.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && (this.selectionStart === 6 || this.selectionStart === 14) && this.selectionStart === this.selectionEnd) {
                    const pos = this.selectionStart;
                    this.value = this.value.substring(0, pos - 1) + this.value.substring(pos);
                    this.value = formatCNIC(this.value);
                    this.setSelectionRange(pos - 1, pos - 1);
                    e.preventDefault();
                }
            });
        }

        // --- Contact No Auto Formatting (+92 3XX XXXXXXX) ---
        const phoneInput = document.getElementById('client_phone');
        function formatPhone(val) {
            if (!val) return '';
            let digits = val.replace(/\D/g, '');

            if (digits.startsWith('92')) {
                digits = digits.substring(2);
            } else if (digits.startsWith('0')) {
                digits = digits.substring(1);
            }

            digits = digits.substring(0, 10);

            if (digits.length === 0) {
                return val.includes('+') ? '+92 ' : '';
            }

            let res = '+92 ' + digits.substring(0, 3);
            if (digits.length > 3) {
                res += ' ' + digits.substring(3, 10);
            }
            return res;
        }

        if (phoneInput) {
            if (phoneInput.value) {
                phoneInput.value = formatPhone(phoneInput.value);
            }

            phoneInput.addEventListener('focus', function() {
                if (!this.value.trim()) {
                    this.value = '+92 ';
                }
            });

            phoneInput.addEventListener('input', function() {
                this.value = formatPhone(this.value);
            });

            phoneInput.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && (this.value === '+92 ' || this.value === '+92' || this.value === '+9')) {
                    e.preventDefault();
                    this.value = '';
                }
            });

            phoneInput.addEventListener('blur', function() {
                let digits = this.value.replace(/\D/g, '');
                if (digits === '92' || digits === '') {
                    this.value = '';
                }
            });

            const form = phoneInput.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    let digits = phoneInput.value.replace(/\D/g, '');
                    if (digits === '92' || digits === '') {
                        phoneInput.value = '';
                    }
                });
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClientFormatting);
    } else {
        initClientFormatting();
    }
})();
</script>
@endpush
