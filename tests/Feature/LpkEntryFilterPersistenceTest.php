<?php

namespace Tests\Feature;

use App\Http\Livewire\LpkEntryController;
use Livewire\Livewire;
use Tests\TestCase;

class LpkEntryFilterPersistenceTest extends TestCase
{
    private function setFilters(): void
    {
        Livewire::test(LpkEntryController::class)
            ->set('tglMasuk', '2026-01-02')
            ->set('tglKeluar', '2026-01-05')
            ->set('transaksi', 2)
            ->set('lpk_no', '000123-456')
            ->set('searchTerm', 'abc');
    }

    public function test_filters_survive_round_trip_via_edit_page(): void
    {
        $this->setFilters();

        Livewire::withHeaders(['referer' => url('/edit-lpk?orderId=1')])
            ->test(LpkEntryController::class)
            ->assertSet('tglMasuk', '2026-01-02')
            ->assertSet('tglKeluar', '2026-01-05')
            ->assertSet('transaksi', 2)
            ->assertSet('lpk_no', '000123-456')
            ->assertSet('searchTerm', 'abc');
    }

    public function test_filters_reset_when_coming_from_other_menu(): void
    {
        $this->setFilters();

        Livewire::withHeaders(['referer' => url('/dashboard')])
            ->test(LpkEntryController::class)
            ->assertSet('lpk_no', null)
            ->assertSet('searchTerm', null);
    }
}
