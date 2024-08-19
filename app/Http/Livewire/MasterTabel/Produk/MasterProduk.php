<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use App\Models\MsProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class MasterProduk extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $searchTerm;
    public $product_type_id;
    public $idUpdate;
    public $idDelete;
    public $paginate = 10;

    public function search()
    {
        $this->resetPage();
        $this->render();
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
            MsProduct::where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now()
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer deleted successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the buyer: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $data = DB::table('msproduct as msp')
            ->leftJoin('msproduct_type as mspt', 'msp.product_type_id', '=', 'mspt.id')
            ->leftJoin('mskatanuki as msk', 'msp.katanuki_id', '=', 'msk.id')
            ->select(
                'msp.id',
                'msp.code as product_code',
                'msp.name as product_name',
                'msp.product_type_code',
                'mspt.name as product_type_name',
                DB::raw('msp.ketebalan || \'x\' || msp.diameterlipat || \'x\' || msp.productlength as dimensi'),
                'msp.unit_weight',
                'msk.code as katanuki_code',
                'msp.number_of_color',
                'msp.back_color_number',
                'msp.status',
                'msp.updated_by',
                'msp.updated_on'
            )
            ->when(isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined", function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('msp.name', 'ilike', "%" . $this->searchTerm . "%")
                        ->orWhere('msp.product_type_code', 'ilike', "%" . $this->searchTerm . "%")
                        ->orWhere('msp.code', 'ilike', "%" . $this->searchTerm . "%");
                });
            })
            ->when(isset($this->product_type_id) && $this->product_type_id != "" && $this->product_type_id != "undefined", function ($query) {
                $query->where('msp.product_type_id', $this->product_type_id);
            })
            ->when($this->paginate != 'all', function ($query) {
                return $query->paginate($this->paginate);
            }, function ($query) {
                $count = $query->count();
                return $query->paginate($count);
            });

        return view('livewire.master-tabel.produk.master-produk', [
            'data' => $data
        ])->extends('layouts.master');
    }
}
