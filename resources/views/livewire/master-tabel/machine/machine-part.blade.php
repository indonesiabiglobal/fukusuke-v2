<div>
    <div class="row filter-section">
        <div class="col-lg-12 mt-2">
            <div class="row">

                <div class="col-12 col-lg-6">
                    {{-- Header Title --}}
                    <h4 class="card-title mb-4 fs-22">Machine Part</h4>
                    {{-- Button Add Machine Part --}}
                    <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                        <i class="ri-add-line"> </i> Add
                    </button>

                    {{-- modal add machine part --}}
                    <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-addLabel">Add Machine Part</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form wire:submit.prevent="store">
                                        <div class="row g-3">
                                            {{-- Code --}}
                                            <div class="col-xxl-12">
                                                <div>
                                                    <label for="code" class="form-label">Code</label>
                                                    <input type="text"
                                                        class="form-control @error('code') is-invalid @enderror"
                                                        id="code" wire:model.defer="code" maxlength="10"
                                                        placeholder="Kode/Code" oninput="this.value = this.value.toUpperCase()">
                                                    @error('code')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- Part Machine --}}
                                            <div class="col-xxl-12">
                                                <div>
                                                    <label for="part_machine" class="form-label">Part Machine</label>
                                                    <input type="text"
                                                        class="form-control @error('part_machine') is-invalid @enderror"
                                                        id="part_machine" wire:model.defer="part_machine" placeholder="Nama Bagian Mesin">
                                                    @error('part_machine')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- Departemen --}}
                                            <div class="col-xxl-12">
                                                <div class="row">
                                                    <label class="form-label">Departemen</label>
                                                    <select data-choices data-choices-sorting="true"
                                                        class="form-select @error('department_id') is-invalid @enderror"
                                                        wire:model="department_id">
                                                        <option value="" selected>Silahkan Pilih</option>
                                                        @foreach (\App\Models\MsDepartment::select('id','name','code')->division()->active()->get() as $department)
                                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('department_id')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- button --}}
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <button id="btnCreate" type="submit" class="btn btn-success w-lg" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="store">
                                                            <i class="ri-save-3-line"></i> Save
                                                        </span>
                                                        <div wire:loading wire:target="store">
                                                            <span class="d-flex align-items-center">
                                                                <span class="spinner-border flex-shrink-0" role="status">
                                                                    <span class="visually-hidden">Loading...</span>
                                                                </span>
                                                                <span class="flex-grow-1 ms-1">Loading...</span>
                                                            </span>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div><!--end col-->
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end modal add --}}

                    {{-- modal edit machine part --}}
                    <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel" aria-modal="true" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-editLabel">Edit Machine Part</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form wire:submit.prevent="update">
                                        <div class="row g-3">
                                            {{-- Code --}}
                                            <div class="col-xxl-12">
                                                <div>
                                                    <label for="code_edit" class="form-label">Code</label>
                                                    <input type="text"
                                                        class="form-control @error('code') is-invalid @enderror"
                                                        id="code_edit" wire:model.defer="code" maxlength="10"
                                                        placeholder="Kode/Code" oninput="this.value = this.value.toUpperCase()">
                                                    @error('code')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- Part Machine --}}
                                            <div class="col-xxl-12">
                                                <div>
                                                    <label for="part_machine_edit" class="form-label">Part Machine</label>
                                                    <input type="text"
                                                        class="form-control @error('part_machine') is-invalid @enderror"
                                                        id="part_machine_edit" wire:model.defer="part_machine" placeholder="Nama Bagian Mesin">
                                                    @error('part_machine')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- Departemen --}}
                                            <div class="col-xxl-12">
                                                <div class="row">
                                                    <label class="form-label">Departemen</label>
                                                    <select data-choices data-choices-sorting="true"
                                                        class="form-select @error('department_id') is-invalid @enderror"
                                                        wire:model="department_id">
                                                        @foreach (\App\Models\MsDepartment::select('id','name')->division()->active()->get() as $department)
                                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('department_id')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- Status (muncul hanya jika non-aktif sebelumnya) --}}
                                            <div x-data="{ isVisible: $wire.entangle('statusIsVisible') }">
                                                <div class="col-xxl-12" x-show="isVisible">
                                                    <div wire:ignore>
                                                        <label class="form-label">Status</label>
                                                        <select class="form-select" wire:model="status">
                                                            <option value="0" {{ $status == '0' ? 'selected' : '' }}>Inactive</option>
                                                            <option value="1" {{ $status == '1' ? 'selected' : '' }}>Active</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- button --}}
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <button id="btnUpdate" type="submit" class="btn btn-success w-lg" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="update">
                                                            <i class="ri-save-3-line"></i> Update
                                                        </span>
                                                        <div wire:loading wire:target="update">
                                                            <span class="d-flex align-items-center">
                                                                <span class="spinner-border flex-shrink-0" role="status">
                                                                    <span class="visually-hidden">Loading...</span>
                                                                </span>
                                                                <span class="flex-grow-1 ms-1">Loading...</span>
                                                            </span>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div><!--end col-->
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end modal edit --}}

                    {{-- start modal delete machine part --}}
                    <div id="modal-delete" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal-delete"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mt-2 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                            <h4>Are you sure ?</h4>
                                            <p class="text-muted mx-4 mb-0">Are you sure you want to remove this machine part?</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                                        <button wire:click="destroy" id="btnCreate" type="button" class="btn w-sm btn-danger" id="remove-item" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="destroy">
                                                <i class="ri-save-3-line"></i> Yes, Delete It!
                                            </span>
                                            <div wire:loading wire:target="destroy">
                                                <span class="d-flex align-items-center">
                                                    <span class="spinner-border flex-shrink-0" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </span>
                                                    <span class="flex-grow-1 ms-1">Loading...</span>
                                                </span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end modal delete --}}
                </div>

                {{-- toggle column table --}}
                <div class="col-12 col-lg-6">
                    <div class="col text-end dropdown">
                        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14 mt-2">
                            <i class="ri-grid-fill"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <label style="cursor: pointer;">
                                    <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="1" checked>
                                    Part Machine
                                </label>
                            </li>
                            <li>
                                <label style="cursor: pointer;">
                                    <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2" checked>
                                    Code
                                </label>
                            </li>
                            <li>
                                <label style="cursor: pointer;">
                                    <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3" checked>
                                    Departemen
                                </label>
                            </li>
                            <li>
                                <label style="cursor: pointer;">
                                    <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4" checked>
                                    Status
                                </label>
                            </li>
                            <li>
                                <label style="cursor: pointer;">
                                    <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5" checked>
                                    Updated By
                                </label>
                            </li>
                            <li>
                                <label style="cursor: pointer;">
                                    <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6" checked>
                                    Updated
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="machineTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Part Machine</th>
                    <th>Code</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th>Updated By</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <button type="button" class="btn fs-15 p-1 bg-primary rounded btn-edit"
                                data-edit-id="{{ $item->id }}" wire:click="edit({{ $item->id }})">
                                <i class="ri-edit-box-line text-white"></i>
                            </button>
                            <button {{ $item->status == 0 ? 'hidden' : '' }} type="button"
                                class="btn fs-15 p-1 bg-danger rounded modal-delete btn-delete"
                                data-delete-id="{{ $item->id }}" wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->part_machine }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->departmentname }}</td>
                        <td>
                            {!! $item->status == 1
                                ? '<span class="badge text-success bg-success-subtle">Active</span>'
                                : '<span class="badge text-bg-danger">Non Active</span>' !!}
                        </td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->updated_on)->format('d-M-Y H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">No machine part found for your search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- {{ $data->links() }} --}}
    </div>

    <style>
        #machineTable.table>:not(caption)>*>* {
            font-size: 13px !important;
            padding: 4px 2px 4px 4px;
            color: var(--tb-table-color-state, var(--tb-table-color-type, var(--tb-table-color)));
            background-color: var(--tb-table-bg);
            border-bottom-width: var(--tb-border-width);
            box-shadow: inset 0 0 0 9999px var(--tb-table-bg-state, var(--tb-table-bg-type, var(--tb-table-accent-bg)));
        }
    </style>
</div>

@script
<script>
    $wire.on('showModalCreate', () => { $('#modal-add').modal('show'); });
    $wire.on('closeModalCreate', () => { $('#modal-add').modal('hide'); });

    $wire.on('showModalUpdate', () => { $('#modal-edit').modal('show'); });
    $wire.on('closeModalUpdate', () => { $('#modal-edit').modal('hide'); });

    $wire.on('showModalDelete', () => { $('#modal-delete').modal('show'); });
    $wire.on('closeModalDelete', () => { $('#modal-delete').modal('hide'); });

    $wire.on('initDataTable', () => { initDataTable('machineTable'); });

    function calculateTableHeight() {
        const totalHeight = window.innerHeight;
        const filterSectionTop = document.querySelector('.filter-section')?.getBoundingClientRect().top || 0;
        const offsetTop = document.querySelector('#machineTable')?.getBoundingClientRect().top || 0;
        const paddingTop = document.querySelector('.navbar-header')?.getBoundingClientRect().top || 0;
        const availableHeight = totalHeight - offsetTop - filterSectionTop - paddingTop;
        return availableHeight;
    }

    function initDataTable(id) {
        const savedOrder = $wire.get('sortingTable');
        let defaultOrder = [[1, "asc"]]; // Part Machine
        if (savedOrder) defaultOrder = savedOrder;

        if ($.fn.dataTable.isDataTable('#' + id)) {
            let table = $('#' + id).DataTable();
            table.clear();
            table.destroy();
        }

        setTimeout(() => {
            let table = $('#' + id).DataTable({
                pageLength: 10,
                searching: true,
                responsive: true,
                scrollX: true,
                order: defaultOrder,
                scrollY: calculateTableHeight() + 'px',
                scrollCollapse: true,
                language: {
                    emptyTable: `
                        <div class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                        </div>
                    `
                },
            });

            table.on('order.dt', function() {
                let order = table.order();
                if (order.length == 0 && defaultOrder.length > 0) order = defaultOrder;
                $wire.call('updateSortingTable', order);
            });

            // actions
            $('.btn-delete').on('click', function() {
                let id = $(this).attr('data-delete-id');
                $wire.dispatch('delete', { id });
            });
            $('.btn-edit').on('click', function() {
                let id = $(this).attr('data-edit-id');
                $wire.dispatch('edit', { id });
            });

            // default column visibility (skip Action col idx 0)
            $('.toggle-column').each(function() {
                let column = table.column($(this).attr('data-column'));
                column.visible($(this).is(':checked'));
            });

            $('.toggle-column').off('change').on('change', function() {
                let column = table.column($(this).attr('data-column'));
                column.visible(!column.visible());
            });

            setTimeout(() => {
                table.on('search.dt', function() {
                    var value = $('.dt-search input').val();
                    debounce(function() {
                        window.history.pushState(null, null, `?search=${value}`);
                    }, 300)();
                });

                let querySearch = new URLSearchParams(window.location.search).get('search') || '';
                table.search(querySearch).draw();
            }, 10);
        }, 500);
    }
</script>
@endscript
