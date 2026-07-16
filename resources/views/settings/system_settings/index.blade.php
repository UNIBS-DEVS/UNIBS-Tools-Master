@extends('settings.layout')

@section('settings-title', 'Manage System Settings')

@section('settings-actions')
    <div class="d-flex align-items-center gap-2">

        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search..."
                value="{{ request('search') }}">

            <button class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-search"></i>
            </button>

            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary ms-1">
                <i class="fa fa-x"></i>
            </a>
        </form>

        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSettingModal">
            <i class="fa fa-plus"></i> Add System Setting
        </button>

    </div>
@endsection

@section('settings-content')

    <div class="table-responsive">
        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Parameter</th>
                    <th>Value</th>
                    <th>Remarks</th>
                    <th>Added By</th>
                    <th>Updated By</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($settings as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($settings->currentPage() - 1) * $settings->perPage() }}</td>

                        <td>{{ $item->setting_parameter }}</td>

                        <td>
                            @php
                                $val = json_decode($item->setting_value, true);
                            @endphp

                            {{ is_array($val) ? implode(', ', $val) : $item->setting_value }}
                        </td>

                        <td>{{ $item->remarks ?? '-' }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->update_by_user->name ?? '-' }}</td>

                        <td class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm btn-primary"
                                onclick="openEditModal({{ $item->id }}, '{{ $item->setting_parameter }}', '{{ $item->setting_value }}', `{{ $item->remarks }}`)">
                                <i class="fa fa-edit"></i>
                            </button>

                            <form action="{{ route('settings.system-settings.destroy', $item->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-3">
        {{ $settings->links() }}
    </div>

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addSettingModal">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('settings.system-settings.store') }}">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Add Setting</h5>
                    </div>

                    <div class="modal-body">
                        <input type="text" name="setting_parameter" class="form-control mb-2" placeholder="Parameter">
                        <input type="text" name="setting_value" class="form-control mb-2" placeholder="Value">
                        <textarea name="remarks" class="form-control" placeholder="Remarks"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <form id="updateForm">

                <input type="hidden" id="edit_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Setting</h5>
                    </div>

                    <div class="modal-body">
                        <input type="text" id="edit_parameter" class="form-control mb-2" readonly>
                        <input type="text" id="edit_value" class="form-control mb-2">
                        <textarea id="edit_remarks" class="form-control"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Update</button>
                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openEditModal(id, parameter, value, remarks) {
            $('#edit_id').val(id);
            $('#edit_parameter').val(parameter);
            $('#edit_value').val(value);
            $('#edit_remarks').val(remarks);
            $('#editModal').modal('show');
        }

        $('#updateForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "/settings/system-settings/" + $('#edit_id').val(),
                type: "POST",
                data: {
                    _method: "PUT",
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    setting_value: $('#edit_value').val(),
                    remarks: $('#edit_remarks').val()
                },
                success: function() {
                    location.reload(); // simple + safe
                }
            });
        });
    </script>
@endpush
