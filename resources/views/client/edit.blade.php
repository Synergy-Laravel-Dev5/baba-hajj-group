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
                                Update Client Informati
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
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="mdi mdi-card-account-details-outline"></i></span>
                                                <input type="text" name="cnic" id="client_cnic"
                                                    value="{{ old('cnic', $client->cnic) }}" class="form-control"
                                                    placeholder="42101-2285325-7" maxlength="15" autocomplete="off">
                                            </div>
                                            @error('cnic')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact No</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="mdi mdi-whatsapp text-success"></i></span>
                                                <input type="text" name="phone" id="client_phone"
                                                    value="{{ old('phone', $client->phone) }}" class="form-control"
                                                    placeholder="+92 334 5409691" maxlength="16" autocomplete="off">
                                            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const cnicInput = document.getElementById('client_cnic');
        const phoneInput = document.getElementById('client_phone');

        // ==========================================
        // CNIC Auto Formatter: XXXXX-XXXXXXX-X
        // ==========================================
        function formatCnic(val) {
            if (!val) return '';
            const digits = val.replace(/\D/g, '').slice(0, 13);
            let res = '';
            if (digits.length > 0) {
                res += digits.slice(0, Math.min(5, digits.length));
            }
            if (digits.length > 5) {
                res += '-' + digits.slice(5, Math.min(12, digits.length));
            }
            if (digits.length > 12) {
                res += '-' + digits.slice(12, 13);
            }
            return res;
        }

        if (cnicInput) {
            if (cnicInput.value) {
                cnicInput.value = formatCnic(cnicInput.value);
            }

            cnicInput.addEventListener('input', function() {
                this.value = formatCnic(this.value);
            });

            cnicInput.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    const pos = this.selectionStart;
                    if (pos === this.selectionEnd && (pos === 6 || pos === 14)) {
                        e.preventDefault();
                        const val = this.value;
                        const newVal = val.slice(0, pos - 2) + val.slice(pos);
                        this.value = formatCnic(newVal);
                        const newPos = Math.max(0, pos - 2);
                        this.setSelectionRange(newPos, newPos);
                    }
                }
            });
        }

        // ==========================================
        // Phone Auto Formatter: +92 3XX XXXXXXX
        // ==========================================
        function formatPhone(val) {
            if (!val) return '';
            let digits = val.replace(/\D/g, '');

            if (digits.startsWith('92')) {
                digits = digits.substring(2);
            }
            if (digits.startsWith('0')) {
                digits = digits.substring(1);
            }

            digits = digits.substring(0, 10);

            if (digits.length === 0) {
                return val.includes('+92') ? '+92 ' : '';
            }

            let res = '+92 ';
            if (digits.length <= 3) {
                res += digits;
            } else {
                res += digits.substring(0, 3) + ' ' + digits.substring(3);
            }
            return res;
        }

        if (phoneInput) {
            if (phoneInput.value && phoneInput.value.trim() !== '') {
                const pureDigits = phoneInput.value.replace(/\D/g, '').replace(/^92/, '');
                phoneInput.value = pureDigits.length > 0 ? formatPhone(phoneInput.value) : '';
            }

            phoneInput.addEventListener('focus', function() {
                if (!this.value.trim()) {
                    this.value = '+92 ';
                }
            });

            phoneInput.addEventListener('blur', function() {
                const pureDigits = this.value.replace(/\D/g, '').replace(/^92/, '');
                if (pureDigits.length === 0) {
                    this.value = '';
                }
            });

            phoneInput.addEventListener('input', function() {
                const oldVal = this.value;
                const pureDigits = oldVal.replace(/\D/g, '').replace(/^92/, '');

                if (oldVal === '' || oldVal === '+' || oldVal === '+9') {
                    this.value = '';
                    return;
                }

                if (pureDigits.length === 0 && (oldVal === '+92' || oldVal === '+92 ')) {
                    this.value = '+92 ';
                    return;
                }

                this.value = formatPhone(oldVal);
            });

            phoneInput.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    const pos = this.selectionStart;
                    const pureDigits = this.value.replace(/\D/g, '').replace(/^92/, '');

                    if (pureDigits.length === 0) {
                        e.preventDefault();
                        this.value = '';
                    } else if (pos === this.selectionEnd && pos === 9 && this.value.charAt(8) === ' ') {
                        e.preventDefault();
                        const val = this.value;
                        const newVal = val.slice(0, 7) + val.slice(9);
                        this.value = formatPhone(newVal);
                        this.setSelectionRange(7, 7);
                    }
                }
            });

            const parentForm = phoneInput.closest('form');
            if (parentForm) {
                parentForm.addEventListener('submit', function() {
                    const pureDigits = phoneInput.value.replace(/\D/g, '').replace(/^92/, '');
                    if (pureDigits.length === 0) {
                        phoneInput.value = '';
                    }
                });
            }
        }
    });
</script>
@endpush