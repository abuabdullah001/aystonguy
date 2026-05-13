@extends('Backend.Layouts.Dashboard.master')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css">
    <style>
        @media (max-width: 991.98px) {
            #videos-table {
                font-size: 13px;
            }
        }

        #videos-table td,
        #videos-table th {
            white-space: nowrap;
            vertical-align: middle;
        }

        #videos-table .d-flex {
            flex-wrap: wrap;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Video List</h5>
                <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">Create Video</a>
            </div>
            <div class="card-body">
                <div class="alert alert-danger d-none" id="form-errors"></div>
                <div class="alert alert-success d-none" id="form-success"></div>

                <div class="table-responsive">
                    <table class="table table-bordered nowrap" id="videos-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Video</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        $(function() {
            const $errorBox = $('#form-errors');
            const $successBox = $('#form-success');
            const videosBaseUrl = "{{ route('admin.videos.index', [], false) }}";

            $.fn.dataTable.ext.errMode = 'none';

            const table = $('#videos-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                columnDefs: [
                    { targets: -1, responsivePriority: 1 },
                    { targets: 0, responsivePriority: 2 },
                    { targets: 2, responsivePriority: 3 },
                    { targets: 3, responsivePriority: 10001 },
                    { targets: 4, responsivePriority: 10002 },
                    { targets: 5, responsivePriority: 10003 },
                ],
                ajax: {
                    url: '{{ route('admin.videos.data', [], false) }}',
                    type: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    error: function(xhr) {
                        let message = 'Failed to load videos table.';

                        if (xhr.status === 401) {
                            message = 'Unauthorized (401). Please login again.';
                        } else if (xhr.status === 403) {
                            message = 'Forbidden (403). You do not have permission to view videos.';
                        } else if (xhr.status === 419) {
                            message = 'Page expired (419). Please refresh the page.';
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        $errorBox.removeClass('d-none').html('<div>' + message + '</div>');
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'category_title',
                        name: 'category.title',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description_preview',
                        name: 'description',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image_preview',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'video_link',
                        name: 'video',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#videos-table').on('error.dt', function(e, settings, techNote, message) {
                $errorBox.removeClass('d-none').html('<div>' + (message || 'DataTables error.') + '</div>');
            });

            function showErrors(xhr) {
                if (xhr.status === 0) {
                    $errorBox.removeClass('d-none').html(
                        '<div>Upload failed. This usually happens when the server blocks large uploads. Please increase PHP/server upload limits and try again.</div>'
                        );
                    return;
                }

                if (xhr.status === 419) {
                    $errorBox.removeClass('d-none').html(
                        '<div>Page expired (419). Please refresh the page and try again.</div>');
                    return;
                }

                if (xhr.status === 413) {
                    const msg = xhr.responseJSON?.message ||
                    'Upload too large (413). Please upload a smaller file.';
                    $errorBox.removeClass('d-none').html('<div>' + msg + '</div>');
                    return;
                }

                if (xhr.status === 403) {
                    $errorBox.removeClass('d-none').html(
                        '<div>Forbidden (403). You do not have permission to perform this action.</div>');
                    return;
                }

                const errors = xhr.responseJSON?.errors || {};
                let html = '';
                Object.keys(errors).forEach(function(key) {
                    html += '<div>' + errors[key][0] + '</div>';
                });

                if (!html && xhr.responseJSON?.message) {
                    html = '<div>' + xhr.responseJSON.message + '</div>';
                }

                $errorBox.removeClass('d-none').html(html || '<div>Something went wrong.</div>');
            }

            $('#videos-table').on('click', '.js-delete-video', function() {
                const videoId = $(this).data('id');
                $errorBox.addClass('d-none').html('');
                $successBox.addClass('d-none').html('');

                if (!confirm('Delete this video?')) {
                    return;
                }

                $.ajax({
                    url: videosBaseUrl + '/' + videoId,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(res) {
                        $successBox.removeClass('d-none').html(res.message ||
                            'Deleted successfully.');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        showErrors(xhr);
                    }
                });
            });
        });
    </script>
@endpush
