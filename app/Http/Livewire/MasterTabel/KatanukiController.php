<?php

namespace App\Http\Livewire\MasterTabel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class KatanukiController extends Component
{
    use WithFileUploads;
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    public $katanukis;
    public $searchTerm;
    public $code;
    public $name;
    public $filename;
    public $photo;
    public $idUpdate;
    public $idDelete;

    #[Validate('image|max:10240')] // 10MB Max


    public function mount()
    {
        $this->katanukis = DB::table('mskatanuki')
            ->get(['id', 'code', 'name', 'filename', 'status', 'updated_by', 'updated_on']);
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
    }

    public function removePhoto()
    {
        $this->photo = null;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:mskatanuki,code',
            'name' => 'required',
            'photo' => 'required|image|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            // menyimpan file image ke storage
            $filename = $this->photo->getClientOriginalName();
            $this->filename = $this->photo->storeAs('katanuki', $filename, 'public');

            // menyimpan data ke database
            DB::table('mskatanuki')->insert([
                'code' => $this->code,
                'name' => strtoupper($this->name),
                'filename' => $this->filename,
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
            ->first(['code', 'name', 'filename']);
        $this->code = $katanuki->code;
        $this->name = $katanuki->name;
        $this->filename = $katanuki->filename;
        $this->idUpdate = $id;
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:mskatanuki,code,' . $this->idUpdate,
            'name' => 'required',
            'photo' => 'image|max:10240',
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
                $filename = $this->photo->getClientOriginalName();
                $this->filename = $this->photo->storeAs('katanuki', $filename, 'public');
            } else {
                $this->filename = $this->filename;
            }

            // menyimpan data ke database
            DB::table('mskatanuki')
                ->where('id', $this->idUpdate)
                ->update([
                    'code' => $this->code,
                    'name' => strtoupper($this->name),
                    'filename' => $this->filename,
                    'status' => $statusActive,
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
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            $statusInactive = 0;
            // $katanuki = DB::table('mskatanuki')->where('id', $this->idDelete)->first();
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

    public function search()
    {
        $this->resetPage();
        $this->render();
    }

    public function render()
    {
        $data = DB::table('mskatanuki')
            ->when(isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined", function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('code', 'ilike', "%" . $this->searchTerm . "%")
                        ->orWhere('name', 'ilike', "%" . $this->searchTerm . "%");
                });
            })
            ->paginate(10);

        return view('livewire.master-tabel.katanuki', [
            'data' => $data
        ])->extends('layouts.master');

    }
}
