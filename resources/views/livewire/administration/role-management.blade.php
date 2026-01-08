<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Role Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Role Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Roles List</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary" wire:click="openAddModal">
                                <i class="ri-add-line"></i> Add Role
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search roles...">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Access</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Can Delete</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $index => $role)
                                    <tr>
                                        <td>{{ $roles->firstItem() + $index }}</td>
                                        <td><strong>{{ $role->role_name }}</strong></td>
                                        <td>{{ $role->description }}</td>
                                        <td>
                                            @foreach($role->access as $access)
                                                <span class="badge bg-info me-1 mb-1">{{ $access->access_name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($role->status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($role->can_delete == 1)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" wire:click="openEditModal({{ $role->id }})" title="Edit">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            @if($role->can_delete == 1)
                                                <button type="button" class="btn btn-sm btn-danger" wire:click="setDeleteId({{ $role->id }})" data-bs-toggle="modal" data-bs-target="#removeRoleModal" title="Delete">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No roles found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Role -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white p-3">
                        <h5 class="modal-title">
                            @if($modalMode === 'add')
                                <i class="ri-add-line"></i> Add New Role
                            @else
                                <i class="ri-edit-line"></i> Edit Role
                            @endif
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <!-- Basic Information -->
                            <div class="bg-light p-3 rounded mb-3">
                                <h6 class="mb-3 text-primary"><i class="ri-information-line me-1"></i> Basic Information</h6>
                                <div class="row">
                                    <!-- Role Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="role_name" class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="role_name" class="form-control @error('role_name') is-invalid @enderror" id="role_name" placeholder="Enter role name">
                                        @error('role_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="col-md-6 mb-3">
                                        <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="description" class="form-control @error('description') is-invalid @enderror" id="description" placeholder="Enter description">
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold d-block">Status <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input @error('status') is-invalid @enderror" type="checkbox" role="switch" id="statusSwitch" wire:model.live="status">
                                            <label class="form-check-label" for="statusSwitch">
                                                <span class="badge {{ $status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </label>
                                        </div>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Can Delete -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold d-block">Can Delete <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input @error('can_delete') is-invalid @enderror" type="checkbox" role="switch" id="canDeleteSwitch" wire:model.live="can_delete">
                                            <label class="form-check-label" for="canDeleteSwitch">
                                                <span class="badge {{ $can_delete ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $can_delete ? 'Yes' : 'No' }}
                                                </span>
                                            </label>
                                        </div>
                                        @error('can_delete')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Access Permissions -->
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <h6 class="mb-3 text-warning"><i class="ri-shield-check-line me-1"></i> Access Permissions</h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold">Select Access <span class="text-danger">*</span></label>
                                        <div class="border rounded p-3 bg-white @error('selectedAccess') border-danger @enderror" style="max-height: 300px; overflow-y: auto;">
                                            <div class="row">
                                                @foreach ($allAccess as $access)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" wire:model="selectedAccess" value="{{ $access->id }}" id="access_{{ $access->id }}">
                                                            <label class="form-check-label" for="access_{{ $access->id }}">
                                                                <strong>{{ $access->access_name }}</strong>
                                                                @if($access->description)
                                                                    <br><small class="text-muted">{{ $access->description }}</small>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('selectedAccess')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeModal">Cancel</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri-save-line"></i>
                                    @if($modalMode === 'add') Save @else Update @endif
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Remove Role Modal -->
    <div id="removeRoleModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-removeRoleModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548"
                            style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you sure you want to remove this role?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn w-sm btn-danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">
                            <span wire:loading.remove wire:target="delete">
                                Yes, Delete It!
                            </span>
                            <span wire:loading wire:target="delete">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Deleting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-switch-lg .form-check-input {
            width: 3.5rem;
            height: 1.75rem;
            cursor: pointer;
        }

        .form-switch-lg .form-check-input:checked {
            background-color: #0ab39c;
            border-color: #0ab39c;
        }

        .form-check-label {
            cursor: pointer;
            margin-left: 0.5rem;
        }

        .bg-light {
            background-color: #f3f6f9 !important;
        }
    </style>

    @script
        <script>
            // Handle notification after delete
            $wire.on('notification', (data) => {
                const [event] = data;

                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('removeRoleModal'));
                if (modal) {
                    modal.hide();
                }

                // Show notification
                if (event.type === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: event.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else if (event.type === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: event.message,
                        showConfirmButton: true
                    });
                }
            });
        </script>
    @endscript

    @if (session()->has('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            });
        </script>
    @endif
</div>
