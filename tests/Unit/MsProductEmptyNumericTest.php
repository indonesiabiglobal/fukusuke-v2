<?php

namespace Tests\Unit;

use App\Models\MsProduct;
use Tests\TestCase;

/**
 * Regresi: EditProduk gagal dengan SQLSTATE 22P02 ("invalid input syntax for type
 * double precision: \"\"") karena input angka yang dikosongkan dikirim Livewire sebagai "".
 */
class MsProductEmptyNumericTest extends TestCase
{
    public function test_empty_string_in_numeric_columns_becomes_null(): void
    {
        $product = new MsProduct();
        foreach (MsProduct::NUMERIC_COLUMNS as $column) {
            $product->setAttribute($column, '');
        }

        $product->nullifyEmptyNumerics();

        foreach (MsProduct::NUMERIC_COLUMNS as $column) {
            $this->assertNull($product->getAttribute($column), "$column harus null");
        }
    }

    public function test_real_values_and_text_columns_are_untouched(): void
    {
        $product = new MsProduct();
        $product->setAttribute('case_inner_count', '12');
        $product->setAttribute('extracted_dimension_a', 0);
        $product->setAttribute('kodehagata', '');
        $product->setAttribute('unit_weight', '0');
        $product->setAttribute('ketebalan', null);

        $product->nullifyEmptyNumerics();

        $this->assertSame('0', $product->getAttribute('unit_weight'));
        $this->assertNull($product->getAttribute('ketebalan'));

        $this->assertSame('12', $product->getAttribute('case_inner_count'));
        $this->assertSame(0, $product->getAttribute('extracted_dimension_a'));
        $this->assertSame('', $product->getAttribute('kodehagata'));
    }

    public function test_saving_event_applies_the_normalisation(): void
    {
        $product = new MsProduct();
        $product->setAttribute('case_inner_count', '');

        MsProduct::getEventDispatcher()->dispatch('eloquent.saving: ' . MsProduct::class, $product);

        $this->assertNull($product->getAttribute('case_inner_count'));
    }
}
