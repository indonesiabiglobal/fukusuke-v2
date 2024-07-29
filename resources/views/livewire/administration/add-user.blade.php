<div class="row">
    {{-- <div class="col-lg-2"></div> --}}
    <div class="col-12 col-lg-12">
        <form wire:submit.prevent="save">
            <div class="form-group">
                <div class="input-group mb-1">
                    <label class="control-label col-12 col-lg-3 fw-bold text-muted">Email</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" wire:model="email" required/>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="input-group mb-1">
                    <label class="control-label col-12 col-lg-3 fw-bold text-muted">User Name</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" wire:model="username" required/>
                    @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="input-group mb-1">
                    <label class="control-label col-12 col-lg-3 fw-bold text-muted">Password</label>
                    <input type="text" class="form-control @error('password') is-invalid @enderror" wire:model="password" required/>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0">
                        <div class="card-body">
                            <h6 class="fs-15">User Roles</h6>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Select All
                                </label>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isAdmin">
                                        <label class="form-check-label" for="isAdmin">
                                            Administrator
                                        </label>
                                        <select wire:model.defer="isAdministrator" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>

                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isKenpin">
                                        <label class="form-check-label" for="isKenpin">
                                            Kenpin
                                        </label>
                                        <select wire:model.defer="isKenpin" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>

                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isNippoInfure">
                                        <label class="form-check-label" for="isNippoInfure">
                                            Nippo Infure
                                        </label>
                                        <select wire:model.defer="isNippoInfure" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-4">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isOrder">
                                        <label class="form-check-label" for="isOrder">
                                            Order Transaction
                                        </label>
                                        <select wire:model.defer="isOrder" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>

                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isWarehouse">
                                        <label class="form-check-label" for="isWarehouse">
                                            Warehouse
                                        </label>
                                        <select wire:model.defer="isWarehouse" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>

                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isJamKerja">
                                        <label class="form-check-label" for="isJamKerja">
                                            Jam Kerja
                                        </label>
                                        <select wire:model.defer="isJamKerja" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-4">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isMasterTable">
                                        <label class="form-check-label" for="isMasterTable">
                                            Master Table
                                        </label>
                                        <select wire:model.defer="isMasterTable" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>

                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isNippoSeitai">
                                        <label class="form-check-label" for="isNippoSeitai">
                                            Warehouse
                                        </label>
                                        <select wire:model.defer="isNippoSeitai" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div>

                                    {{-- <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="isJamKerja">
                                        <label class="form-check-label" for="isJamKerja">
                                            Jam Kerja
                                        </label>
                                        <select wire:model.defer="isJamKerja" class="ms-auto">
                                            <option value="1">Read</option>
                                            <option value="2">Write</option>
                                        </select>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <br>
            <button type="button" class="btn btn-warning" wire:click="cancel">
                <span wire:loading.remove wire:target="cancel">
                    <i class="ri-close-line"> </i> Close
                </span>
                <div wire:loading wire:target="cancel">
                    <span class="d-flex align-items-center">
                        <span class="spinner-border flex-shrink-0" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                        <span class="flex-grow-1 ms-1">
                            Loading...
                        </span>
                    </span>                    
                </div>
            </button>
            <button type="submit" class="btn btn-success">
                <span wire:loading.remove wire:target="save">
                    <i class="ri-save-3-line"></i> Save
                </span>
                <div wire:loading wire:target="save">
                    <span class="d-flex align-items-center">
                        <span class="spinner-border flex-shrink-0" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                        <span class="flex-grow-1 ms-1">
                            Loading...
                        </span>
                    </span>
                </div>
            </button>
        </form>
    </div>
</div>