<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit User</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/security-management">Security Management</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form wire:submit.prevent="update">
                    <!-- Basic Information Section -->
                    <div class="bg-light p-3 rounded mb-4">
                        <h6 class="mb-3 text-primary"><i class="ri-user-line me-1"></i> Basic Information</h6>
                        <div class="row">
                            <!-- Username -->
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Enter username">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Employee Name -->
                            <div class="col-md-6 mb-3">
                                <label for="empname" class="form-label fw-semibold">Employee Name <span class="text-danger">*</span></label>
                                <input type="text" wire:model="empname" class="form-control @error('empname') is-invalid @enderror" id="empname" placeholder="Enter employee name">
                                @error('empname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Employee ID -->
                            <div class="col-md-6 mb-3">
                                <label for="empid" class="form-label fw-semibold">Employee ID</label>
                                <input type="text" wire:model="empid" class="form-control" id="empid" placeholder="Enter employee ID">
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="bg-info bg-opacity-10 p-3 rounded mb-4">
                        <h6 class="mb-3 text-info"><i class="ri-lock-line me-1"></i> Security</h6>
                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">Password <small class="text-muted">(leave blank if not changing)</small></label>
                                <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter new password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Confirm new password">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Roles & Permissions Section -->
                    <div class="bg-warning bg-opacity-10 p-3 rounded mb-4">
                        <h6 class="mb-3 text-warning"><i class="ri-shield-user-line me-1"></i> Roles & Permissions</h6>
                        <div class="row">
                            <!-- Roles (Multiple Selection) -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Select Roles <span class="text-danger">*</span></label>
                                <div class="border rounded p-3 bg-white @error('selectedRoles') border-danger @enderror" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($userroles as $role)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" id="role_{{ $role->id }}">
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                <strong>{{ $role->description }}</strong>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedRoles')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="bg-success bg-opacity-10 p-3 rounded mb-4">
                        <h6 class="mb-3 text-success"><i class="ri-information-line me-1"></i> Additional Information</h6>
                        <div class="row">
                            <!-- Code -->
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label fw-semibold">Code</label>
                                <input type="text" wire:model="code" class="form-control" id="code" placeholder="Enter code">
                            </div>

                            <!-- Territory -->
                            <div class="col-md-6 mb-3">
                                <label for="territory_ix" class="form-label fw-semibold">Territory</label>
                                <input type="text" wire:model="territory_ix" class="form-control" id="territory_ix" placeholder="Enter territory">
                            </div>

                            <!-- Status Toggle -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold d-block">Status <span class="text-danger">*</span></label>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input @error('status') is-invalid @enderror" type="checkbox" role="switch" id="statusSwitch" wire:model="status" value="1" {{ $status == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusSwitch">
                                        <span class="badge {{ $status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="/security-management" class="btn btn-light">
                                    <i class="ri-arrow-left-line"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="update">
                                        <i class="ri-save-line"></i> Update User
                                    </span>
                                    <span wire:loading wire:target="update">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Updating...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
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
</div>
