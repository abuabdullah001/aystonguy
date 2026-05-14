@extends('Backend.Layouts.Dashboard.master')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .chat-container-wrapper {
            height: calc(100vh - 160px);
            min-height: 650px;
        }
        .chat-history {
            height: calc(100vh - 380px); /* Adjust to leave space for header and footer */
            min-height: 400px;
            overflow-y: auto;
            padding: 25px;
            background: #f8f9fa;
            scroll-behavior: smooth;
        }
        .user-list {
            height: calc(100vh - 280px);
            min-height: 500px;
            overflow-y: auto;
        }
        .user-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
            margin: 0;
            padding: 15px 20px !important;
            border-radius: 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .user-item:hover {
            background: #f8f9fa;
        }
        .user-item.active {
            background: #e7f1ff !important;
            border-left-color: #0d6efd;
        }
        .recording-pulse {
            animation: pulse-red 1.5s infinite;
        }
        @keyframes pulse-red {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 82, 82, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 82, 82, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 82, 82, 0); }
        }
        .avatar img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 50%;
        }
        .chat-footer {
            padding: 15px 25px;
            background: #fff;
            border-top: 1px solid #eee;
        }
        .media-preview-container {
            display: none;
            padding: 10px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            gap: 10px;
            flex-wrap: wrap;
        }
        .media-preview-item {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        .media-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .media-preview-item .remove-media {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(255,0,0,0.8);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }
        #chat-card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y chat-container-wrapper">
        <div class="row h-100 justify-content-center">
            <!-- Chat Box -->
            <div class="col-md-10 h-100">
                <div class="card h-100" id="chat-card">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3 position-relative">
                                <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                    <i class="bi bi-robot text-white fs-4"></i>
                                </div>
                                <span class="badge badge-dot bg-success position-absolute bottom-0 end-0 border border-2 border-white" style="width: 12px; height: 12px;"></span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Ayeston AI Assistant</h5>
                                <small class="text-success">Always Online</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body chat-history" id="chat-messages">
                        <!-- Messages loaded here -->
                    </div>
                    <div id="media-preview" class="media-preview-container">
                        <!-- Media previews here -->
                    </div>
                    <div class="card-footer chat-footer">
                        <form id="message-form" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="image-input" name="image" accept="image/*" class="d-none">
                            <input type="file" id="audio-input" name="audio" accept="audio/*" class="d-none">
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-icon rounded-circle" onclick="$('#image-input').click()">
                                        <i class="bi bi-image"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-icon rounded-circle ms-1" id="record-btn">
                                        <i class="bi bi-mic"></i>
                                    </button>
                                </div>
                                <div class="flex-grow-1 position-relative">
                                    <input type="text" name="message" id="msg-input" class="form-control border-1 bg-light py-2" placeholder="Ask AI anything..." style="border-radius: 25px; padding-left: 20px; padding-right: 20px;" autocomplete="off">
                                </div>
                                <button type="submit" id="send-btn" class="btn btn-primary btn-icon rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border:none; background: #0d6efd;">
                                    <i class="bi bi-send-fill text-white fs-5"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let mediaRecorder;
            let audioChunks = [];
            let isRecording = false;

            loadMessages();

            function loadMessages() {
                $.get("{{ route('admin.chats.conversation') }}", function(res) {
                    console.log('Chat History Loaded:', res.count, 'messages');
                    $('#chat-messages').html(res.html);
                    scrollToBottom();
                });
            }

            function scrollToBottom() {
                const chatHistory = document.getElementById('chat-messages');
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }

            function transcribeAudio(file) {
                const formData = new FormData();
                formData.append('audio', file);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('admin.chats.transcribe') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.text) {
                            $('#msg-input').val(res.text);
                            $('#transcription-status').text('Voice Ready');
                            $('.spinner-border').hide();
                        }
                    },
                    error: function() {
                        $('#transcription-status').text('Transcription Failed');
                        $('.spinner-border').hide();
                    }
                });
            }

            // Image Preview
            $('#image-input').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#media-preview').html(`
                            <div class="media-preview-item">
                                <img src="${e.target.result}">
                                <div class="remove-media" onclick="clearMedia()"><i class="bi bi-x"></i></div>
                            </div>
                        `).show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            window.clearMedia = function() {
                $('#image-input').val('');
                $('#audio-input').val('');
                $('#media-preview').hide().html('');
                audioChunks = [];
            };

            // Audio Recording
            $('#record-btn').on('click', async function() {
                if (!isRecording) {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        mediaRecorder = new MediaRecorder(stream);
                        audioChunks = [];

                        mediaRecorder.ondataavailable = (event) => {
                            audioChunks.push(event.data);
                        };

                        mediaRecorder.onstop = () => {
                            // Specify a higher bitrate and ensure we use a standard audio format
                            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                            const audioUrl = URL.createObjectURL(audioBlob);
                            
                            const file = new File([audioBlob], "recording.wav", { type: "audio/wav" });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            document.getElementById('audio-input').files = dataTransfer.files;

                            $('#media-preview').html(`
                                <div class="media-preview-item d-flex align-items-center justify-content-center bg-dark text-white p-2" style="width: auto; min-width: 150px;">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    <span id="transcription-status">Transcribing...</span>
                                    <div class="remove-media" onclick="clearMedia()"><i class="bi bi-x"></i></div>
                                </div>
                            `).show();

                            // Auto-transcribe and put in input box
                            transcribeAudio(file);
                        };

                        mediaRecorder.start();
                        isRecording = true;
                        $(this).addClass('btn-danger recording-pulse').removeClass('btn-outline-secondary').html('<i class="bi bi-stop-fill"></i>');
                    } catch (err) {
                        console.error('Mic Error:', err);
                        // Show a more visual UI alert instead of just a browser alert
                        const errorHtml = `
                            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                                <strong><i class="bi bi-exclamatation-triangle-fill"></i> Microphone Blocked!</strong><br>
                                Browsers block microphones on <code>http://</code> sites for security.<br><br>
                                <strong>To fix this:</strong><br>
                                1. Open <code>chrome://flags/#unsafely-treat-insecure-origin-as-secure</code><br>
                                2. Enable it and add <code>http://ayestonguy.test</code><br>
                                3. Relaunch your browser.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('#chat-messages').append(errorHtml);
                        scrollToBottom();
                    }
                } else {
                    mediaRecorder.stop();
                    isRecording = false;
                    $(this).removeClass('btn-danger').addClass('btn-outline-secondary').html('<i class="bi bi-mic"></i>');
                }
            });

            $('#message-form').on('submit', function(e) {
                e.preventDefault();
                const message = $('#msg-input').val().trim();
                const imageFile = $('#image-input')[0].files[0];
                const audioFile = $('#audio-input')[0].files[0];
                
                if (!message && !imageFile && !audioFile) return;

                // Optimistic UI
                let mediaInfo = '';
                if (imageFile) mediaInfo = '<div class="text-muted small mb-1">[Sending Image...]</div>';
                if (audioFile) mediaInfo = '<div class="text-muted small mb-1">[Sending Voice...]</div>';

                const userMsgHtml = `
                    <div class="chat-message mb-4">
                        <div class="d-flex ">
                            <div class="user-avatar flex-shrink-0 me-3">
                                <div class="avatar">
                                    <img src="{{ auth()->user()->profile_image ? asset(auth()->user()->profile_image) : asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="chat-message-wrapper flex-grow-1" style="max-width: 80%;">
                                <div class="chat-message-text p-3 shadow-sm bg-white border" style="border-radius: 12px; border-bottom-left-radius: 2px;">
                                    <div class="message-body">
                                        ${mediaInfo}
                                        <p class="mb-0" style="word-wrap: break-word; line-height: 1.5; font-size: 14px;">${message}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#chat-messages').append(userMsgHtml);
                scrollToBottom();

                const formData = new FormData(this);
                
                $('#msg-input').val('');
                clearMedia();
                $('#send-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm text-white"></span>');

                $.ajax({
                    url: "{{ route('admin.chats.send') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#send-btn').prop('disabled', false).html('<i class="bi bi-send-fill text-white fs-5"></i>');
                        loadMessages();
                    },
                    error: function(res) {
                        $('#send-btn').prop('disabled', false).html('<i class="bi bi-send-fill text-white fs-5"></i>');
                        alert(res.responseJSON?.message || 'Error communicating with AI');
                    }
                });
            });
        });
    </script>
@endpush
