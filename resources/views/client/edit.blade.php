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
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i class="mdi mdi-card-account-details-outline"></i></span>
                                                    <input type="text" name="cnic" id="client_cnic"
                                                        value="{{ old('cnic', $client->cnic) }}" class="form-control"
                                                        placeholder="42101-2285325-7" maxlength="15" autocomplete="off">
                                                </div>
                                                <small class="text-muted fs-11">Format: 42101-2285325-7 (Dashes are added automatically)</small>
                                                @error('cnic')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact / Mobile No</label>
                                                @php
                                                    $storedPhone = old('phone', $client->phone ?? '');
                                                    $selectedDial = old('country_dial_code', '+92');
                                                    $dialCodes = ['+966', '+971', '+91', '+44', '+1', '+968', '+965', '+974', '+973', '+880', '+60', '+62', '+90', '+20', '+27', '+61', '+49', '+33', '+39', '+92'];
                                                    foreach ($dialCodes as $code) {
                                                        if (str_starts_with($storedPhone, $code)) {
                                                            $selectedDial = $code;
                                                            $storedPhone = trim(substr($storedPhone, strlen($code)));
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted"><i class="mdi mdi-whatsapp text-success"></i></span>
                                                    <select name="country_dial_code" id="country_dial_code" class="form-select flex-shrink-0" style="max-width: 145px; background-color:#f8fafc; font-weight:600;">
                                                        <option value="+92" {{ $selectedDial == '+92' ? 'selected' : '' }}>🇵🇰 +92 (PK)</option>
                                                        <option value="+966" {{ $selectedDial == '+966' ? 'selected' : '' }}>🇸🇦 +966 (SA)</option>
                                                        <option value="+971" {{ $selectedDial == '+971' ? 'selected' : '' }}>🇦🇪 +971 (AE)</option>
                                                        <option value="+91" {{ $selectedDial == '+91' ? 'selected' : '' }}>🇮🇳 +91 (IN)</option>
                                                        <option value="+44" {{ $selectedDial == '+44' ? 'selected' : '' }}>🇬🇧 +44 (UK)</option>
                                                        <option value="+1" {{ $selectedDial == '+1' ? 'selected' : '' }}>🇺🇸 +1 (US/CA)</option>
                                                        <option value="+968" {{ $selectedDial == '+968' ? 'selected' : '' }}>🇴🇲 +968 (OM)</option>
                                                        <option value="+965" {{ $selectedDial == '+965' ? 'selected' : '' }}>🇰🇼 +965 (KW)</option>
                                                        <option value="+974" {{ $selectedDial == '+974' ? 'selected' : '' }}>🇶🇦 +974 (QA)</option>
                                                        <option value="+973" {{ $selectedDial == '+973' ? 'selected' : '' }}>🇧🇭 +973 (BH)</option>
                                                        <option value="+880" {{ $selectedDial == '+880' ? 'selected' : '' }}>🇧🇩 +880 (BD)</option>
                                                        <option value="+60" {{ $selectedDial == '+60' ? 'selected' : '' }}>🇲🇾 +60 (MY)</option>
                                                        <option value="+62" {{ $selectedDial == '+62' ? 'selected' : '' }}>🇮🇩 +62 (ID)</option>
                                                        <option value="+90" {{ $selectedDial == '+90' ? 'selected' : '' }}>🇹🇷 +90 (TR)</option>
                                                        <option value="+20" {{ $selectedDial == '+20' ? 'selected' : '' }}>🇪🇬 +20 (EG)</option>
                                                        <option value="+27" {{ $selectedDial == '+27' ? 'selected' : '' }}>🇿🇦 +27 (ZA)</option>
                                                        <option value="+61" {{ $selectedDial == '+61' ? 'selected' : '' }}>🇦🇺 +61 (AU)</option>
                                                        <option value="+49" {{ $selectedDial == '+49' ? 'selected' : '' }}>🇩🇪 +49 (DE)</option>
                                                        <option value="+33" {{ $selectedDial == '+33' ? 'selected' : '' }}>🇫🇷 +33 (FR)</option>
                                                        <option value="+39" {{ $selectedDial == '+39' ? 'selected' : '' }}>🇮🇹 +39 (IT)</option>
                                                    </select>
                                                    <input type="text" name="phone" id="client_phone"
                                                        value="{{ $storedPhone }}" class="form-control"
                                                        placeholder="3XX XXXXXXX or local number" maxlength="20" autocomplete="off">
                                                </div>
                                                <small class="text-muted fs-11">Select country dial code from dropdown (supports Pakistan +92, India +91, Saudi +966, etc.).</small>
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
        const cnicInput = document.getElementById('client_cnic');
        const phoneInput = document.getElementById('client_phone');

        // --- CNIC Auto Formatting (XXXXX-XXXXXXX-X) ---
        function formatCNIC(val) {
            if (!val) return '';
            let digits = val.replace(/\D/g, '').substring(0, 13);
            let res = '';
            if (digits.length > 0) {
                res = digits.substring(0, Math.min(5, digits.length));
                if (digits.length > 5) {
                    res += '-' + digits.substring(5, Math.min(12, digits.length));
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

        // --- Contact No & Country Dial Code Support ---
        const dialSelect = document.getElementById('country_dial_code');

        if (phoneInput) {
            // Auto-detect country code if pasted with +
            phoneInput.addEventListener('input', function() {
                let val = this.value.trim();
                if (val.startsWith('+')) {
                    if (dialSelect) {
                        for (let opt of dialSelect.options) {
                            if (val.startsWith(opt.value)) {
                                dialSelect.value = opt.value;
                                this.value = val.substring(opt.value.length).trim();
                                break;
                            }
                        }
                    }
                }
            });
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
