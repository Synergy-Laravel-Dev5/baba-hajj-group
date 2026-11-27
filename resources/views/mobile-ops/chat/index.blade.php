@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold m-0 text-dark">Live Mobile App Chat & Bookings</h4>
            <p class="text-muted fs-13 mb-0">Communicate in real-time with pilgrims, app users, and booking applicants.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($totalUnreadCount > 0)
                <span class="badge bg-danger rounded-pill px-3 py-2 fs-12 shadow-sm">
                    <i class="mdi mdi-bell-ring-outline me-1"></i> {{ $totalUnreadCount }} Unread Message(s)
                </span>
            @endif
            <span class="badge bg-light text-dark border px-3 py-2 fs-12">
                <i class="mdi mdi-account-group-outline me-1 text-primary"></i> {{ $users->count() }} Conversations / Bookings
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm overflow-hidden" style="min-height: 640px;">
        <div class="card-body p-0">
            <div class="row g-0">
                <!-- Users / Bookings Conversations List (Left Side) -->
                <div class="col-lg-4 col-md-5 border-end">
                    <div class="p-3 border-bottom bg-light">
                        <div class="position-relative">
                            <input type="text" id="chatSearchInput" class="form-control form-control-sm ps-4"
                                placeholder="Search by name, phone, or package...">
                            <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-2 text-muted fs-16"></i>
                        </div>
                    </div>

                    <div class="chat-users-list" style="max-height: 580px; overflow-y: auto;">
                        @forelse($users as $user)
                            @php
                                $isSelected = $selectedUser && $selectedUser->id === $user->id;
                                $lastMsg = $user->chats->first();
                                $booking = $user->inquiries->first();
                                $pkgTitle = $booking?->package?->package_title ?? '';
                            @endphp
                            <a href="{{ route('mobile-ops.chat.index', ['user_id' => $user->id]) }}"
                                class="d-flex align-items-start p-3 text-decoration-none border-bottom user-chat-item transition-all {{ $isSelected ? 'bg-primary-subtle' : '' }}"
                                data-name="{{ strtolower($user->name) }}"
                                data-phone="{{ $user->phone }}"
                                data-package="{{ strtolower($pkgTitle) }}"
                                style="border-left: {{ $isSelected ? '4px solid #0d6efd' : '4px solid transparent' }};">
                                <div class="position-relative me-3 flex-shrink-0">
                                    @if ($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                            class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                            style="width: 44px; height: 44px; font-size: 16px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    @if ($user->unread_chats_count > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 10px;">
                                            {{ $user->unread_chats_count }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="fs-13 fw-bold m-0 text-dark text-truncate">{{ $user->name }}</h6>
                                        <span class="fs-11 text-muted flex-shrink-0 ms-1">
                                            @if ($lastMsg)
                                                {{ $lastMsg->created_at->format('h:i A') }}
                                            @elseif ($booking)
                                                {{ $booking->created_at->format('d M') }}
                                            @endif
                                        </span>
                                    </div>

                                    @if ($booking)
                                        <div class="d-flex align-items-center gap-1 mb-1 flex-wrap">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10 text-truncate" style="max-width: 170px;" title="{{ $pkgTitle ?: 'Package Booking' }}">
                                                <i class="mdi mdi-cube-outline me-1"></i>{{ $pkgTitle ?: 'Booking #' . $booking->id }}
                                            </span>
                                            <span class="badge {{ $booking->status === 'closed' ? 'bg-success-subtle text-success border border-success-subtle' : ($booking->status === 'contacted' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle') }} fs-10">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                    @endif

                                    <p class="fs-12 text-muted mb-1 text-truncate">
                                        @if ($lastMsg)
                                            @if ($lastMsg->sender_type === 'admin')
                                                <span class="text-primary fw-semibold">You: </span>
                                            @endif
                                            {{ $lastMsg->message ?: ($lastMsg->attachment ? '📎 Attachment' : '') }}
                                        @elseif ($booking)
                                            <span class="text-secondary"><i class="mdi mdi-account-group me-1"></i>{{ $booking->pax }} Pax • {{ ucfirst($booking->room_type) }}</span>
                                        @else
                                            <span class="fst-italic text-muted">No messages yet</span>
                                        @endif
                                    </p>
                                    <div class="fs-11 text-muted d-flex align-items-center">
                                        <i class="mdi mdi-phone me-1 text-success"></i>{{ $user->phone }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="mdi mdi-account-off-outline fs-36 d-block mb-2"></i>
                                <p class="mb-0 fs-13">No app users or bookings found yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Conversation Window (Right Side) -->
                <div class="col-lg-8 col-md-7 d-flex flex-column" style="min-height: 640px;">
                    @if ($selectedUser)
                        @php
                            $cleanPhone = preg_replace('/\D/', '', $selectedUser->phone);
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '92' . substr($cleanPhone, 1);
                            } elseif (!str_starts_with($cleanPhone, '92') && strlen($cleanPhone) === 10) {
                                $cleanPhone = '92' . $cleanPhone;
                            }
                            $waGreeting = "Assalam-o-Alaikum " . $selectedUser->name . "! This is Baba Hajj & Umrah Group support.";
                            if ($selectedInquiry) {
                                $pkgName = $selectedInquiry->package?->package_title ?? 'Package';
                                $waGreeting = "Assalam-o-Alaikum *" . $selectedUser->name . "*!\nRegarding your booking inquiry #" . $selectedInquiry->id . " for *" . $pkgName . "* with Baba Hajj Group, how can we assist you?";
                            }
                            $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($waGreeting);
                        @endphp

                        <!-- Chat Header -->
                        <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                @if ($selectedUser->avatar_url)
                                    <img src="{{ $selectedUser->avatar_url }}" alt="{{ $selectedUser->name }}"
                                        class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                        style="width: 44px; height: 44px; font-size: 16px;">
                                        {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h5 class="fs-15 fw-bold m-0 text-dark">{{ $selectedUser->name }}</h5>
                                    <div class="fs-12 text-muted d-flex align-items-center gap-2 mt-1 flex-wrap">
                                        <a href="tel:{{ $selectedUser->phone }}" class="text-decoration-none text-muted">
                                            <i class="mdi mdi-phone text-success me-1"></i>{{ $selectedUser->phone }}
                                        </a>
                                        @if ($selectedUser->email)
                                            <span>•</span>
                                            <a href="mailto:{{ $selectedUser->email }}" class="text-decoration-none text-muted">
                                                <i class="mdi mdi-email text-primary me-1"></i>{{ $selectedUser->email }}
                                            </a>
                                        @endif
                                        <span>•</span>
                                        <span class="badge bg-success-subtle text-success fs-10 text-uppercase">{{ $selectedUser->status }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ $waUrl }}" target="_blank"
                                    class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 shadow-sm"
                                    title="Open WhatsApp Chat">
                                    <i class="mdi mdi-whatsapp fs-15"></i> WhatsApp
                                </a>
                                <a href="{{ route('mobile-ops.inquiries.index') }}"
                                    class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm"
                                    title="View Inquiries Table">
                                    <i class="mdi mdi-calendar-check fs-15"></i> All Bookings
                                </a>
                            </div>
                        </div>

                        <!-- Active Booking Inquiry Ribbon Banner -->
                        @if ($selectedInquiry)
                            <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2"
                                style="background: #F0FDF4; border-left: 4px solid #10B981;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-success text-white fs-11">
                                        <i class="mdi mdi-cube-outline me-1"></i> Booking #{{ $selectedInquiry->id }}
                                    </span>
                                    <span class="fw-bold text-dark fs-13">
                                        {{ $selectedInquiry->package?->package_title ?? 'Package Inquiry' }}
                                    </span>
                                    <span class="badge bg-light text-dark border fs-11">
                                        <i class="mdi mdi-account-group me-1 text-primary"></i> {{ $selectedInquiry->pax }} Pax
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 text-uppercase">
                                        {{ $selectedInquiry->room_type }}
                                    </span>
                                    <span class="badge {{ $selectedInquiry->status === 'closed' ? 'bg-success' : ($selectedInquiry->status === 'contacted' ? 'bg-info' : 'bg-warning text-dark') }} fs-11">
                                        {{ ucfirst($selectedInquiry->status) }}
                                    </span>
                                    <span class="fs-12 text-muted">
                                        <i class="mdi mdi-clock-outline me-1"></i> {{ $selectedInquiry->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-primary fs-11 py-1 px-2"
                                        onclick="document.getElementById('chatMessageInput').value='Assalam-o-Alaikum {{ addslashes($selectedUser->name) }}, regarding your booking #{{ $selectedInquiry->id }} for {{ addslashes($selectedInquiry->package?->package_title ?? 'Package') }} ({{ $selectedInquiry->pax }} Pax, {{ ucfirst($selectedInquiry->room_type) }}): '; document.getElementById('chatMessageInput').focus();">
                                        <i class="mdi mdi-reply me-1"></i> Quick Reply
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Chat Messages History -->
                        <div id="chatMessagesContainer" class="p-4 flex-grow-1"
                            style="max-height: 440px; overflow-y: auto; background-color: #F8FAFC;">
                            @forelse($messages as $msg)
                                @php
                                    $isAdmin = $msg->sender_type === 'admin';
                                @endphp
                                <div class="d-flex mb-3 {{ $isAdmin ? 'justify-content-end' : 'justify-content-start' }}">
                                    @if (!$isAdmin)
                                        <div class="me-2 mt-1 flex-shrink-0">
                                            @if ($selectedUser->avatar_url)
                                                <img src="{{ $selectedUser->avatar_url }}" class="rounded-circle"
                                                    style="width: 30px; height: 30px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fs-11 fw-bold"
                                                    style="width: 30px; height: 30px;">
                                                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div style="max-width: 75%;">
                                        <div class="p-3 shadow-sm {{ $isAdmin ? 'bg-primary text-white' : 'bg-white text-dark border' }}"
                                            style="border-radius: 14px; {{ $isAdmin ? 'border-bottom-right-radius: 2px;' : 'border-bottom-left-radius: 2px;' }}">
                                            @if ($msg->message)
                                                <div class="fs-13" style="white-space: pre-wrap; word-break: break-word;">{{ $msg->message }}</div>
                                            @endif

                                            @if ($msg->attachment_url)
                                                <div class="mt-2 pt-2 border-top {{ $isAdmin ? 'border-white-50' : 'border-light' }}">
                                                    @if (preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $msg->attachment))
                                                        <a href="{{ $msg->attachment_url }}" target="_blank">
                                                            <img src="{{ $msg->attachment_url }}" alt="Attachment"
                                                                class="img-fluid rounded border" style="max-height: 180px;">
                                                        </a>
                                                    @else
                                                        <a href="{{ $msg->attachment_url }}" target="_blank"
                                                            class="d-inline-flex align-items-center gap-1 {{ $isAdmin ? 'text-white' : 'text-primary' }} text-decoration-none fs-12 fw-semibold">
                                                            <i class="mdi mdi-file-document-outline fs-16"></i> View Attached File
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="fs-10 text-muted mt-1 {{ $isAdmin ? 'text-end' : 'text-start' }}">
                                            {{ $isAdmin ? ($msg->admin?->name ?? 'Support Agent') : $selectedUser->name }} • {{ $msg->created_at->format('h:i A, d M') }}
                                        </div>
                                    </div>

                                    @if ($isAdmin)
                                        <div class="ms-2 mt-1 flex-shrink-0">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fs-12 shadow-sm"
                                                style="width: 30px; height: 30px;">
                                                <i class="mdi mdi-shield-account"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                        style="width: 58px; height: 58px; font-size: 26px;">
                                        <i class="mdi mdi-calendar-check"></i>
                                    </div>
                                    <h5 class="text-dark fw-bold mb-1">
                                        @if($selectedInquiry)
                                            Booking Inquiry from {{ $selectedUser->name }}
                                        @else
                                            No Conversation History Yet
                                        @endif
                                    </h5>
                                    <p class="fs-13 text-muted mb-3" style="max-width: 440px; margin: 0 auto;">
                                        @if($selectedInquiry)
                                            Applicant submitted inquiry #{{ $selectedInquiry->id }} for <strong>{{ $selectedInquiry->package?->package_title ?? 'Package' }}</strong> ({{ $selectedInquiry->pax }} Pax, {{ ucfirst($selectedInquiry->room_type) }}). Send a greeting below to chat directly with them in the Mobile App.
                                        @else
                                            Send a greeting message to start chatting with {{ $selectedUser->name }}.
                                        @endif
                                    </p>
                                    @if($selectedInquiry)
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="document.getElementById('chatMessageInput').value='Assalam-o-Alaikum {{ addslashes($selectedUser->name) }}, thank you for choosing Baba Hajj & Umrah Group! How may we assist you with your booking #{{ $selectedInquiry->id }}?'; document.getElementById('chatMessageInput').focus();">
                                                <i class="mdi mdi-message-reply-text-outline me-1"></i> Use Greeting Template
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforelse
                        </div>

                        <!-- Chat Input Footer -->
                        <div class="p-3 border-top bg-white">
                            <form id="chatSendForm" action="{{ route('mobile-ops.chat.send', $selectedUser->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div id="attachmentPreviewBar" class="d-none alert alert-light border py-1 px-2 mb-2 d-flex align-items-center justify-content-between fs-12">
                                    <span id="attachmentFileName" class="text-truncate"></span>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0" id="clearAttachmentBtn">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>

                                <div class="input-group">
                                    <label class="btn btn-light border px-3 d-flex align-items-center mb-0" title="Attach image or file">
                                        <i class="mdi mdi-paperclip fs-16"></i>
                                        <input type="file" name="attachment" id="chatAttachmentInput" class="d-none"
                                            accept="image/*,.pdf,.doc,.docx">
                                    </label>
                                    <input type="text" name="message" id="chatMessageInput" class="form-control"
                                        placeholder="Type your message to {{ $selectedUser->name }}..." autocomplete="off">
                                    <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-1" id="chatSendBtn">
                                        <span>Send</span>
                                        <i class="mdi mdi-send fs-15"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <!-- No User Selected Placeholder -->
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 p-5 text-center text-muted">
                            <div class="rounded-circle bg-light p-4 mb-3">
                                <i class="mdi mdi-chat-processing-outline fs-48 text-primary"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Select a Conversation or Booking</h5>
                            <p class="fs-13 text-muted mb-0" style="max-width: 360px;">
                                Choose a pilgrim or booking inquiry from the list on the left to start viewing and replying to their messages.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto scroll chat to bottom
            const container = document.getElementById('chatMessagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }

            // User Search Filter (searches name, phone, and package)
            const searchInput = document.getElementById('chatSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    const items = document.querySelectorAll('.user-chat-item');
                    items.forEach(item => {
                        const name = item.getAttribute('data-name') || '';
                        const phone = item.getAttribute('data-phone') || '';
                        const pkg = item.getAttribute('data-package') || '';
                        if (name.includes(query) || phone.includes(query) || pkg.includes(query)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Attachment file input preview
            const fileInput = document.getElementById('chatAttachmentInput');
            const previewBar = document.getElementById('attachmentPreviewBar');
            const fileNameSpan = document.getElementById('attachmentFileName');
            const clearBtn = document.getElementById('clearAttachmentBtn');

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        fileNameSpan.textContent = '📎 ' + this.files[0].name;
                        previewBar.classList.remove('d-none');
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    fileInput.value = '';
                    previewBar.classList.add('d-none');
                });
            }
        });
    </script>
@endsection
