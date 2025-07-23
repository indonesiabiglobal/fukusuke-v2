<?php

namespace App\Http\Livewire;

use App\Models\TdOrders;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
// use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
// use PowerComponents\LivewirePowerGrid\Footer;
// use PowerComponents\LivewirePowerGrid\Header;
// use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
// use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

use PowerComponents\LivewirePowerGrid\{Column, Footer, Header, PowerGrid, PowerGridComponent};

final class tdorder extends PowerGridComponent
{
    use WithExport;

    public bool $filtersOutside = false;
    public $tglMasuk;
    public $tglKeluar;

    public string $sortField = 'tdorder.id';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showToggleColumns(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource()
    {
        $query=TdOrders::query();

        if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
            $query->where('order_date', '>=', $this->tglMasuk);
        }

        if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
            $query->where('order_date', '<=', $this->tglKeluar);
        }

        return $query;
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        // $options = $this->categorySelectOptions();

        return PowerGrid::fields()
            ->add('id')
            ->add('po_no')
            ->add('product_id')
            ->add('product_code')
            ->add('order_unit')
            ->add('order_date')
            ->add('order_qty')
            ->add('id')
            ->add('po_no')
            ->add('product_id')
            ->add('product_code')
            ->add('order_unit')
            ->add('order_date')
            ->add('order_qty');
    }

    public function columns(): array
    {
        return [
            Column::action('Action'),

            Column::make('Po Number', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Nama Produk', 'product_code')
                ->sortable()
                ->searchable(),

            Column::make('Kode Produk', 'order_unit')
                ->sortable()
                ->searchable(),

            Column::make('Buyer', 'order_date')
                ->sortable()
                ->searchable(),

            Column::make('Quantity', 'po_no')
                ->sortable()
                ->searchable(),

            Column::make('Tgl Order', 'product_id')
                ->sortable()
                ->searchable(),

            Column::make('ETD', 'order_qty')
                ->sortable()
                ->searchable(),

            Column::make('Tgl Proses', 'po_no')
                ->sortable()
                ->searchable(),

            Column::make('No.', 'product_id')
                ->sortable()
                ->searchable()
                ->hidden(),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(TdOrders $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
