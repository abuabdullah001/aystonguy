@extends('Backend.Layouts.Dashboard.master')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    @media (max-width: 991.98px) {
        #categories-table {
            font-size: 13px;
        }
    }

    #categories-table td,
    #categories-table th {
        white-space: nowrap;
    }

    #categories-table .d-flex {
        flex-wrap: wrap;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Category List</h5>
                </div>
                <div class="card-body mt-2">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap" id="categories-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" id="form-title">Create Category</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="form-errors"></div>
                    <div class="alert alert-success d-none" id="form-success"></div>

                    <form id="category-form">
                        @csrf
                        <input type="hidden" id="category_id" name="category_id">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                        <div class="d-grid d-sm-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submit-btn">Create Category</button>
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
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(function () {
    const $form = $('#category-form');
    const $errorBox = $('#form-errors');
    const $successBox = $('#form-success');
    const categoriesBaseUrl = "{{ route('admin.categories.index', [], false) }}";

    const table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: '{{ route('admin.categories.data', [], false) }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    function resetForm() {
        $form[0].reset();
        $('#category_id').val('');
        $('#is_active').prop('checked', true);
        $('#form-title').text('Create Category');
        $('#submit-btn').text('Create Category');
        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');
    }

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

    $form.on('submit', function (e) {
        e.preventDefault();

        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        const categoryId = $('#category_id').val();
        const isUpdate = categoryId !== '';
        const url = isUpdate ? categoriesBaseUrl + '/' + categoryId : "{{ route('admin.categories.store', [], false) }}";
        const method = isUpdate ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
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

    $('#reset-btn').on('click', function () {
        resetForm();
    });

    $('#categories-table').on('click', '.js-edit-category', function () {
        const categoryId = $(this).data('id');
        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        $.ajax({
            url: categoriesBaseUrl + '/' + categoryId + '/edit',
            method: 'GET',
            success: function (res) {
                const category = res.data;
                $('#category_id').val(category.id);
                $('#title').val(category.title || '');
                $('#is_active').prop('checked', !!category.is_active);

                $('#form-title').text('Edit Category');
                $('#submit-btn').text('Update Category');
            },
            error: function (xhr) {
                showErrors(xhr);
            }
        });
    });

    $('#categories-table').on('click', '.js-delete-category', function () {
        const categoryId = $(this).data('id');
        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        if (!confirm('Delete this category?')) {
            return;
        }

        $.ajax({
            url: categoriesBaseUrl + '/' + categoryId,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function (res) {
                $successBox.removeClass('d-none').html(res.message || 'Deleted successfully.');
                table.ajax.reload(null, false);
                resetForm();
            },
            error: function (xhr) {
                showErrors(xhr);
            }
        });
    });

    $('#categories-table').on('click', '.js-toggle-category-status', function () {
        const categoryId = $(this).data('id');
        $errorBox.addClass('d-none').html('');
        $successBox.addClass('d-none').html('');

        $.ajax({
            url: categoriesBaseUrl + '/' + categoryId + '/toggle-status',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function (res) {
                $successBox.removeClass('d-none').html(res.message || 'Status updated successfully.');
                table.ajax.reload(null, false);

                const editingId = $('#category_id').val();
                if (editingId !== '' && String(editingId) === String(categoryId) && res.data) {
                    $('#is_active').prop('checked', !!res.data.is_active);
                }
            },
            error: function (xhr) {
                showErrors(xhr);
            }
        });
    });
});
</script>
@endpush
