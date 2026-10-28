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

        $product->normalizeEmptyNumerics();

        foreach (MsProduct::NUMERIC_COLUMNS as $column) {
            $this->assertNull($product->getAttribute($column), "$column harus null");
        }
    }

    public function test_empty_or_null_count_columns_become_zero(): void
    {
        $product = new MsProduct();
        $product->setAttribute('case_gaiso_count', null);
        $product->setAttribute('case_inner_count', '');

        $product->normalizeEmptyNumerics();

        foreach (MsProduct::COUNT_COLUMNS as $column) {
            $this->assertSame(0, $product->getAttribute($column), "$column harus 0");
        }
    }

    public function test_box_count_is_never_zeroed_because_reports_divide_by_it(): void
    {
        $product = new MsProduct();
        $product->setAttribute('case_box_count', '');

        $product->normalizeEmptyNumerics();

        $this->assertNull($product->getAttribute('case_box_count'));
        $this->assertNotContains('case_box_count', MsProduct::COUNT_COLUMNS);
    }

    public function test_count_columns_not_loaded_are_left_alone(): void
    {
        $product = new MsProduct();
        $product->setAttribute('name', 'X');

        $product->normalizeEmptyNumerics();

        $this->assertArrayNotHasKey('case_inner_count', $product->getAttributes());
    }

    public function test_real_values_and_text_columns_are_untouched(): void
    {
        $product = new MsProduct();
        $product->setAttribute('case_inner_count', '12');
        $product->setAttribute('extracted_dimension_a', 0);
        $product->setAttribute('kodehagata', '');
        $product->setAttribute('unit_weight', '0');
        $product->setAttribute('ketebalan', null);

        $product->normalizeEmptyNumerics();

        $this->assertSame('12', $product->getAttribute('case_inner_count'));
        $this->assertSame(0, $product->getAttribute('extracted_dimension_a'));
        $this->assertSame('', $product->getAttribute('kodehagata'));
        $this->assertSame('0', $product->getAttribute('unit_weight'));
        $this->assertNull($product->getAttribute('ketebalan'));
    }

    public function test_saving_event_applies_the_normalisation(): void
    {
        $product = new MsProduct();
        $product->setAttribute('extracted_dimension_a', '');
        $product->setAttribute('case_inner_count', '');

        MsProduct::getEventDispatcher()->dispatch('eloquent.saving: ' . MsProduct::class, $product);

        $this->assertNull($product->getAttribute('extracted_dimension_a'));
        $this->assertSame(0, $product->getAttribute('case_inner_count'));
    }
}
