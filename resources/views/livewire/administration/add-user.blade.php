<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Add User</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/security-management">Security Management</a></li>
                        <li class="breadcrumb-item active">Add User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <!-- Username -->
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Enter username">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Employee Name -->
                        <div class="col-md-6 mb-3">
                            <label for="empname" class="form-label">Employee Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="empname" class="form-control @error('empname') is-invalid @enderror" id="empname" placeholder="Enter employee name">
                            @error('empname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Employee ID -->
                        <div class="col-md-6 mb-3">
                            <label for="empid" class="form-label">Employee ID</label>
                            <input type="text" wire:model="empid" class="form-control" id="empid" placeholder="Enter employee ID">
                        </div>

                        <!-- Password -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Confirm password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-6 mb-3">
                            <label for="roleid" class="form-label">Role <span class="text-danger">*</span></label>
                            <select wire:model="roleid" class="form-select @error('roleid') is-invalid @enderror" id="roleid">
                                <option value="">- Select Role -</option>
                                @foreach ($userroles as $role)
                                    <option value="{{ $role->id }}">{{ $role->description }}</option>
                                @endforeach
                            </select>
                            @error('roleid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role Mode -->
                        <div class="col-md-6 mb-3">
                            <label for="rolemode" class="form-label">Role Mode</label>
                            <select wire:model="rolemode" class="form-select" id="rolemode">
                                <option value="readonly">Read Only</option>
                                <option value="readwrite">Read Write</option>
                            </select>
                        </div>

                        <!-- Code -->
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" wire:model="code" class="form-control" id="code" placeholder="Enter code">
                        </div>

                        <!-- Territory -->
                        <div class="col-md-6 mb-3">
                            <label for="territory_ix" class="form-label">Territory</label>
                            <input type="text" wire:model="territory_ix" class="form-control" id="territory_ix" placeholder="Enter territory">
                        </div>

                        <!-- Status -->
                        <div class="col-md-12 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select wire:model="status" class="form-select @error('status') is-invalid @enderror" id="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri-save-line"></i> Save
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Saving...
                                </span>
                            </button>
                            <a href="/security-management" class="btn btn-secondary">
                                <i class="ri-arrow-left-line"></i> Back
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
