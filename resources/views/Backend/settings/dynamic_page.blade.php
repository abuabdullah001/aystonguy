@extends('Backend.Layouts.Dashboard.master')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
    @media (max-width: 991.98px) {
        #dynamic-pages-table {
            font-size: 13px;
        }
    }

    #dynamic-pages-table td,
    #dynamic-pages-table th {
        white-space: nowrap;
    }

    #dynamic-pages-table .d-flex {
        flex-wrap: wrap;
    }

    /* CKEditor Dark/Black Theme */
    .ck-editor__editable_inline {
        min-height: 220px;
        background: #1a1a1a !important;
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* CKEditor Toolbar */
    .ck.ck-editor__main > .ck-editor__editable {
        background: #1a1a1a !important;
        color: #fff !important;
    }

    .ck.ck-toolbar {
        background: #0a0a0a !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        flex-wrap: wrap !important;
    }

    .ck .ck-button {
        color: #fff !important;
    }

    .ck .ck-button:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .ck .ck-button.ck-on {
        background: rgba(102, 126, 234, 0.4) !important;
    }

    /* CKEditor Text Color */
    .ck-editor__editable p {
        color: #fff !important;
    }

    .ck-editor__editable {
        background: #1a1a1a !important;
    }

    /* CKEditor Content */
    .ck.ck-content {
        color: #fff !important;
    }

    /* Popup and Dropdowns */
    .ck.ck-balloon-panel {
        background: #0a0a0a !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    .ck.ck-dropdown {
        background: #0a0a0a !important;
    }

    .ck.ck-dropdown__panel {
        background: #0a0a0a !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    .ck.ck-dropdown__panel .ck-button {
        color: #fff !important;
    }

    .ck.ck-dropdown__panel .ck-button:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    /* Input Fields in Editor */
    .ck.ck-input__text {
        background: #1a1a1a !important;
        color: #fff !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    .ck.ck-input__text:focus {
        border-color: rgba(102, 126, 234, 0.5) !important;
        box-shadow: inset 0 0 0 1px rgba(102, 126, 234, 0.3) !important;
    }

    /* Editor Labels */
    .ck.ck-labeled-field-view > label {
        color: #fff !important;
    }

    /* Separator */
    .ck.ck-toolbar__separator {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    /* Responsive Fixes */
    #dynamic-page-form,
    #dynamic-page-form .mb-3,
    #dynamic-page-form .ck,
    #dynamic-page-form .ck-editor,
    #dynamic-page-form .ck-editor__top,
    #dynamic-page-form .ck-editor__main,
    #dynamic-page-form .ck.ck-editor,
    #dynamic-page-form .ck.ck-editor__main,
    #dynamic-page-form .ck.ck-editor__editable {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }

    #dynamic-page-form .ck.ck-toolbar {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch;
    }

    #dynamic-page-form .ck-editor__editable_inline,
    #dynamic-page-form .ck.ck-content {
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
    }

    #dynamic-page-form .ck-editor__editable_inline table {
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }

    .col-12.col-lg-4 .card-body {
        overflow-x: hidden;
    }

    .ck.ck-toolbar {
        max-width: 100% !important;
    }

    .ck-editor__main {
        max-width: 100% !important;
    }

    .ck.ck-editor__top .ck-toolbar {
        border-radius: 4px 4px 0 0;
    }

    /* Responsive Table */
    @media (max-width: 767px) {
        #dynamic-pages-table {
            font-size: 11px !important;
        }

        #dynamic-pages-table td,
        #dynamic-pages-table th {
            padding: 4px !important;
        }

        .ck-editor__editable_inline {
            min-height: 150px !important;
        }

        .ck.ck-toolbar {
            padding: 4px !important;
        }

        .ck.ck-toolbar .ck-toolbar__separator {
            margin: 2px 2px !important;
        }
    }

    /* CKEditor Responsive - Small screens */
    @media (max-width: 576px) {
        .ck.ck-toolbar {
            flex-wrap: wrap;
            gap: 4px;
        }

        .ck-editor__editable_inline {
            min-height: 200px !important;
            font-size: 14px;
        }

        .ck.ck-editor__editable_inline {
            padding: 10px !important;
        }
    }

    /* Layout responsive */
    @media (max-width: 1199px) {
        .container-xxl.flex-grow-1 {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        .row.g-4 {
            gap: 0.8rem !important;
        }
    }

    @media (max-width: 767px) {
        .row.g-4 {
            gap: 1rem !important;
        }

        .col-12 {
            width: 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Dynamic Page List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap" id="dynamic-pages-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" id="form-title">Create Dynamic Page</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="form-errors"></div>
                    <div class="alert alert-success d-none" id="form-success"></div>

                    <form id="dynamic-page-form">
                        @csrf
                        <input type="hidden" id="page_id" name="page_id">

                        <div class="mb-3">
                            <label class="form-label">Page Title</label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                placeholder="Enter page title"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="10"
                                placeholder="Write page description"></textarea>
                        </div>

                        <div class="d-grid d-sm-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submit-btn">Create Page</button>
                            <button type="button" class="btn btn-outline-secondary" id="reset-btn">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    const $form = $('#dynamic-page-form');
    const $errorBox = $('#form-errors');
    const $successBox = $('#form-success');
    let descriptionEditor;

    function showErrors(xhr) {
        const errors = xhr.responseJSON?.errors || {};
        let html = '';

        Object.keys(errors).forEach(function (key) {
            html += '<div>' + errors[key][0] + '</div>';
        });

        if (!html && xhr.responseJSON?.message) {
            html = '<div>' + xhr.responseJSON.message + '</div>';
        }

        $errorBox.removeClass('d-none').html(html || '<div>Something went wrong.</div>');
    }

    function resetForm() {
        $form[0].reset();
        $('#page_id').val('');
        $('#form-title').text('Create Dynamic Page');
        $('#submit-btn').text('Create Page');
        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        if (descriptionEditor) {
            descriptionEditor.setData('');
        }
    }

    const table = $('#dynamic-pages-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: false,
        scrollX: true,
        ajax: '{{ route('admin.settings.dynamic.page.data') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'description_preview', name: 'description', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor
            .create(document.querySelector('#description'))
            .then(function (editor) {
                descriptionEditor = editor;
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    $form.on('submit', function (e) {
        e.preventDefault();

        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        const pageId = $('#page_id').val();
        const isUpdate = pageId !== '';
        const url = isUpdate
            ? '{{ url('admin/settings/dynamic-page') }}/' + pageId
            : '{{ route('admin.settings.dynamic.page.store') }}';
        const method = isUpdate ? 'PUT' : 'POST';

        const payload = {
            title: $('#title').val(),
            description: descriptionEditor ? descriptionEditor.getData() : $('#description').val(),
            _token: '{{ csrf_token() }}'
        };

        if (isUpdate) {
            payload._method = 'PUT';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: payload,
            success: function (res) {
                $successBox.removeClass('d-none').html(res.message || 'Saved successfully.');
                table.ajax.reload(null, false);

                if (!isUpdate) {
                    resetForm();
                }
            },
            error: function (xhr) {
                showErrors(xhr);
            }
        });
    });

    $('#dynamic-pages-table').on('click', '.js-edit-page', function () {
        const pageId = $(this).data('id');
        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        $.ajax({
            url: '{{ url('admin/settings/dynamic-page') }}/' + pageId + '/edit',
            method: 'GET',
            success: function (res) {
                const page = res.data;
                $('#page_id').val(page.id);
                $('#title').val(page.title || '');

                if (descriptionEditor) {
                    descriptionEditor.setData(page.description || '');
                } else {
                    $('#description').val(page.description || '');
                }

                $('#form-title').text('Update Dynamic Page');
                $('#submit-btn').text('Update Page');
            },
            error: function (xhr) {
                showErrors(xhr);
            }
        });
    });

    $('#dynamic-pages-table').on('click', '.js-delete-page', function () {
        const pageId = $(this).data('id');
        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        if (!window.confirm('Are you sure you want to delete this page?')) {
            return;
        }

        $.ajax({
            url: '{{ url('admin/settings/dynamic-page') }}/' + pageId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function (res) {
                $successBox.removeClass('d-none').html(res.message || 'Deleted successfully.');
                table.ajax.reload(null, false);

                if ($('#page_id').val() === String(pageId)) {
                    resetForm();
                }
            },
            error: function (xhr) {
                showErrors(xhr);
            }
        });
    });

    $('#reset-btn').on('click', function () {
        resetForm();
    });
});
</script>
@endpush
