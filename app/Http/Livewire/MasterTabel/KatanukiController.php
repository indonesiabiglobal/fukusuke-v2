<?php

namespace App\Http\Livewire\MasterTabel;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Session;

class KatanukiController extends Component
{
    use WithFileUploads;
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $katanukis;
    public $searchTerm;
    public $code;
    public $name;
    public $filename;
    public $photo;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;

    #[Session]
    public $sortingTable;

    #[Validate('image|max:10240')] // 10MB Max


    public function mount()
    {
        //     $this->katanukis = DB::table('mskatanuki')
        //         ->get(['id', 'code', 'name', 'filename', 'status', 'updated_by', 'updated_on']);
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
        $this->removePhoto();
    }

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function removePhoto()
    {
        $this->photo = null;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|max:10|unique:mskatanuki,code',
            'name' => 'required',
            // 'photo' => 'required|image|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            // menyimpan file image ke storage
            if (isset($this->photo)) {
                // Membuat nama file custom, misalnya menambahkan timestamp atau ID unik
                // $originalName = pathinfo($this->photo->getClientOriginalName(), PATHINFO_FILENAME);
                // $extension = $this->photo->getClientOriginalExtension();
                $filename = Str::random(20) . '.' . $this->photo->getClientOriginalExtension();

                // Menyimpan file dengan nama custom
                $this->filename = $this->photo->storeAs('katanuki', $filename, 'public');
            }

            // menyimpan data ke database
            DB::table('mskatanuki')->insert([
                'code' => $this->code,
                'name' => strtoupper($this->name),
                'filename' => $this->filename ?? null,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Katanuki created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Katanuki: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Katanuki: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $katanuki = DB::table('mskatanuki')
            ->where('id', $id)
            ->first(['code', 'name', 'filename', 'status']);
        $this->code = $katanuki->code;
        $this->name = $katanuki->name;
        $this->filename = $katanuki->filename;
        $this->idUpdate = $id;
        $this->status = $katanuki->status;
        $this->statusIsVisible = $katanuki->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|max:10|unique:mskatanuki,code,' . $this->idUpdate,
            'name' => 'required',
            'photo' => 'max:10240',
        ]);

        DB::beginTransaction();
        try {
            // cek apakah file image diubah atau tidak
            $this->filename = $this->photo ? 'katanuki/' . $this->photo->getClientOriginalName() : $this->filename;
            $mskatanuki = DB::table('mskatanuki')->where('id', $this->idUpdate)->first();
            if ($this->filename && $mskatanuki->filename != $this->filename) {
                // hapus file image lama
                $pathFile = $mskatanuki->filename;
                if ($pathFile) {
                    $pathFile = 'public/' . $pathFile;
                    if (file_exists(storage_path('app/' . $pathFile))) {
                        unlink(storage_path('app/' . $pathFile));
                    }
                }
            }

            $statusActive = 1;
            if ($this->photo) {
                // menyimpan file image ke storage
                $filename = Str::random(20) . '.' . $this->photo->getClientOriginalExtension();
                $this->filename = $this->photo->storeAs('katanuki', $filename, 'public');
            }

            // menyimpan data ke database
            DB::table('mskatanuki')
                ->where('id', $this->idUpdate)
                ->update([
                    'code' => $this->code,
                    'name' => strtoupper($this->name),
                    'filename' => $this->filename,
                    'status' => $this->status,
                    'updated_by' => auth()->user()->username,
                    'updated_on' => now(),
                ]);
            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Katanuki updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Katanuki: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Katanuki: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            $statusInactive = 0;
            $katanuki = DB::table('mskatanuki')->where('id', $this->idDelete)->first();
            // $pathFile = $katanuki->filename;
            // if ($pathFile) {
            //     $pathFile = 'public/' . $pathFile;
            //     if (file_exists(storage_path('app/' . $pathFile))) {
            //         unlink(storage_path('app/' . $pathFile));
            //     }
            // }
            DB::table('mskatanuki')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Katanuki deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Katanuki: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Katanuki: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $data = DB::table('mskatanuki')
            ->get();

        return view('livewire.master-tabel.katanuki', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
