@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold m-0 text-dark">Mobile App REST API Documentation</h4>
            <p class="text-muted fs-13 mb-0">Complete reference and JSON schemas for Android, iOS, Flutter, and React Native
                mobile developers.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-12">
                <i class="mdi mdi-check-decagram me-1"></i> API Version 2.0 (Live)
            </span>
        </div>
    </div>

    <!-- Quick Overview Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <h5 class="fs-15 fw-bold text-dark mb-1">API Base Endpoint</h5>
                    <p class="text-muted fs-13 mb-3">All mobile application requests must be routed to this base URL with
                        JSON headers.</p>
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-white fw-semibold">BASE URL</span>
                        <input type="text" class="form-control bg-light font-monospace" value="{{ url('/api/app') }}"
                            readonly id="baseUrlInput">
                        <button class="btn btn-primary"
                            onclick="navigator.clipboard.writeText('{{ url('/api/app') }}'); alert('Base URL copied to clipboard!');">
                            <i class="mdi mdi-content-copy me-1"></i> Copy
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border">
                        <div class="fw-semibold text-dark fs-13 mb-1"><i
                                class="mdi mdi-shield-key-outline text-warning me-1"></i> Authentication Header</div>
                        <code class="fs-12 text-dark d-block">Authorization: Bearer &lt;token&gt;</code>
                        <div class="text-muted fs-11 mt-1">Returned upon login or register. Required for protected routes.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Modules Accordion -->
    <div class="accordion mb-4" id="apiAccordion">
        <!-- 1. Authentication -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
            <h2 class="accordion-header" id="headingAuth">
                <button class="accordion-button fw-bold fs-15 text-dark bg-white" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseAuth">
                    <i class="mdi mdi-account-key-outline text-primary me-2 fs-18"></i> 1. User Authentication Module
                </button>
            </h2>
            <div id="collapseAuth" class="accordion-collapse collapse show">
                <div class="accordion-body p-4 bg-light">
                    <!-- Register -->
                    <div class="card border mb-3">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-success me-2 px-2 py-1">POST</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/auth/register</code>
                            </div>
                            <span class="badge bg-light text-muted border">Public</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-2">Registers a new mobile app user and generates a Bearer token
                                automatically.</p>
                            <h6 class="fs-12 fw-bold text-dark">Request Payload (JSON):</h6>
                            <pre class="bg-dark text-white p-3 rounded fs-12 mb-2"><code>{
  "name": "Muhammad Ali",
  "phone": "03001234567",
  "email": "ali@example.com",
  "password": "password123",
  "fcm_token": "optional_firebase_token",
  "device_name": "iPhone 15 Pro"
}</code></pre>
                            <h6 class="fs-12 fw-bold text-dark">Success Response (201 Created):</h6>
                            <pre class="bg-dark text-white p-3 rounded fs-12 mb-0"><code>{
  "success": true,
  "message": "Registration successful. Welcome to Baba Hajj Group!",
  "data": {
    "token": "1|abc123token...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Muhammad Ali",
      "phone": "03001234567",
      "email": "ali@example.com",
      "avatar_url": null,
      "status": "active"
    }
  }
}</code></pre>
                        </div>
                    </div>

                    <!-- Login -->
                    <div class="card border mb-3">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-success me-2 px-2 py-1">POST</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/auth/login</code>
                            </div>
                            <span class="badge bg-light text-muted border">Public</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-2">Login using Phone Number OR Email along with password.</p>
                            <pre class="bg-dark text-white p-3 rounded fs-12 mb-2"><code>{
  "identifier": "03001234567", // or "ali@example.com"
  "password": "password123",
  "fcm_token": "optional_firebase_push_token",
  "device_name": "Samsung S24"
}</code></pre>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="card border mb-0">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-primary me-2 px-2 py-1">GET</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/auth/profile</code>
                            </div>
                            <span class="badge bg-warning text-dark border">Auth Required</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-0">Returns current logged-in user profile, unread chat count, and
                                notifications count.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Hajj & Umrah Packages -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
            <h2 class="accordion-header" id="headingPackages">
                <button class="accordion-button collapsed fw-bold fs-15 text-dark bg-white" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapsePackages">
                    <i class="mdi mdi-cube-outline text-success me-2 fs-18"></i> 2. Hajj & Umrah Packages Module
                </button>
            </h2>
            <div id="collapsePackages" class="accordion-collapse collapse">
                <div class="accordion-body p-4 bg-light">
                    <!-- Get Packages -->
                    <div class="card border mb-3">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-primary me-2 px-2 py-1">GET</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/packages</code>
                            </div>
                            <span class="badge bg-light text-muted border">Public</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-2">Fetches active packages. Query params: <code>?type=hajj</code>,
                                <code>?type=umrah</code>, <code>?search=keyword</code>.
                            </p>
                            <h6 class="fs-12 fw-bold text-dark">Dedicated Shortcuts:</h6>
                            <ul class="fs-12 mb-0">
                                <li><code>GET /api/app/packages/hajj</code> - Direct Hajj packages</li>
                                <li><code>GET /api/app/packages/umrah</code> - Direct Umrah packages</li>
                                <li><code>GET /api/app/packages/{id}</code> - Full package details (accommodations, room
                                    rates, flights, itinerary, terms)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Booking Inquiry Submission -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
            <h2 class="accordion-header" id="headingBookings">
                <button class="accordion-button collapsed fw-bold fs-15 text-dark bg-white" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseBookings">
                    <i class="mdi mdi-calendar-check-outline text-warning me-2 fs-18"></i> 3. Booking Request & My Bookings
                </button>
            </h2>
            <div id="collapseBookings" class="accordion-collapse collapse">
                <div class="accordion-body p-4 bg-light">
                    <!-- Submit Booking -->
                    <div class="card border mb-3">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-success me-2 px-2 py-1">POST</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/bookings</code>
                            </div>
                            <span class="badge bg-light text-muted border">Public / Authenticated</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-2">
                                Submits a package booking request. Can be called by a guest/random person or an
                                authenticated app user (auto-links user ID).
                                Also dispatches an in-app notification and returns direct WhatsApp chat confirmation.
                            </p>
                            <h6 class="fs-12 fw-bold text-dark">Request (multipart/form-data or JSON):</h6>
                            <pre class="bg-dark text-white p-3 rounded fs-12 mb-2"><code>{
  "package_id": 1,
  "full_name": "Tariq Mahmood",
  "contact": "03009876543",
  "email": "tariq@example.com",
  "pax": 2,
  "room_type": "double", // sharing, quad, triple, double
  "passport_number": "AB1234567",
  "cnic_number": "42101-1234567-1",
  "special_request": "Near Kaaba view requested",
  "passport_document": "(optional file)",
  "nic_or_visa_document": "(optional file)"
}</code></pre>
                        </div>
                    </div>

                    <!-- My Bookings -->
                    <div class="card border mb-0">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-primary me-2 px-2 py-1">GET</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/bookings/my</code>
                            </div>
                            <span class="badge bg-warning text-dark border">Auth Required</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-0">Returns list of bookings submitted by the authenticated user
                                with real-time operational status (pending, contacted, closed/confirmed).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Live In-App Chat -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
            <h2 class="accordion-header" id="headingChat">
                <button class="accordion-button collapsed fw-bold fs-15 text-dark bg-white" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseChat">
                    <i class="mdi mdi-chat-processing-outline text-info me-2 fs-18"></i> 4. In-App Live Support Chat
                </button>
            </h2>
            <div id="collapseChat" class="accordion-collapse collapse">
                <div class="accordion-body p-4 bg-light">
                    <!-- Get Chat Messages -->
                    <div class="card border mb-3">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-primary me-2 px-2 py-1">GET</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/chat/messages</code>
                            </div>
                            <span class="badge bg-warning text-dark border">Auth Required</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-0">Fetches message history between user and support desk.
                                Automatically marks unread admin messages as read.</p>
                        </div>
                    </div>

                    <!-- Send Chat Message -->
                    <div class="card border mb-0">
                        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-success me-2 px-2 py-1">POST</span>
                                <code class="fs-13 text-dark fw-bold">/api/app/chat/send</code>
                            </div>
                            <span class="badge bg-warning text-dark border">Auth Required</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="fs-12 text-muted mb-2">Sends a message or file attachment from user to agency admin.
                            </p>
                            <pre class="bg-dark text-white p-3 rounded fs-12 mb-0"><code>{
  "message": "Assalam-o-Alaikum, is ziyarat transport included?",
  "attachment": "(optional image or pdf file)"
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Content, Guides, Videos, Books, Duas -->
        <div class="accordion-item border-0 shadow-sm mb-0 rounded overflow-hidden">
            <h2 class="accordion-header" id="headingContent">
                <button class="accordion-button collapsed fw-bold fs-15 text-dark bg-white" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseContent">
                    <i class="mdi mdi-book-open-page-variant-outline text-danger me-2 fs-18"></i> 5. Guidelines, Videos,
                    Books, Ziyarat & Duas
                </button>
            </h2>
            <div id="collapseContent" class="accordion-collapse collapse">
                <div class="accordion-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded">
                                <span class="badge bg-primary mb-1">GET</span>
                                <h6><code>/api/app/guides</code></h6>
                                <p class="fs-12 text-muted mb-0">Step-by-step Hajj & Umrah guidelines and rules.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded">
                                <span class="badge bg-primary mb-1">GET</span>
                                <h6><code>/api/app/videos</code></h6>
                                <p class="fs-12 text-muted mb-0">Tutorial videos and Live stream links (YouTube/Vimeo).</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded">
                                <span class="badge bg-primary mb-1">GET</span>
                                <h6><code>/api/app/books</code></h6>
                                <p class="fs-12 text-muted mb-0">Downloadable Hajj and Umrah guide books (PDFs).</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded">
                                <span class="badge bg-primary mb-1">GET</span>
                                <h6><code>/api/app/ziyarat</code></h6>
                                <p class="fs-12 text-muted mb-0">Holy places in Makkah and Madinah with high-res photos.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded">
                                <span class="badge bg-primary mb-1">GET</span>
                                <h6><code>/api/app/duas</code></h6>
                                <p class="fs-12 text-muted mb-0">Essential Hajj & Umrah duas (Arabic, transliteration, Urdu
                                    & English).</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded">
                                <span class="badge bg-primary mb-1">GET</span>
                                <h6><code>/api/app/emergency-contacts</code></h6>
                                <p class="fs-12 text-muted mb-0">Agency 24/7 helplines & Saudi emergency services numbers.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
